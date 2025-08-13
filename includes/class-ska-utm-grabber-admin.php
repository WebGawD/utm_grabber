<?php
class SKA_UTM_Grabber_Admin {
    public function add_plugin_admin_menu() {
        add_options_page(
            __( 'SearchKings Africa UTM Grabber Settings', 'ska-utm-grabber' ),
            __( 'SearchKings Africa UTM Grabber', 'ska-utm-grabber' ),
            'manage_options',
            'ska-utm-grabber',
            array( $this, 'display_plugin_admin_page' )
        );
    }
    

    public function display_plugin_admin_page() {
        // Display admin settings page
        include_once SKA_UTM_GRABBER_PATH . 'admin/partials/ska-utm-grabber-admin-display.php';
    }

    public function register_and_build_fields()
    {

        // Add settings section
        add_settings_section(
            'utm_grabber_section',
            __( 'SearchKings Africa UTM Grabber Settings', 'ska-utm-grabber' ),
            array( $this, 'utm_grabber_settings_section_callback' ),
            'utmGrabber'
        );
    }

    public function utm_grabber_settings_section_callback() {
        echo __( 'Enter the settings for the SearchKings Africa UTM Grabber plugin.', 'ska-utm-grabber' );
    }

    public function save_settings()
    {
        if (!isset($_POST['utm_grabber_nonce']) || !wp_verify_nonce($_POST['utm_grabber_nonce'], 'utm_grabber_nonce_action')) {
            wp_die('Security check failed');
        }
        // Process and save settings
    }
}