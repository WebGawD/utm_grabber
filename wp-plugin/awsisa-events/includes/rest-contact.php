<?php
/**
 * Contact form REST endpoint.
 *
 * Registered in awsisa-events.php alongside the other routes.
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the /awsisa/v1/contact route.
 * Called from awsisa_register_rest_routes() in rest-api.php.
 */
function awsisa_register_contact_route() {
	register_rest_route(
		'awsisa/v1',
		'/contact',
		array(
			'methods'             => 'POST',
			'callback'            => 'awsisa_handle_contact',
			'permission_callback' => '__return_true',
			'args'                => array(
				'first_name' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'last_name'  => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'email'      => array( 'required' => true, 'sanitize_callback' => 'sanitize_email', 'validate_callback' => 'is_email' ),
				'subject'    => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
				'message'    => array( 'required' => true, 'sanitize_callback' => 'sanitize_textarea_field' ),
				'organisation' => array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
				'popia_consent' => array( 'required' => true ),
			),
		)
	);
}
add_action( 'rest_api_init', 'awsisa_register_contact_route' );

/**
 * Handles the contact form submission.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_handle_contact( WP_REST_Request $request ) {
	if ( ! $request->get_param( 'popia_consent' ) ) {
		return new WP_Error( 'popia_required', 'Consent to data processing is required.', array( 'status' => 422 ) );
	}

	$name    = trim( $request->get_param( 'first_name' ) . ' ' . $request->get_param( 'last_name' ) );
	$email   = $request->get_param( 'email' );
	$subject = $request->get_param( 'subject' );
	$message = $request->get_param( 'message' );
	$org     = $request->get_param( 'organisation' );

	$subject_map = array(
		'registration'  => 'Delegate Registration',
		'accommodation' => 'Accommodation & Bookings',
		'sponsorship'   => 'Sponsorship & Exhibiting',
		'speaking'      => 'Speaking / Abstract Submission',
		'media'         => 'Media & Press',
		'legacy'        => 'Legacy Initiative / Donations',
		'general'       => 'General Enquiry',
	);
	$subject_label = $subject_map[ $subject ] ?? ucfirst( $subject );

	$to      = 'info@afriwater-san.africa';
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$body = sprintf(
		'<p><strong>From:</strong> %1$s &lt;%2$s&gt;<br><strong>Organisation:</strong> %3$s<br><strong>Topic:</strong> %4$s</p><hr><p>%5$s</p>',
		esc_html( $name ),
		esc_html( $email ),
		esc_html( $org ?: '—' ),
		esc_html( $subject_label ),
		nl2br( esc_html( $message ) )
	);

	$sent = wp_mail( $to, '[Watersan 2026 Contact] ' . $subject_label . ' — ' . $name, $body, $headers );

	if ( ! $sent ) {
		return new WP_Error( 'mail_failed', 'Failed to send email.', array( 'status' => 500 ) );
	}

	return rest_ensure_response( array( 'success' => true ) );
}
