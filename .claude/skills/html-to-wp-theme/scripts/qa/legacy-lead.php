<?php
// Create a lead the way theme 1.3.x did: public media-library attachment.
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require $argv[1] . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$up = wp_upload_dir();
$file = $up['path'] . '/lead-legacytest-plan.pdf';
file_put_contents( $file, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n" );
$lead = wp_insert_post( array( 'post_type' => 'ls_lead', 'post_status' => 'private', 'post_title' => 'فرم قدیمی – تست' ) );
$att = wp_insert_attachment( array( 'post_mime_type' => 'application/pdf', 'post_title' => 'plan', 'post_status' => 'private', 'post_parent' => $lead ), $file, $lead );
update_post_meta( $lead, '_ls_lead_fields', array( array( 'label' => 'نام', 'value' => 'آزمون قدیمی' ), array( 'label' => 'ایمیل', 'value' => 'old@example.com' ) ) );
update_post_meta( $lead, '_ls_lead_form', 'فرم قدیمی' );
update_post_meta( $lead, '_ls_lead_phone', '09120000000' );
update_post_meta( $lead, '_ls_lead_tracking', 'LS-12345' );
update_post_meta( $lead, '_ls_lead_files', array( $att ) );
echo json_encode( array( 'lead' => $lead, 'att' => $att, 'url' => wp_get_attachment_url( $att ), 'sha' => hash_file( 'sha256', $file ) ) ), "\n";
