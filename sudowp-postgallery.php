<?php

use Lib\PostGallery;
use Lib\PostGalleryActivator;
use Lib\PostGalleryDeactivator;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sudowp.com
 * @since             1.12.6
 * @package           PostGallery
 *
 * @wordpress-plugin
 * Plugin Name:       SudoWP PostGallery (Security Fork)
 * Plugin URI:        https://github.com/Sudo-WP/sudowp-postgallery
 * Description:       A security-hardened fork of the abandoned PostGallery plugin. Fixes critical Arbitrary File Upload vulnerabilities (CVE-2025-13543).
 * Version:           1.12.6
 * Author:            SudoWP, WP Republic
 * Author URI:        https://sudowp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       postgallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'POSTGALLERY_VERSION', '1.12.6' );

define( 'POSTGALLERY_DIR', str_replace( '\\', '/', __DIR__ ) );
define( 'POSTGALLERY_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * The class responsible for auto loading classes.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/PostGalleryActivator.php
 */
function activatePostGallery() {
	PostGalleryActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/PostGalleryDeactivator.php
 */
function deactivatePostGallery() {
	PostGalleryDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activatePostGallery' );
register_deactivation_hook( __FILE__, 'deactivatePostGallery' );

/**
 * Begins execution of the plugin.
 */
function run_post_gallery() {
	$plugin = new PostGallery();
	$plugin->run();
}
run_post_gallery();