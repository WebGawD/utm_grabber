<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SKA_UTM_Grabber {
    private $admin;
    private $public;

    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once SKA_UTM_GRABBER_PATH . 'includes/class-ska-utm-grabber-admin.php';
        require_once SKA_UTM_GRABBER_PATH . 'includes/class-ska-utm-grabber-public.php';

        $this->admin = new SKA_UTM_Grabber_Admin();
        $this->public = new SKA_UTM_Grabber_Public();
    }

    private function define_admin_hooks() {
        add_action( 'admin_menu', array( $this->admin, 'add_plugin_admin_menu' ) );
        add_action( 'admin_init', array( $this->admin, 'register_and_build_fields' ) );
    }

    private function define_public_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) );
        add_shortcode( 'ska_utm_grabber_anchor', array( $this->public, 'anchor_shortcode' ) );
    }

    public function run() {
        // Main plugin execution
    }
}