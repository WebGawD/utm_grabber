<?php
/**
 * Delegate PWA authentication REST endpoints.
 *
 * Registers three public routes for OTP-based delegate login and QR token
 * lookup. All writes use the Supabase service key; reads use the anon key.
 *
 * Routes:
 *   POST /awsisa/v1/delegate/auth/request-otp  — generate + email 6-digit OTP
 *   POST /awsisa/v1/delegate/auth/verify-otp   — verify code, return delegate
 *   GET  /awsisa/v1/delegate/auth/qr/(?P<token>[a-zA-Z0-9\-]+) — QR token lookup
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the delegate auth REST routes.
 */
function awsisa_register_delegate_routes() {
	$ns = 'awsisa/v1';

	register_rest_route(
		$ns,
		'/delegate/auth/request-otp',
		array(
			'methods'             => 'POST',
			'callback'            => 'awsisa_delegate_request_otp',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/delegate/auth/verify-otp',
		array(
			'methods'             => 'POST',
			'callback'            => 'awsisa_delegate_verify_otp',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/delegate/auth/qr/(?P<token>[a-zA-Z0-9\-]+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'awsisa_delegate_qr_lookup',
			'permission_callback' => '__return_true',
			'args'                => array(
				'token' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'awsisa_register_delegate_routes' );

// ============================================================
// Helper: safe delegate fields to return to the PWA
// ============================================================

/**
 * Returns only the fields the PWA needs — excludes private notes,
 * dietary requirements, accessibility, payment details, etc.
 *
 * @param array $delegate Full delegate row from Supabase.
 * @return array Filtered delegate data.
 */
function awsisa_delegate_safe_fields( $delegate ) {
	return array(
		'id'             => $delegate['id']             ?? '',
		'first_name'     => $delegate['first_name']     ?? '',
		'last_name'      => $delegate['last_name']      ?? '',
		'email'          => $delegate['email']          ?? '',
		'organisation'   => $delegate['organisation']   ?? '',
		'delegate_type'  => $delegate['delegate_type']  ?? '',
		'country'        => $delegate['country']        ?? '',
		'qr_code_token'  => $delegate['qr_code_token']  ?? '',
		'seat_reference' => $delegate['seat_reference'] ?? null,
	);
}

// ============================================================
// Endpoint 1: POST /awsisa/v1/delegate/auth/request-otp
// ============================================================

/**
 * Generates a 6-digit OTP and emails it to the delegate.
 *
 * Rate limit: max 3 unused OTPs per email within the last 15 minutes.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_delegate_request_otp( WP_REST_Request $request ) {
	$body  = $request->get_json_params();
	$email = isset( $body['email'] ) ? sanitize_email( $body['email'] ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error(
			'invalid_email',
			__( 'A valid email address is required.', 'awsisa-events' ),
			array( 'status' => 400 )
		);
	}

	// 1. Confirm the email belongs to a registered delegate.
	$delegate_rows = awsisa_supabase(
		'delegates',
		'GET',
		array(),
		'email=eq.' . rawurlencode( $email ) . '&select=id,first_name,last_name,email&limit=1'
	);

	if ( is_wp_error( $delegate_rows ) ) {
		return new WP_Error(
			'supabase_error',
			$delegate_rows->get_error_message(),
			array( 'status' => 500 )
		);
	}

	if ( empty( $delegate_rows ) || empty( $delegate_rows[0]['id'] ) ) {
		// Don't reveal whether the email is registered — generic message.
		return new WP_REST_Response(
			array( 'message' => __( 'If that email is registered, you will receive a code shortly.', 'awsisa-events' ) ),
			200
		);
	}

	$delegate = $delegate_rows[0];

	// 2. Rate limit: max 3 valid OTPs per email in the last 15 minutes.
	$rate_check = awsisa_supabase(
		'delegate_otps',
		'GET',
		array(),
		'email=eq.' . rawurlencode( $email )
			. '&used_at=is.null'
			. '&expires_at=gt.' . rawurlencode( gmdate( 'Y-m-d\TH:i:s\Z' ) )
			. '&select=id&limit=4',
		true // service key
	);

	if ( ! is_wp_error( $rate_check ) && count( $rate_check ) >= 3 ) {
		return new WP_Error(
			'rate_limit',
			__( 'Too many requests. Please wait a few minutes before requesting a new code.', 'awsisa-events' ),
			array( 'status' => 429 )
		);
	}

	// 3. Generate a 6-digit code and hash it.
	$code      = (string) wp_rand( 100000, 999999 );
	$otp_hash  = password_hash( $code, PASSWORD_DEFAULT );

	// 4. Insert into delegate_otps (service key — no RLS).
	$insert = awsisa_supabase(
		'delegate_otps',
		'POST',
		array(
			'email'       => $email,
			'otp_hash'    => $otp_hash,
			'delegate_id' => $delegate['id'],
		),
		'',
		true // service key
	);

	if ( is_wp_error( $insert ) ) {
		return new WP_Error(
			'supabase_error',
			$insert->get_error_message(),
			array( 'status' => 500 )
		);
	}

	// 5. Send the OTP email.
	$sent = awsisa_send_otp_email(
		$email,
		$code,
		$delegate['first_name']
	);

	if ( ! $sent ) {
		// Log but don't block — the OTP is in the DB; the delegate can retry.
		error_log( 'AWSISA: OTP email failed to send to ' . $email );
	}

	return new WP_REST_Response(
		array( 'message' => __( 'If that email is registered, you will receive a code shortly.', 'awsisa-events' ) ),
		200
	);
}

// ============================================================
// Endpoint 2: POST /awsisa/v1/delegate/auth/verify-otp
// ============================================================

/**
 * Verifies a 6-digit OTP and returns the delegate row on success.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_delegate_verify_otp( WP_REST_Request $request ) {
	$body  = $request->get_json_params();
	$email = isset( $body['email'] ) ? sanitize_email( $body['email'] )      : '';
	$code  = isset( $body['code'] )  ? sanitize_text_field( $body['code'] )  : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error(
			'invalid_email',
			__( 'A valid email address is required.', 'awsisa-events' ),
			array( 'status' => 400 )
		);
	}

	if ( empty( $code ) || strlen( $code ) !== 6 || ! ctype_digit( $code ) ) {
		return new WP_Error(
			'invalid_code',
			__( 'Please enter a valid 6-digit code.', 'awsisa-events' ),
			array( 'status' => 400 )
		);
	}

	// 1. Fetch all unexpired, unused OTPs for this email (latest first).
	$now = gmdate( 'Y-m-d\TH:i:s\Z' );

	$otps = awsisa_supabase(
		'delegate_otps',
		'GET',
		array(),
		'email=eq.' . rawurlencode( $email )
			. '&used_at=is.null'
			. '&expires_at=gt.' . rawurlencode( $now )
			. '&select=id,otp_hash'
			. '&order=created_at.desc'
			. '&limit=5',
		true // service key
	);

	if ( is_wp_error( $otps ) ) {
		return new WP_Error(
			'supabase_error',
			$otps->get_error_message(),
			array( 'status' => 500 )
		);
	}

	// 2. Find a matching OTP via password_verify.
	$matched_id = null;

	if ( ! empty( $otps ) ) {
		foreach ( $otps as $otp_row ) {
			if ( ! empty( $otp_row['otp_hash'] ) && password_verify( $code, $otp_row['otp_hash'] ) ) {
				$matched_id = $otp_row['id'];
				break;
			}
		}
	}

	if ( null === $matched_id ) {
		return new WP_Error(
			'invalid_otp',
			__( 'Invalid or expired code. Please request a new one.', 'awsisa-events' ),
			array( 'status' => 401 )
		);
	}

	// 3. Mark the OTP as used.
	$used_now = gmdate( 'Y-m-d\TH:i:s\Z' );

	awsisa_supabase(
		'delegate_otps',
		'PATCH',
		array( 'used_at' => $used_now ),
		'id=eq.' . rawurlencode( $matched_id ),
		true // service key
	);

	// 4. Fetch the delegate row (safe fields only).
	$delegate_rows = awsisa_supabase(
		'delegates',
		'GET',
		array(),
		'email=eq.' . rawurlencode( $email )
			. '&select=id,first_name,last_name,email,organisation,delegate_type,country,qr_code_token,seat_reference'
			. '&limit=1'
	);

	if ( is_wp_error( $delegate_rows ) || empty( $delegate_rows[0] ) ) {
		return new WP_Error(
			'delegate_not_found',
			__( 'Delegate account not found.', 'awsisa-events' ),
			array( 'status' => 404 )
		);
	}

	return new WP_REST_Response(
		array(
			'delegate' => awsisa_delegate_safe_fields( $delegate_rows[0] ),
		),
		200
	);
}

// ============================================================
// Endpoint 3: GET /awsisa/v1/delegate/auth/qr/{token}
// ============================================================

/**
 * Looks up a delegate by their printed QR code token.
 *
 * Used by the PWA scanner: when delegate B scans delegate A's badge,
 * this returns A's public profile so B can save them as a contact.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response|WP_Error
 */
function awsisa_delegate_qr_lookup( WP_REST_Request $request ) {
	$token = $request->get_param( 'token' );

	if ( empty( $token ) ) {
		return new WP_Error(
			'missing_token',
			__( 'QR token is required.', 'awsisa-events' ),
			array( 'status' => 400 )
		);
	}

	$rows = awsisa_supabase(
		'delegates',
		'GET',
		array(),
		'qr_code_token=eq.' . rawurlencode( $token )
			. '&select=id,first_name,last_name,email,organisation,delegate_type,country,qr_code_token,seat_reference'
			. '&limit=1'
	);

	if ( is_wp_error( $rows ) ) {
		return new WP_Error(
			'supabase_error',
			$rows->get_error_message(),
			array( 'status' => 500 )
		);
	}

	if ( empty( $rows[0] ) ) {
		return new WP_Error(
			'not_found',
			__( 'No delegate found for this QR code.', 'awsisa-events' ),
			array( 'status' => 404 )
		);
	}

	return new WP_REST_Response(
		array(
			'delegate' => awsisa_delegate_safe_fields( $rows[0] ),
		),
		200
	);
}
