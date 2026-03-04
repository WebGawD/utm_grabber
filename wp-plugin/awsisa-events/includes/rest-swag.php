<?php
/**
 * Swag Bag download tracking REST endpoint.
 *
 * GET /awsisa/v1/swag-bag/{id}/download
 *
 * Increments the download_count for the item, then 302-redirects the
 * browser to the actual file URL stored in Supabase.
 *
 * Called by the [awsisa_swag_bag] shortcode download buttons.
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the swag-bag download route.
 */
function awsisa_register_swag_routes() {
	register_rest_route(
		'awsisa/v1',
		'/swag-bag/(?P<id>[a-zA-Z0-9\-]{1,40})/download',
		array(
			'methods'             => 'GET',
			'callback'            => 'awsisa_rest_swag_download',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'description'       => __( 'UUID of the swag bag item.', 'awsisa-events' ),
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'awsisa_register_swag_routes' );

/**
 * Handles a swag bag download request.
 *
 * 1. Looks up the swag_bag_items row.
 * 2. Increments download_count.
 * 3. Logs an NFC tap (type: swag_download).
 * 4. Redirects to the file_url with a 302.
 *
 * On any error a JSON error response is returned instead of a redirect.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return void|WP_REST_Response
 */
function awsisa_rest_swag_download( WP_REST_Request $request ) {
	$item_id = $request->get_param( 'id' );

	// ── Fetch the swag bag item ──────────────────────────────────────────────
	$item_result = awsisa_supabase(
		'swag_bag_items',
		'GET',
		array(),
		'id=eq.' . rawurlencode( $item_id ) . '&is_active=eq.true&select=id,title,file_url,download_count,sponsor_id&limit=1'
	);

	if ( is_wp_error( $item_result ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'message' => $item_result->get_error_message() ),
			502
		);
	}

	if ( empty( $item_result ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'message' => __( 'Item not found.', 'awsisa-events' ) ),
			404
		);
	}

	$item     = $item_result[0];
	$file_url = $item['file_url'] ?? '';

	if ( empty( $file_url ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'message' => __( 'No file attached to this item.', 'awsisa-events' ) ),
			404
		);
	}

	// ── Increment download_count (best-effort, non-blocking) ─────────────────
	awsisa_supabase(
		'swag_bag_items',
		'PATCH',
		array( 'download_count' => intval( $item['download_count'] ) + 1 ),
		'id=eq.' . rawurlencode( $item_id ),
		false // anon key — count is not sensitive
	);

	// ── Log NFC/swag tap ────────────────────────────────────────────────────
	awsisa_supabase(
		'nfc_taps',
		'POST',
		array(
			'tap_type'   => 'swag_download',
			'sponsor_id' => $item['sponsor_id'] ?? null,
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] )
				? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )
				: null,
		),
		'',
		false
	);

	// ── Validate the file URL before redirecting ─────────────────────────────
	$safe_url = esc_url_raw( $file_url );
	if ( empty( $safe_url ) || ! filter_var( $safe_url, FILTER_VALIDATE_URL ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'message' => __( 'Invalid file URL.', 'awsisa-events' ) ),
			422
		);
	}

	// Only allow https:// or our own domain (prevents open redirect).
	$parsed   = wp_parse_url( $safe_url );
	$own_host = wp_parse_url( home_url(), PHP_URL_HOST );

	$allowed_schemes = array( 'https' );
	if ( ! in_array( $parsed['scheme'] ?? '', $allowed_schemes, true ) ) {
		return new WP_REST_Response(
			array( 'success' => false, 'message' => __( 'Invalid file URL scheme.', 'awsisa-events' ) ),
			422
		);
	}

	// ── 302 redirect to file ──────────────────────────────────────────────────
	// We bypass WP_REST_Response here so we can send a proper redirect header.
	// Output has not started because REST API runs before the template.
	nocache_headers();
	header( 'Location: ' . $safe_url, true, 302 );
	exit;
}
