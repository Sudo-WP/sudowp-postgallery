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

$success = false;
$uploads = wp_upload_dir();
$uploadDir = $uploads['basedir'];
$uploadUrl = $uploads['baseurl'];

$postid = filter_input( INPUT_GET, 'postid', FILTER_SANITIZE_NUMBER_INT );
if ( empty( $postid ) ) {
    $postid = filter_input( INPUT_POST, 'postid', FILTER_SANITIZE_NUMBER_INT );
}

$attachmentId = filter_input( INPUT_GET, 'attachmentid', FILTER_SANITIZE_NUMBER_INT );
if ( empty( $attachmentId ) ) {
    $attachmentId = filter_input( INPUT_POST, 'attachmentid', FILTER_SANITIZE_NUMBER_INT );
}

// SudoWP Security: Capability checks
if ( !empty( $postid ) && !current_user_can( 'edit_post', $postid ) ) {
    wp_die( 'Insufficient permissions to delete images for this post.', 'Permission Denied', array( 'response' => 403 ) );
}

if ( !empty( $attachmentId ) && !current_user_can( 'delete_post', $attachmentId ) ) {
    wp_die( 'Insufficient permissions to delete this attachment.', 'Permission Denied', array( 'response' => 403 ) );
}

$deletedFiles = [];

if ( empty( $attachmentId ) && !empty( $postid ) ) {
    // Delete all images from a post
    $images = \Lib\PostGallery::getImages( $postid );
    foreach ( $images as $image ) {
        $deletedFiles[] = \Admin\PostGalleryAdmin::deleteAttachment( $image['attachmentId'] );
    }
    $success = true;
} else if ( !empty( $attachmentId ) ) {
    // Deletes a single file
    $deletedFiles[] = \Admin\PostGalleryAdmin::deleteAttachment( $attachmentId );
    $success = true;
} else {
    die( 'No postid or attachmentid' );
}


// delete from cache - SudoWP Security: Add safety checks
$cacheDir = $uploadDir . '/cache/';
if ( file_exists( $cacheDir ) && is_dir( $cacheDir ) ) {
    $cacheDirContents = scandir( $cacheDir );
    foreach ( $deletedFiles as $file ) {
        // Sanitize filename
        $file = basename( $file );
        $file = explode( '.', $file );
        $fileExtension = array_pop( $file );
        $file = implode( '.', $file );
        $length = strlen( $file );

        // SudoWP Security: Optimize realpath() calls - calculate once outside loop
        $realCacheDir = realpath( $cacheDir );
        
        foreach ( $cacheDirContents as $cacheFile ) {
            // Skip . and ..
            if ( $cacheFile === '.' || $cacheFile === '..' ) {
                continue;
            }
            
            if ( substr( $cacheFile, 0, $length ) == $file ) {
                $cacheFilePath = $cacheDir . $cacheFile;
                $realCacheFilePath = realpath( $cacheFilePath );
                
                // Verify path is within cache directory before deleting
                if ( $realCacheFilePath && $realCacheDir && strpos( $realCacheFilePath, $realCacheDir ) === 0 ) {
                    unlink( $cacheFilePath );
                }
            }
        }
    }
}

echo( intval( $success ) );
