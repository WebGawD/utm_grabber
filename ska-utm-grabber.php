<?php

/**
 * Plugin Name: SearchKings Africa UTM Grabber
 * Plugin URI: https://searchkingsafrica.com/utm-grabber
 * Description: A plugin that dynamically updates links with UTM parameters and adds them to form fields.
 * Version: 1.0.3
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Lwazi Ndlebe
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: ska-utm-grabber
 * Domain Path: /languages
 * 
 * @package SKA_UTM_Grabber
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'SKA_UTM_GRABBER_VERSION', '1.0.3' );
define('SKA_UTM_GRABBER_PLUGIN_FILE', __FILE__);
define('SKA_UTM_GRABBER_PLUGIN_BASENAME', plugin_basename(SKA_UTM_GRABBER_PLUGIN_FILE));
define( 'SKA_UTM_GRABBER_PATH', plugin_dir_path( __FILE__ ) );
define( 'SKA_UTM_GRABBER_URL', plugin_dir_url( __FILE__ ) );

// Include main plugin class
require_once SKA_UTM_GRABBER_PATH . 'includes/class-ska-utm-grabber.php';

// Initialize the plugin
function ska_utm_grabber_init() {
    $plugin = new SKA_UTM_Grabber();
    $plugin->run();
}
add_action( 'plugins_loaded', 'ska_utm_grabber_init' );

// Load plugin text domain
function ska_utm_grabber_load_textdomain() {
    load_plugin_textdomain('ska-utm-grabber', false, dirname(SKA_UTM_GRABBER_PLUGIN_BASENAME) . '/languages');
}
add_action( 'init', 'ska_utm_grabber_load_textdomain' );

// Check and update plugin version
function ska_utm_grabber_check_version() {
    $stored_version = get_option('ska_utm_grabber_version');
    if ($stored_version !== SKA_UTM_GRABBER_VERSION) {
        ska_utm_grabber_update();
    }
}
add_action( 'plugins_loaded', 'ska_utm_grabber_check_version', 0 );

// Update routine
function ska_utm_grabber_update() {
    // Perform any necessary updates based on version differences
    // For example, you might need to update database schemas or plugin settings

    // Update the version in the database
    update_option( 'ska_utm_grabber_version', SKA_UTM_GRABBER_VERSION );
}

// Activation hook
function ska_utm_grabber_activate() {
    // Perform any necessary actions on plugin activation
    // This is a good place to set default options if needed

    // Set the initial version in the database
    update_option('ska_utm_grabber_version', SKA_UTM_GRABBER_VERSION);


    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(SKA_UTM_GRABBER_PLUGIN_FILE, 'ska_utm_grabber_activate');

// Deactivation hook
function ska_utm_grabber_deactivate() {
    // Perform any cleanup if necessary
    flush_rewrite_rules();
}
register_deactivation_hook(SKA_UTM_GRABBER_PLUGIN_FILE, 'ska_utm_grabber_deactivate');

// Uninstall hook (typically defined in uninstall.php)
// register_uninstall_hook( SKA_UTM_GRABBER_PLUGIN_FILE, 'ska_utm_grabber_uninstall' );

if (!class_exists('SKA_UTM_Grabber_Updater')) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ska-utm-grabber-updater.php';
}

function ska_utm_grabber_updater()
{
    $updater = new SKA_UTM_Grabber_Updater(
        __FILE__,
        'https://plugin.searchkingsafrica.com/wp-update-server/?action=get_metadata&slug=ska-utm-grabber'
    );
}
add_action('init', 'ska_utm_grabber_updater');