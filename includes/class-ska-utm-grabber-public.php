<?php
class SKA_UTM_Grabber_Public {
    public function enqueue_scripts() {
        wp_enqueue_script( 'ska-utm-grabber-script', SKA_UTM_GRABBER_URL . 'assets/js/ska-utm-grabber.js', array('jquery'), SKA_UTM_GRABBER_VERSION, true );
        wp_enqueue_style( 'ska-utm-grabber-style', SKA_UTM_GRABBER_URL . 'assets/css/ska-utm-grabber.css', array(), SKA_UTM_GRABBER_VERSION );

        wp_localize_script('ska-utm-grabber-script', 'utmGrabberData', array(
            'utmParams' => array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'device', 'network', 'placement', 'adposition', 'gclid')
        ) );
    }

   
}