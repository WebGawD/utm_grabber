<?php
/**
 * REST API endpoints for Awsisa Events.
 *
 * All endpoints are under the namespace: awsisa/v1
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all REST API routes.
 */
function awsisa_register_rest_routes() {
	$ns = 'awsisa/v1';

	// Public endpoints (no auth required).
	register_rest_route( $ns, '/register',           array( 'methods' => 'POST', 'callback' => 'awsisa_rest_register',         'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/donate',             array( 'methods' => 'POST', 'callback' => 'awsisa_rest_donate',           'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/accommodation/book', array( 'methods' => 'POST', 'callback' => 'awsisa_rest_book_accomm',      'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/nfc/tap',            array( 'methods' => 'POST', 'callback' => 'awsisa_rest_nfc_tap',          'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/agenda',             array( 'methods' => 'GET',  'callback' => 'awsisa_rest_get_agenda',       'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/sponsors',           array( 'methods' => 'GET',  'callback' => 'awsisa_rest_get_sponsors',     'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/accommodation',      array( 'methods' => 'GET',  'callback' => 'awsisa_rest_get_accomm',       'permission_callback' => '__return_true' ) );

	// Payment webhook endpoints.
	register_rest_route( $ns, '/payment/payfast',       array( 'methods' => 'POST', 'callback' => 'awsisa_rest_payfast_itn',      'permission_callback' => '__return_true' ) );
	register_rest_route( $ns, '/payment/peachpayments', array( 'methods' => 'POST', 'callback' => 'awsisa_rest_peach_webhook',    'permission_callback' => '__return_true' ) );
}
add_action( 'rest_api_init', 'awsisa_register_rest_routes' );

// ============================================================
// Helper: CORS headers for REST API (for React app calls)
// ============================================================

/**
 * Adds CORS headers to REST API responses.
 *
 * @param WP_HTTP_Response $response Response object.
 * @return WP_HTTP_Response
 */
function awsisa_rest_cors_headers( $response ) {
	$allowed_origins = array(
		home_url(),
		'http://localhost:3000',
		'http://localhost:5173',
	);

	$origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) : '';

	if ( in_array( $origin, $allowed_origins, true ) ) {
		$response->header( 'Access-Control-Allow-Origin',  $origin );
		$response->header( 'Access-Control-Allow-Methods', 'GET, POST, OPTIONS' );
		$response->header( 'Access-Control-Allow-Headers', 'Authorization, Content-Type, X-WP-Nonce' );
	}
	return $response;
}
add_filter( 'rest_post_dispatch', 'awsisa_rest_cors_headers' );

// ============================================================
// Validation helpers
// ============================================================

/**
 * Validates and sanitizes a delegate registration payload.
 *
 * @param array $data Raw POST data.
 * @return array|WP_Error Sanitized data or WP_Error with validation messages.
 */
function awsisa_validate_registration( $data ) {
	$errors = new WP_Error();

	$first_name   = isset( $data['first_name'] )   ? sanitize_text_field( $data['first_name'] )   : '';
	$last_name    = isset( $data['last_name'] )    ? sanitize_text_field( $data['last_name'] )    : '';
	$email        = isset( $data['email'] )        ? sanitize_email( $data['email'] )             : '';
	$country      = isset( $data['country'] )      ? sanitize_text_field( $data['country'] )      : '';
	$delegate_type = isset( $data['delegate_type'] ) ? sanitize_text_field( $data['delegate_type'] ) : '';
	$popia_consent = ! empty( $data['popia_consent'] );

	$valid_types = array( 'government', 'utility', 'private_sector', 'ngo', 'academic', 'media', 'exhibitor', 'sponsor' );

	if ( empty( $first_name ) ) $errors->add( 'first_name', __( 'First name is required.', 'awsisa-events' ) );
	if ( empty( $last_name ) )  $errors->add( 'last_name',  __( 'Last name is required.', 'awsisa-events' ) );
	if ( ! is_email( $email ) ) $errors->add( 'email',      __( 'A valid email address is required.', 'awsisa-events' ) );
	if ( empty( $country ) )    $errors->add( 'country',    __( 'Country is required.', 'awsisa-events' ) );
	if ( ! in_array( $delegate_type, $valid_types, true ) ) {
		$errors->add( 'delegate_type', __( 'Invalid delegate type.', 'awsisa-events' ) );
	}
	if ( ! $popia_consent ) {
		$errors->add( 'popia_consent', __( 'POPIA consent is required.', 'awsisa-events' ) );
	}

	if ( $errors->has_errors() ) {
		return $errors;
	}

	return array(
		'first_name'         => $first_name,
		'last_name'          => $last_name,
		'email'              => $email,
		'organisation'       => isset( $data['organisation'] )  ? sanitize_text_field( $data['organisation'] )  : null,
		'job_title'          => isset( $data['job_title'] )     ? sanitize_text_field( $data['job_title'] )     : null,
		'country'            => $country,
		'phone'              => isset( $data['phone'] )         ? sanitize_text_field( $data['phone'] )         : null,
		'delegate_type'      => $delegate_type,
		'popia_consent'      => true,
		'popia_consented_at' => current_time( 'c' ),
		'marketing_consent'  => ! empty( $data['marketing_consent'] ),
		'profile_public'     => ! empty( $data['profile_public'] ),
	);
}

// ============================================================
// POST /awsisa/v1/register
// ============================================================

/**
 * Handles delegate registration.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_rest_register( WP_REST_Request $request ) {
	$body = $request->get_json_params() ?: $request->get_body_params();

	$validated = awsisa_validate_registration( $body );
	if ( is_wp_error( $validated ) ) {
		return new WP_REST_Response( array(
			'success' => false,
			'errors'  => $validated->get_error_messages(),
		), 422 );
	}

	// Check for existing email.
	$existing = awsisa_supabase( 'delegates', 'GET', array(), 'email=eq.' . rawurlencode( $validated['email'] ) . '&select=id,registration_status&limit=1' );
	if ( is_wp_error( $existing ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => $existing->get_error_message() ), 502 );
	}
	if ( ! empty( $existing ) ) {
		return new WP_REST_Response( array(
			'success' => false,
			'message' => __( 'An account with this email address already exists.', 'awsisa-events' ),
		), 409 );
	}

	// Create delegate (uses service role to bypass RLS).
	$delegate = awsisa_supabase( 'delegates', 'POST', $validated, '', true );
	if ( is_wp_error( $delegate ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => $delegate->get_error_message() ), 502 );
	}

	$delegate_row = is_array( $delegate ) && isset( $delegate[0] ) ? $delegate[0] : $delegate;

	// Log POPIA consent (service role, append-only).
	awsisa_supabase( 'popia_consents', 'POST', array(
		'entity_type'      => 'delegate',
		'entity_id'        => $delegate_row['id'],
		'consent_version'  => '1.0',
		'ip_address'       => awsisa_hash_ip( awsisa_get_client_ip() ),
		'user_agent'       => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
		'consented_fields' => array( 'data_processing', 'event_management' ),
	), '', true );

	// Queue confirmation email.
	awsisa_send_registration_email( $delegate_row );

	return new WP_REST_Response( array(
		'success'     => true,
		'delegate_id' => $delegate_row['id'],
		'qr_token'    => $delegate_row['qr_code_token'],
		'message'     => __( 'Registration successful! Check your email for your QR code.', 'awsisa-events' ),
	), 201 );
}

// ============================================================
// POST /awsisa/v1/donate
// ============================================================

/**
 * Handles a donation submission.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_rest_donate( WP_REST_Request $request ) {
	$body = $request->get_json_params() ?: $request->get_body_params();

	$donor_name   = isset( $body['donor_name'] )  ? sanitize_text_field( $body['donor_name'] ) : '';
	$donor_email  = isset( $body['donor_email'] ) ? sanitize_email( $body['donor_email'] )     : '';
	$amount_zar   = isset( $body['amount_zar'] )  ? abs( floatval( $body['amount_zar'] ) )     : 0;
	$popia_consent = ! empty( $body['popia_consent'] );

	if ( empty( $donor_name ) || ! is_email( $donor_email ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Name and valid email are required.', 'awsisa-events' ) ), 422 );
	}
	if ( $amount_zar < 10 ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Minimum donation is R10.', 'awsisa-events' ) ), 422 );
	}
	if ( ! $popia_consent ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'POPIA consent is required.', 'awsisa-events' ) ), 422 );
	}

	$donation = awsisa_supabase( 'donations', 'POST', array(
		'donor_name'    => $donor_name,
		'donor_email'   => $donor_email,
		'donor_phone'   => isset( $body['donor_phone'] ) ? sanitize_text_field( $body['donor_phone'] ) : null,
		'amount_zar'    => $amount_zar,
		'currency'      => 'ZAR',
		'payment_method' => isset( $body['payment_method'] ) ? sanitize_text_field( $body['payment_method'] ) : null,
		'is_anonymous'  => ! empty( $body['is_anonymous'] ),
		'message'       => isset( $body['message'] ) ? sanitize_textarea_field( $body['message'] ) : null,
		'project_area'  => isset( $body['project_area'] ) ? sanitize_text_field( $body['project_area'] ) : null,
		'popia_consent' => true,
		'payment_status' => 'pending',
	), '', true );

	if ( is_wp_error( $donation ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => $donation->get_error_message() ), 502 );
	}

	$donation_row = is_array( $donation ) && isset( $donation[0] ) ? $donation[0] : $donation;

	// Log POPIA consent.
	awsisa_supabase( 'popia_consents', 'POST', array(
		'entity_type'     => 'donor',
		'entity_id'       => $donation_row['id'],
		'consent_version' => '1.0',
		'ip_address'      => awsisa_hash_ip( awsisa_get_client_ip() ),
		'consented_fields' => array( 'donation_processing' ),
	), '', true );

	return new WP_REST_Response( array(
		'success'     => true,
		'donation_id' => $donation_row['id'],
		'amount_zar'  => $amount_zar,
		'message'     => __( 'Thank you for your Legacy contribution!', 'awsisa-events' ),
		// Caller redirects to payment gateway using this ID.
	), 201 );
}

// ============================================================
// POST /awsisa/v1/accommodation/book
// ============================================================

/**
 * Handles accommodation booking.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_rest_book_accomm( WP_REST_Request $request ) {
	$body       = $request->get_json_params() ?: $request->get_body_params();
	$delegate_id = isset( $body['delegate_id'] ) ? sanitize_text_field( $body['delegate_id'] ) : '';
	$package_id  = isset( $body['package_id'] )  ? sanitize_text_field( $body['package_id'] )  : '';

	if ( empty( $delegate_id ) || empty( $package_id ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Delegate ID and Package ID are required.', 'awsisa-events' ) ), 422 );
	}

	// Verify package exists and has availability.
	$package = awsisa_supabase( 'accommodation_packages', 'GET', array(), 'id=eq.' . rawurlencode( $package_id ) . '&select=*&limit=1' );
	if ( is_wp_error( $package ) || empty( $package ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Package not found.', 'awsisa-events' ) ), 404 );
	}

	$pkg = $package[0];
	if ( $pkg['booked_count'] >= $pkg['total_rooms'] ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Sorry, this package is fully booked.', 'awsisa-events' ) ), 409 );
	}

	// Create booking.
	$booking = awsisa_supabase( 'accommodation_bookings', 'POST', array(
		'delegate_id'     => $delegate_id,
		'package_id'      => $package_id,
		'check_in_date'   => sanitize_text_field( $body['check_in_date'] ?? '2026-11-09' ),
		'check_out_date'  => sanitize_text_field( $body['check_out_date'] ?? '2026-11-12' ),
		'guests'          => max( 1, intval( $body['guests'] ?? 1 ) ),
		'total_price'     => $pkg['price_zar'],
		'payment_status'  => 'pending',
		'special_requests' => isset( $body['special_requests'] ) ? sanitize_textarea_field( $body['special_requests'] ) : null,
	), '', true );

	if ( is_wp_error( $booking ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => $booking->get_error_message() ), 502 );
	}

	// Increment booked_count (PATCH, use RPC in production).
	awsisa_supabase( 'accommodation_packages', 'PATCH', array(
		'booked_count' => (int) $pkg['booked_count'] + 1,
	), 'id=eq.' . rawurlencode( $package_id ), true );

	$booking_row = is_array( $booking ) && isset( $booking[0] ) ? $booking[0] : $booking;

	return new WP_REST_Response( array(
		'success'    => true,
		'booking_id' => $booking_row['id'],
		'total_zar'  => $pkg['price_zar'],
		'message'    => __( 'Accommodation booked! Proceed to payment.', 'awsisa-events' ),
	), 201 );
}

// ============================================================
// POST /awsisa/v1/nfc/tap
// ============================================================

/**
 * Logs an NFC tap event (delegate card view or booth tap).
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function awsisa_rest_nfc_tap( WP_REST_Request $request ) {
	$body     = $request->get_json_params() ?: $request->get_body_params();
	$tap_type = isset( $body['tap_type'] ) ? sanitize_text_field( $body['tap_type'] ) : '';

	$valid_types = array( 'delegate_view', 'booth_tap', 'swag_download' );
	if ( ! in_array( $tap_type, $valid_types, true ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid tap_type.' ), 422 );
	}

	awsisa_supabase( 'nfc_taps', 'POST', array(
		'tap_type'      => $tap_type,
		'delegate_id'   => isset( $body['delegate_id'] ) ? sanitize_text_field( $body['delegate_id'] ) : null,
		'sponsor_id'    => isset( $body['sponsor_id'] )  ? sanitize_text_field( $body['sponsor_id'] )  : null,
		'visitor_token' => isset( $body['visitor_token'] ) ? sanitize_text_field( $body['visitor_token'] ) : null,
		'user_agent'    => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : null,
	), '', false ); // anon key — no PII stored in user_agent beyond UA string.

	return new WP_REST_Response( array( 'success' => true ), 201 );
}

// ============================================================
// GET endpoints
// ============================================================

/**
 * Returns published agenda sessions.
 *
 * @return WP_REST_Response
 */
function awsisa_rest_get_agenda() {
	$data = awsisa_supabase( 'agenda_sessions', 'GET', array(), 'is_published=eq.true&order=day.asc,sort_order.asc&select=*' );
	return is_wp_error( $data )
		? new WP_REST_Response( array( 'error' => $data->get_error_message() ), 502 )
		: new WP_REST_Response( $data );
}

/**
 * Returns active sponsors.
 *
 * @return WP_REST_Response
 */
function awsisa_rest_get_sponsors() {
	$data = awsisa_supabase( 'sponsors', 'GET', array(), 'is_active=eq.true&order=sort_order.asc&select=id,name,tier,logo_url,website_url,booth_number,booth_nfc_slug,description' );
	return is_wp_error( $data )
		? new WP_REST_Response( array( 'error' => $data->get_error_message() ), 502 )
		: new WP_REST_Response( $data );
}

/**
 * Returns active accommodation packages.
 *
 * @return WP_REST_Response
 */
function awsisa_rest_get_accomm() {
	$data = awsisa_supabase( 'accommodation_packages', 'GET', array(), 'is_active=eq.true&order=sort_order.asc&select=*' );
	return is_wp_error( $data )
		? new WP_REST_Response( array( 'error' => $data->get_error_message() ), 502 )
		: new WP_REST_Response( $data );
}

// ============================================================
// Payment Webhooks (Stubs)
// ============================================================

/**
 * Handles PayFast ITN (Instant Transaction Notification).
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function awsisa_rest_payfast_itn( WP_REST_Request $request ) {
	// TODO: Validate PayFast signature before processing.
	// See: https://developers.payfast.co.za/api#itn

	$body = $request->get_body_params();

	$payment_status = isset( $body['payment_status'] ) ? sanitize_text_field( $body['payment_status'] ) : '';
	$m_payment_id   = isset( $body['m_payment_id'] )   ? sanitize_text_field( $body['m_payment_id'] )   : '';

	if ( 'COMPLETE' === $payment_status && ! empty( $m_payment_id ) ) {
		// Update delegate payment status.
		awsisa_supabase( 'delegates', 'PATCH', array(
			'payment_status'      => 'paid',
			'payment_ref'         => $m_payment_id,
			'registration_status' => 'confirmed',
		), 'payment_ref=eq.' . rawurlencode( $m_payment_id ), true );
	}

	// PayFast requires HTTP 200 to acknowledge receipt.
	return new WP_REST_Response( 'OK', 200 );
}

/**
 * Handles Peachpayments webhook.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function awsisa_rest_peach_webhook( WP_REST_Request $request ) {
	// TODO: Validate Peachpayments webhook signature.
	// See: https://developer.peachpayments.com/docs/webhooks

	$body   = $request->get_json_params();
	$result = isset( $body['result'] ) ? $body['result'] : array();
	$code   = isset( $result['code'] ) ? $result['code'] : '';

	// Peach success codes start with '000.000'.
	if ( strpos( $code, '000.000' ) === 0 ) {
		$ref = isset( $body['id'] ) ? sanitize_text_field( $body['id'] ) : '';
		awsisa_supabase( 'delegates', 'PATCH', array(
			'payment_status'      => 'paid',
			'payment_ref'         => $ref,
			'registration_status' => 'confirmed',
		), 'payment_ref=eq.' . rawurlencode( $ref ), true );
	}

	return new WP_REST_Response( array( 'status' => 'received' ), 200 );
}

// ============================================================
// Utility helpers
// ============================================================

/**
 * Returns the real client IP address.
 *
 * @return string
 */
function awsisa_get_client_ip() {
	$keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
	foreach ( $keys as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
			$ip = explode( ',', $ip )[0];
			if ( filter_var( trim( $ip ), FILTER_VALIDATE_IP ) ) {
				return trim( $ip );
			}
		}
	}
	return '0.0.0.0';
}

/**
 * One-way hashes an IP address for POPIA-compliant storage.
 *
 * @param string $ip IP address.
 * @return string SHA-256 hash.
 */
function awsisa_hash_ip( $ip ) {
	return hash( 'sha256', $ip . wp_salt( 'auth' ) );
}
