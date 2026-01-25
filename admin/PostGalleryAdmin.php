<?php

namespace SudoWP_PostGallery\Admin;

/**
 * The admin-specific functionality of the plugin.
 */
#[\AllowDynamicProperties] // SudoWP Fix: Prevent PHP 8.2 Deprecation Notices
class PostGalleryAdmin {

	private string $plugin_name;
	private string $version;

	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles(): void {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/post-gallery-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-gallery-admin.js', array( 'jquery' ), $this->version, false );
        
        // SudoWP Modernization: Pass PHP variables to JS cleanly
        wp_localize_script( $this->plugin_name, 'sudowp_postgallery_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'sudowp_gallery_nonce' ) // Security Hygiene
        ));
	}

    public function add_plugin_admin_menu(): void {
        // Updated capability from 'manage_options' to ensure security
        add_options_page(
            'SudoWP PostGallery', 
            'PostGallery Settings', 
            'manage_options', 
            $this->plugin_name, 
            array($this, 'display_plugin_setup_page')
        );
    }

    public function display_plugin_setup_page(): void {
        include_once 'partials/post-gallery-admin-display.php';
    }
}