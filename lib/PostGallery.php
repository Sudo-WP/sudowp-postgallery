<?php

namespace SudoWP_PostGallery\Lib;

use SudoWP_PostGallery\Lib\PostGalleryLoader;
use SudoWP_PostGallery\Lib\PostGalleryI18n;
use SudoWP_PostGallery\Admin\PostGalleryAdmin;
use SudoWP_PostGallery\Public\PostGalleryPublic;

/**
 * The core plugin class.
 */
class PostGallery {

	/**
	 * Explicit Property Declaration (PHP 8.2 Fix)
	 */
	protected PostGalleryLoader $loader;
	protected string $plugin_name;
	protected string $version;

	public function __construct() {
		$this->plugin_name = 'sudowp-postgallery';
		$this->version = '1.0.1'; // Bump version for SudoWP release

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies(): void {
		// We assume the Autoloader handles class inclusion via namespaces.
		// Initializing the Loader class engine.
		$this->loader = new PostGalleryLoader();
	}

	private function set_locale(): void {
		$plugin_i18n = new PostGalleryI18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks(): void {
		$plugin_admin = new PostGalleryAdmin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        // Ensure menu is added securely
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
	}

	private function define_public_hooks(): void {
		$plugin_public = new PostGalleryPublic( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	public function run(): void {
		$this->loader->run();
	}

	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	public function get_loader(): PostGalleryLoader {
		return $this->loader;
	}

	public function get_version(): string {
		return $this->version;
	}
}