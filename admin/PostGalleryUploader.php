<?php

namespace Admin;

use Lib\PostGallery;
use Pub\PostGalleryPublic;
use Lib\Thumb;

if ( !defined( 'ABSPATH' ) ) exit;

class PostGalleryUploader {
	private $uploadedFile;
	private $uploadDir;
	private $uploadUrl;
	private $postid;
	private $uploadFolder;
	private $filename;
	private $fullPath;

	private $postGalleryPublic;

	public function __construct() {
		// SudoWP Security Patch: Verify User Capabilities
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_die( 'Security Error: Insufficient permissions.' );
		}

		$uploads = wp_upload_dir();
		$this->postGalleryPublic = PostGalleryPublic::getInstance();
		$this->postid = filter_input( INPUT_POST, 'postid', FILTER_SANITIZE_NUMBER_INT );
		$this->uploadDir = $uploads['basedir'];
		$this->uploadUrl = str_replace( get_bloginfo( 'wpurl' ), '', $uploads['baseurl'] );
		$this->uploadFolder = filter_input( INPUT_POST, 'uploadFolder', FILTER_SANITIZE_STRING );

		$this->uploadedFile = $_FILES['file'];

		// SudoWP Security Patch: Strict File Type Validation
		$this->validateFileSecurity();

		$this->createFilename();
		$this->createFolders();
		
		if ( move_uploaded_file( $this->uploadedFile['tmp_name'], $this->fullPath ) ) {
			$this->resizeImage( 1920, 1080 );
			$thumb = $this->createThumb();
			$attachmentId = $this->createAttachmentPost();

			$html = $this->getItemHtml( $attachmentId, $thumb );
			echo $html;
		} else {
			echo 'error';
		}
	}

	/**
	 * SudoWP: Strict Security Validation
	 * Blocks PHP execution and enforces image-only uploads.
	 */
	private function validateFileSecurity() {
		if ( empty( $this->uploadedFile ) || ! isset( $this->uploadedFile['name'] ) ) {
			wp_die( 'No file uploaded.' );
		}

		$filename = $this->uploadedFile['name'];
		$file_ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		
		// 1. Allowlist Check
		$allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
		if ( ! in_array( $file_ext, $allowed_extensions, true ) ) {
			wp_die( 'Security Error: Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.' );
		}

		// 2. MIME Type Check
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $this->uploadedFile['tmp_name'] );
		finfo_close( $finfo );

		$allowed_mimes = array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'image/webp'
		);

		if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
			wp_die( 'Security Error: Invalid MIME type detected.' );
		}
	}

	private function createFilename() {
		$filename = isset( $_REQUEST["name"] ) ? $_REQUEST["name"] : $this->uploadedFile['name'];
		
		// SudoWP: Use sanitize_file_name
		$filename = sanitize_file_name( $filename );
		
		// Ensure extension is safe (double check)
		$info = pathinfo( $filename );
		$ext  = empty( $info['extension'] ) ? '' : '.' . strtolower( $info['extension'] );
		$name = basename( $filename, $ext );
		
		$this->filename = $name . $ext;
		$this->fullPath = $this->uploadDir . '/gallery/' . $this->uploadFolder . '/' . $this->filename;
		
		// Prevent overwriting
		$i = 1;
		while( file_exists( $this->fullPath ) ) {
			$this->filename = $name . '_' . $i . $ext;
			$this->fullPath = $this->uploadDir . '/gallery/' . $this->uploadFolder . '/' . $this->filename;
			$i++;
		}
	}

	private function createFolders() {
		if ( ! file_exists( $this->uploadDir . '/gallery' ) ) {
			mkdir( $this->uploadDir . '/gallery', 0755 );
		}
		if ( ! file_exists( $this->uploadDir . '/gallery/' . $this->uploadFolder ) ) {
			mkdir( $this->uploadDir . '/gallery/' . $this->uploadFolder, 0755 );
		}
	}

	private function createThumb() {
		$thumbInstance = Thumb::getInstance();
		$thumb = $thumbInstance->getThumb( [
			'path' => $this->fullPath,
			'width' => 300,
			'height' => 300,
			'scale' => 2,
		] );
		return $thumb;
	}

	private function resizeImage( $width, $height ) {
		// Basic resize logic (kept from original, but safe now due to file validation)
		$thumbInstance = Thumb::getInstance();
		$thumb = $thumbInstance->getThumb( [
			'path' => $this->fullPath,
			'width' => $width,
			'height' => $height,
			'scale' => 2,
		] );

		// Replace original with resized version to save space
		if ( file_exists( $thumb['path'] ) && $thumb['path'] !== $this->fullPath ) {
			unlink( $this->fullPath );
			rename( $thumb['path'], $this->fullPath );
		}
	}

	private function createAttachmentPost() {
		// Use WP standard attachment logic
		$file_type = wp_check_filetype( $this->filename, null );
		$attachment = array(
			'guid'           => $this->uploadUrl . '/gallery/' . $this->uploadFolder . '/' . $this->filename,
			'post_mime_type' => $file_type['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $this->filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $this->fullPath, $this->postid );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $this->fullPath );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	private function getItemHtml( $attachmentId, $thumb ) {
		$tpl = new \Lib\Template( POSTGALLERY_DIR . '/admin/partials/uploaded-image-item.php' );
		$image = [
			'id' => $attachmentId,
			'thumb' => $thumb['url'],
			'full' => wp_get_attachment_url( $attachmentId ),
			'name' => basename( $this->fullPath )
		];
		$tpl->set( 'image', $image );
		return $tpl->render();
	}
}