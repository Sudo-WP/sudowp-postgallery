<?php
if ( !defined( 'ABSPATH' ) ) exit;

// SudoWP Security: Authentication check
if ( !is_user_logged_in() ) {
    die( 'Login required!' );
}

// SudoWP Security: Nonce verification for CSRF protection
if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'sudowp_gallery_nonce' ) ) {
    wp_die( 'Security check failed!', 'Nonce Verification Failed', array( 'response' => 403 ) );
}

if ( empty( $_FILES ) || empty( $_FILES['file'] ) ) {
    die( 'No uploaded files' );
}

$uploader = new \Admin\PostGalleryUploader();
$result = $uploader->handleUpload();

echo json_encode( $result );
die();