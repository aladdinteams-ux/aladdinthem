<?php
/**
 * Private storage for customer documents attached to inquiries.
 *
 * - Files never enter the media library and never get a public URL.
 * - Stored under an unguessable directory protected with .htaccess / web.config
 *   (Apache, LiteSpeed, IIS). On Nginx the path cannot be listed or guessed; for
 *   full protection define LARIJANI_CORE_PRIVATE_DIR outside the web root
 *   (see the status page for a live accessibility test).
 * - Every file is checked by extension allow-list, size, magic bytes and, for
 *   ZIP archives, by inspecting their entries.
 * - Downloads go through admin-post.php with a nonce + capability check.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allowed extensions and their limits.
 *
 * @return array ext => array( mime, max bytes )
 */
function larijani_core_upload_rules() {
	return apply_filters(
		'larijani_core_upload_rules',
		array(
			'jpg'  => array( 'image/jpeg', 10 * MB_IN_BYTES ),
			'jpeg' => array( 'image/jpeg', 10 * MB_IN_BYTES ),
			'png'  => array( 'image/png', 10 * MB_IN_BYTES ),
			'webp' => array( 'image/webp', 10 * MB_IN_BYTES ),
			'pdf'  => array( 'application/pdf', 20 * MB_IN_BYTES ),
			'dwg'  => array( 'application/acad', 30 * MB_IN_BYTES ),
			'zip'  => array( 'application/zip', 30 * MB_IN_BYTES ),
		)
	);
}

/**
 * Upload limits shared by the form handler.
 *
 * @return array
 */
function larijani_core_upload_limits() {
	return apply_filters(
		'larijani_core_upload_limits',
		array(
			'max_files'        => 5,
			'max_total'        => 50 * MB_IN_BYTES,
			'zip_max_entries'  => 500,
			'zip_max_unpacked' => 200 * MB_IN_BYTES,
		)
	);
}

/**
 * Absolute path of the private storage root (created and protected on demand).
 *
 * @return string Empty string when it could not be created.
 */
function larijani_core_private_dir() {
	if ( defined( 'LARIJANI_CORE_PRIVATE_DIR' ) && LARIJANI_CORE_PRIVATE_DIR ) {
		$dir = untrailingslashit( LARIJANI_CORE_PRIVATE_DIR );
	} else {
		$name = get_option( 'larijani_core_private_dirname' );
		if ( ! $name || ! preg_match( '/^larijani-private-[a-z0-9]{24}$/', $name ) ) {
			$name = 'larijani-private-' . strtolower( wp_generate_password( 24, false, false ) );
			update_option( 'larijani_core_private_dirname', $name, false );
		}
		$uploads = wp_upload_dir( null, false );
		if ( ! empty( $uploads['error'] ) ) {
			return '';
		}
		$dir = untrailingslashit( $uploads['basedir'] ) . '/' . $name;
	}
	if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
		return '';
	}
	larijani_core_protect_dir( $dir );
	return $dir;
}

/**
 * Write deny rules, a silent index and disable script execution.
 *
 * @param string $dir Directory.
 */
