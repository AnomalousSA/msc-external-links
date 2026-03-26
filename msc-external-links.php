<?php
/**
 * Plugin Name: Micro Site Care: External Links
 * Plugin URI:  https://anomalous.co.za
 * Description: Lightweight external link highlighting with icon, noopener, target-blank, and domain exclusion controls.
 * Version:     0.1.0
 * Author:      Anomalous Developers
 * Author URI:  https://anomalous.co.za
 * Text Domain: msc-external-links
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MSCEL_PLUGIN_VERSION', '0.1.0' );
define( 'MSCEL_PLUGIN_FILE', __FILE__ );
define( 'MSCEL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSCEL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MSCEL_PLUGIN_DIR . 'includes/class-msc-external-links-module.php';
require_once MSCEL_PLUGIN_DIR . 'includes/class-msc-external-links-settings.php';
require_once MSCEL_PLUGIN_DIR . 'includes/class-msc-external-links.php';

register_activation_hook( __FILE__, array( 'MSC_External_Links', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MSC_External_Links', 'deactivate' ) );

add_action(
    'plugins_loaded',
    static function () {
        load_plugin_textdomain( 'msc-external-links', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        MSC_External_Links::instance();
    }
);
