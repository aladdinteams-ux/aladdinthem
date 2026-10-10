<?php
// Minimal POT generator for the 'larijani-stone' text domain (gettext calls with literal strings).
$root = $argv[1]; $out = $argv[2]; $want = $argv[3] ?? 'larijani-stone'; $proj = $argv[4] ?? 'Larijani Stone';
$fns = array( '__' => 1, '_e' => 1, 'esc_html__' => 1, 'esc_html_e' => 1, 'esc_attr__' => 1, 'esc_attr_e' => 1, '_x' => 2, '_n' => 3, 'esc_html_x' => 2, 'esc_attr_x' => 2 );
$entries = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root ) );
foreach ( $it as $f ) {
	$path = $f->getPathname();
	if ( substr( $path, -4 ) !== '.php' || preg_match( '#/(node_modules|build|release|child-theme|plugins|\.git)/#', substr( $path, strlen( $root ) ) ) ) { continue; }
	$toks = token_get_all( file_get_contents( $path ) );
	$n = count( $toks );
	for ( $i = 0; $i < $n; $i++ ) {
		if ( ! is_array( $toks[ $i ] ) || T_STRING !== $toks[ $i ][0] || ! isset( $fns[ $toks[ $i ][1] ] ) ) { continue; }
		$fn = $toks[ $i ][1]; $line = $toks[ $i ][2];
		$j = $i + 1; while ( $j < $n && is_array( $toks[ $j ] ) && T_WHITESPACE === $toks[ $j ][0] ) { $j++; }
		if ( '(' !== $toks[ $j ] ) { continue; }
		$args = array(); $cur = null; $depth = 0;
		for ( $k = $j + 1; $k < $n; $k++ ) {
			$t = $toks[ $k ];
			if ( '(' === $t ) { $depth++; $cur = false; continue; }
			if ( ')' === $t ) { if ( 0 === $depth ) { $args[] = $cur; break; } $depth--; continue; }
			if ( ',' === $t && 0 === $depth ) { $args[] = $cur; $cur = null; continue; }
			if ( is_array( $t ) && T_WHITESPACE === $t[0] ) { continue; }
			if ( is_array( $t ) && T_CONSTANT_ENCAPSED_STRING === $t[0] && null === $cur ) { $cur = eval( 'return ' . $t[1] . ';' ); continue; }
			$cur = false;
		}
		$domain = end( $args );
		if ( $want !== $domain || ! is_string( $args[0] ?? null ) ) { continue; }
		$ctx = in_array( $fn, array( '_x', 'esc_html_x', 'esc_attr_x' ), true ) ? ( $args[1] ?? '' ) : '';
		$key = $ctx . "\x04" . $args[0];
		$entries[ $key ]['msgid'] = $args[0];
		$entries[ $key ]['ctx'] = $ctx;
		if ( '_n' === $fn ) { $entries[ $key ]['plural'] = $args[1]; }
		$entries[ $key ]['refs'][] = ltrim( str_replace( $root, '', $path ), '/' ) . ':' . $line;
	}
}
$esc = function ( $s ) { return addcslashes( $s, "\"\\\n\t" ); };
$pot = "# Copyright (C) Larijani Stone\n# This file is distributed under the GPL-2.0-or-later.\nmsgid \"\"\nmsgstr \"\"\n\"Project-Id-Version: " . $proj . "\\n\"\n\"MIME-Version: 1.0\\n\"\n\"Content-Type: text/plain; charset=UTF-8\\n\"\n\"Content-Transfer-Encoding: 8bit\\n\"\n\"X-Domain: " . $want . "\\n\"\n\n";
foreach ( $entries as $e ) {
	$pot .= '#: ' . implode( ' ', array_slice( $e['refs'], 0, 4 ) ) . "\n";
	if ( $e['ctx'] ) { $pot .= 'msgctxt "' . $esc( $e['ctx'] ) . "\"\n"; }
	$pot .= 'msgid "' . $esc( $e['msgid'] ) . "\"\n";
	if ( isset( $e['plural'] ) ) { $pot .= 'msgid_plural "' . $esc( $e['plural'] ) . "\"\nmsgstr[0] \"\"\nmsgstr[1] \"\"\n\n"; } else { $pot .= "msgstr \"\"\n\n"; }
}
file_put_contents( $out, $pot );
echo count( $entries ), " strings\n";