function larijani_core_protect_dir( $dir ) {
	$files = array(
		'.htaccess'  => "# Larijani Stone Core: private customer files.\nOptions -Indexes\n<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tOrder deny,allow\n\tDeny from all\n</IfModule>\n<IfModule mod_php.c>\n\tphp_flag engine off\n</IfModule>\n<IfModule mod_php7.c>\n\tphp_flag engine off\n</IfModule>\nRemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8 .phar\nRemoveType .php .phtml .php3 .php4 .php5 .php7 .php8 .phar\n",
		'index.php'  => "<?php\n// Silence is golden.\n",
		'index.html' => '',
		'web.config' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<configuration>\n\t<system.webServer>\n\t\t<authorization>\n\t\t\t<deny users=\"*\" />\n\t\t</authorization>\n\t\t<handlers accessPolicy=\"Read\" />\n\t</system.webServer>\n</configuration>\n",
	);
	foreach ( $files as $name => $content ) {
		$path = $dir . '/' . $name;
		if ( ! file_exists( $path ) ) {
			file_put_contents( $path, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- tiny static files in our own directory.
		}
	}
}

/**
 * Normalise $_FILES['ls_files'] into a list.
 *
 * @param array $files Raw $_FILES entry.
 * @return array
 */
function larijani_core_normalize_files( $files ) {
	$out = array();
	if ( empty( $files['name'] ) ) {
		return $out;
	}
	if ( ! is_array( $files['name'] ) ) {
		foreach ( array( 'name', 'type', 'tmp_name', 'error', 'size' ) as $k ) {
			$files[ $k ] = array( isset( $files[ $k ] ) ? $files[ $k ] : '' );
		}
	}
	foreach ( $files['name'] as $i => $name ) {
		if ( UPLOAD_ERR_NO_FILE === (int) $files['error'][ $i ] || '' === (string) $name ) {
			continue;
		}
		$out[] = array(
			'name'     => (string) $name,
			'tmp_name' => (string) $files['tmp_name'][ $i ],
			'error'    => (int) $files['error'][ $i ],
			'size'     => (int) $files['size'][ $i ],
		);
	}
	return $out;
}

/**
 * Read the first bytes of a file.
 *
 * @param string $path Path.
 * @param int    $len Bytes.
 * @return string
 */
function larijani_core_file_head( $path, $len = 16 ) {
	$fh = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	if ( ! $fh ) {
		return '';
	}
	$head = (string) fread( $fh, $len ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
	fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	return $head;
}

/**
 * Verify that the content matches the claimed extension.
 *
 * @param string $path Temp file.
 * @param string $ext Extension.
 * @return true|string True or an error code.
 */
function larijani_core_check_content( $path, $ext ) {
	$head = larijani_core_file_head( $path, 16 );
	switch ( $ext ) {
		case 'jpg':
		case 'jpeg':
			$ok = 0 === strncmp( $head, "\xFF\xD8\xFF", 3 );
			break;
		case 'png':
			$ok = 0 === strncmp( $head, "\x89PNG\r\n\x1A\n", 8 );
			break;
		case 'webp':
			$ok = 0 === strncmp( $head, 'RIFF', 4 ) && 'WEBP' === substr( $head, 8, 4 );
			break;
		case 'pdf':
			$ok = 0 === strncmp( $head, '%PDF-', 5 );
			break;
		case 'dwg':
			$ok = (bool) preg_match( '/^AC10\d\d/', $head ) || 0 === strncmp( $head, 'AC1.', 4 ) || 0 === strncmp( $head, 'MC0.0', 5 );
			break;
		case 'zip':
			$ok = 0 === strncmp( $head, "PK\x03\x04", 4 ) || 0 === strncmp( $head, "PK\x05\x06", 4 );
			break;
		default:
			$ok = (bool) apply_filters( 'larijani_core_check_content', false, $path, $ext );
	}
	if ( ! $ok ) {
		return 'bad_content';
	}
	if ( in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp' ), true ) ) {
		$info = function_exists( 'getimagesize' ) ? @getimagesize( $path ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- invalid images are expected input.
		if ( ! $info || empty( $info[0] ) || empty( $info[1] ) ) {
			return 'bad_content';
		}
	}
	// Reject files whose detected type is script/markup regardless of extension.
	if ( function_exists( 'finfo_open' ) ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		if ( $finfo ) {
			$mime = (string) finfo_file( $finfo, $path );
			finfo_close( $finfo );
			if ( preg_match( '#^(text/(x-php|x-shellscript|html|javascript)|application/(x-httpd-php|x-php|javascript|x-executable|x-dosexec|x-sharedlib))#', $mime ) ) {
				return 'bad_content';
			}
		}
	}
	if ( 'zip' === $ext ) {
		return larijani_core_check_zip( $path );
	}
	return true;
}

/**
 * Inspect a ZIP archive without extracting it.
 *
 * @param string $path Temp file.
 * @return true|string
 */
function larijani_core_check_zip( $path ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		// Cannot inspect: refuse unless the site owner opts in.
		return apply_filters( 'larijani_core_allow_uninspected_zip', false ) ? true : 'zip_unsupported';
	}
	$limits = larijani_core_upload_limits();
	$zip    = new ZipArchive();
	if ( true !== $zip->open( $path ) ) {
		return 'bad_content';
	}
	$count = $zip->numFiles; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	if ( $count < 1 || $count > $limits['zip_max_entries'] ) {
		$zip->close();
		return 'zip_rejected';
	}
	$blocked  = apply_filters( 'larijani_core_zip_blocked_ext', array( 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'cgi', 'pl', 'py', 'asp', 'aspx', 'jsp', 'sh', 'bash', 'exe', 'msi', 'dll', 'com', 'scr', 'bat', 'cmd', 'ps1', 'vbs', 'vbe', 'js', 'jse', 'wsf', 'hta', 'jar', 'htaccess', 'htm', 'html', 'svg', 'lnk', 'reg' ) );
	$unpacked = 0;
	for ( $i = 0; $i < $count; $i++ ) {
		$stat = $zip->statIndex( $i );
		if ( ! $stat ) {
			$zip->close();
			return 'zip_rejected';
		}
		$name      = str_replace( '\\', '/', (string) $stat['name'] );
		$unpacked += (int) $stat['size'];
		$base      = strtolower( basename( $name ) );
		$parts     = explode( '.', $base );
		$is_bad    = false;
		foreach ( array_slice( $parts, 1 ) as $part ) {
			if ( in_array( $part, $blocked, true ) ) {
				$is_bad = true;
			}
		}
		if ( $is_bad || '.htaccess' === $base || '.user.ini' === $base || 0 === strpos( $name, '/' ) || false !== strpos( $name, '../' ) || preg_match( '/^[a-z]:/i', $name ) ) {
			$zip->close();
			return 'zip_rejected';
		}
		// Zip bomb heuristic: extreme compression ratio on a sizeable entry.
		if ( (int) $stat['size'] > 10 * MB_IN_BYTES && (int) $stat['comp_size'] > 0 && ( (int) $stat['size'] / (int) $stat['comp_size'] ) > 100 ) {
			$zip->close();
			return 'zip_rejected';
		}
	}
	$zip->close();
	return $unpacked > $limits['zip_max_unpacked'] ? 'zip_rejected' : true;
}

/**
 * Validate a list of uploaded files without storing them.
 *
 * @param array $files Normalised files.
 * @return true|string
 */
function larijani_core_validate_uploads( $files ) {
	$rules  = larijani_core_upload_rules();
	$limits = larijani_core_upload_limits();
	if ( count( $files ) > $limits['max_files'] ) {
		return 'too_many_files';
	}
	$total = 0;
	foreach ( $files as $file ) {
		if ( UPLOAD_ERR_INI_SIZE === $file['error'] || UPLOAD_ERR_FORM_SIZE === $file['error'] ) {
			return 'file_too_large';
		}
		if ( UPLOAD_ERR_OK !== $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return 'upload_failed';
		}
		$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		if ( ! isset( $rules[ $ext ] ) ) {
			return 'bad_type';
		}
		$size = (int) filesize( $file['tmp_name'] );
		if ( $size < 1 || $size > $rules[ $ext ][1] ) {
			return 'file_too_large';
		}
		$total += $size;
		$check  = larijani_core_check_content( $file['tmp_name'], $ext );
		if ( true !== $check ) {
			return $check;
		}
	}
	return $total > $limits['max_total'] ? 'file_too_large' : true;
}

/**
 * Move validated uploads into private storage.
 *
 * @param array $files Normalised, validated files.
 * @param int   $lead_id Lead id.
 * @return array|false List of stored file records or false on failure.
 */
function larijani_core_store_uploads( $files, $lead_id ) {
	$stored = array();
	foreach ( $files as $file ) {
		$ext    = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		$record = larijani_core_store_file( $file['tmp_name'], $file['name'], $ext, $lead_id, true );
		if ( ! $record ) {
			larijani_core_delete_files( $stored );
			return false;
		}
		$stored[] = $record;
	}
	return $stored;
}

/**
 * Store one file privately.
 *
 * @param string $source Source path.
 * @param string $orig_name Original file name (display only).
 * @param string $ext Validated extension.
 * @param int    $lead_id Lead id.
 * @param bool   $is_upload Whether $source is a PHP upload (move) or a local file (copy).
 * @return array|false
 */
function larijani_core_store_file( $source, $orig_name, $ext, $lead_id, $is_upload ) {
	$root = larijani_core_private_dir();
	if ( ! $root ) {
		return false;
	}
	$rel = 'leads/' . gmdate( 'Y/m' ) . '/' . (int) $lead_id;
	$dir = $root . '/' . $rel;
	if ( ! wp_mkdir_p( $dir ) ) {
		return false;
	}
	larijani_core_protect_dir( $root . '/leads' );
	$id     = strtolower( wp_generate_password( 32, false, false ) );
	$target = $dir . '/' . $id . '.' . $ext;
	$ok     = $is_upload ? move_uploaded_file( $source, $target ) : copy( $source, $target );
	if ( ! $ok ) {
		return false;
	}
	chmod( $target, 0640 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
	$rules = larijani_core_upload_rules();
	$name  = sanitize_file_name( wp_basename( $orig_name ) );
	return array(
		'id'     => $id,
		'name'   => $name ? $name : $id . '.' . $ext,
		'path'   => $rel . '/' . $id . '.' . $ext,
		'size'   => (int) filesize( $target ),
		'mime'   => isset( $rules[ $ext ] ) ? $rules[ $ext ][0] : 'application/octet-stream',
		'sha256' => hash_file( 'sha256', $target ),
	);
}

/**
 * Delete stored files (rollback or privacy erasure).
 *
 * @param array $records File records.
 */
function larijani_core_delete_files( $records ) {
	foreach ( (array) $records as $rec ) {
		$path = larijani_core_file_path( $rec );
		if ( $path ) {
			wp_delete_file( $path );
		}
	}
}

/**
 * Resolve a record to an absolute path inside the private root.
 *
 * @param array $rec Record.
 * @return string Empty when missing or outside the root.
 */
function larijani_core_file_path( $rec ) {
	if ( empty( $rec['path'] ) || ! is_string( $rec['path'] ) ) {
		return '';
	}
	$root = larijani_core_private_dir();
	$real = realpath( $root . '/' . $rec['path'] );
	$base = realpath( $root );
	if ( ! $real || ! $base || 0 !== strpos( $real, $base . DIRECTORY_SEPARATOR ) || ! is_file( $real ) ) {
		return '';
	}
	return $real;
}

/**
 * Private files of a lead.
 *
 * @param int $lead_id Lead id.
 * @return array
 */
function larijani_core_lead_files( $lead_id ) {
	$files = get_post_meta( $lead_id, '_ls_lead_private_files', true );
	return is_array( $files ) ? $files : array();
}

/**
 * Signed admin download URL for one private file.
 *
 * @param int    $lead_id Lead id.
 * @param string $file_id File id.
 * @return string
 */
function larijani_core_file_url( $lead_id, $file_id ) {
	return wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'larijani_lead_file',
				'lead'   => (int) $lead_id,
				'file'   => $file_id,
			),
			admin_url( 'admin-post.php' )
		),
		'larijani_lead_file_' . (int) $lead_id . '_' . $file_id
	);
}

/**
 * Stream a private file to an authorised user.
 */
function larijani_core_download_file() {
	$lead_id = isset( $_GET['lead'] ) ? absint( $_GET['lead'] ) : 0;
	$file_id = isset( $_GET['file'] ) ? sanitize_key( wp_unslash( $_GET['file'] ) ) : '';
	check_admin_referer( 'larijani_lead_file_' . $lead_id . '_' . $file_id );
	if ( ! $lead_id || 'ls_lead' !== get_post_type( $lead_id ) || ! current_user_can( 'edit_post', $lead_id ) ) {
		wp_die( esc_html__( 'شما اجازه دسترسی به این فایل را ندارید.', 'larijani-stone-core' ), '', array( 'response' => 403 ) );
	}
	$rec = null;
	foreach ( larijani_core_lead_files( $lead_id ) as $item ) {
		if ( isset( $item['id'] ) && hash_equals( (string) $item['id'], $file_id ) ) {
			$rec = $item;
		}
	}
	$path = $rec ? larijani_core_file_path( $rec ) : '';
	if ( ! $path ) {
		wp_die( esc_html__( 'فایل پیدا نشد.', 'larijani-stone-core' ), '', array( 'response' => 404 ) );
	}
	$name = str_replace( array( '"', "\r", "\n" ), '', $rec['name'] );
	nocache_headers();
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . rawurlencode( $name ) . '"; filename*=UTF-8\'\'' . rawurlencode( $name ) );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Content-Security-Policy: default-src \'none\'; sandbox' );
	header( 'Cache-Control: private, no-store' );
	while ( ob_get_level() ) {
		ob_end_clean();
	}
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	exit;
}
add_action( 'admin_post_larijani_lead_file', 'larijani_core_download_file' );

/**
 * Remove private files when a lead is permanently deleted.
 *
 * @param int $post_id Post id.
 */
function larijani_core_delete_lead_files( $post_id ) {
	if ( 'ls_lead' === get_post_type( $post_id ) ) {
		larijani_core_delete_files( larijani_core_lead_files( $post_id ) );
	}
}
add_action( 'before_delete_post', 'larijani_core_delete_lead_files' );

/**
 * Live test: is a probe file in private storage reachable over HTTP?
 *
 * @return string 'protected' | 'exposed' | 'unknown'
 */
function larijani_core_private_dir_status() {
	$root = larijani_core_private_dir();
	if ( ! $root ) {
		return 'unknown';
	}
	$uploads = wp_upload_dir( null, false );
	$base    = untrailingslashit( $uploads['basedir'] );
	if ( 0 !== strpos( $root, $base . '/' ) ) {
		return 'protected'; // Outside the uploads URL space.
	}
	$probe = $root . '/probe.txt';
	file_put_contents( $probe, 'larijani-probe' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	$url  = untrailingslashit( $uploads['baseurl'] ) . substr( $probe, strlen( $base ) );
	$resp = wp_remote_get(
		$url,
		array(
			'timeout'     => 8,
			'sslverify'   => false,
			'redirection' => 0,
		)
	);
	wp_delete_file( $probe );
	if ( is_wp_error( $resp ) ) {
		return 'unknown';
	}
	$code = (int) wp_remote_retrieve_response_code( $resp );
	return ( 200 === $code && false !== strpos( wp_remote_retrieve_body( $resp ), 'larijani-probe' ) ) ? 'exposed' : 'protected';
}
