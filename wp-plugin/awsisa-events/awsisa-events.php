<?php
/**
 * Plugin Name: Awsisa Events — Supabase Integration
 * Plugin URI:  https://www.awsisa-watersan-dialogue.org
 * Description: Core integration layer for the Awsisa Watersan Dialogue 2026. Provides REST API endpoints for delegate registration, accommodation bookings, donations, and NFC tap logging. Connects to Supabase as the database backend.
 * Version:     1.0.0
 * Author:      Lubabalo Web App Development
 * Author URI:  https://lubabalo.dev
 * License:     Private
 * Text Domain: awsisa-events
 * Requires at least: 6.2
 * Requires PHP: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWSISA_EVENTS_VERSION', '1.0.0' );
define( 'AWSISA_EVENTS_PATH',    plugin_dir_path( __FILE__ ) );
define( 'AWSISA_EVENTS_URL',     plugin_dir_url( __FILE__ ) );

// Supabase configuration (define these in wp-config.php).
if ( ! defined( 'AWSISA_SUPABASE_URL' ) )         define( 'AWSISA_SUPABASE_URL',          '' );
if ( ! defined( 'AWSISA_SUPABASE_ANON_KEY' ) )     define( 'AWSISA_SUPABASE_ANON_KEY',    '' );
if ( ! defined( 'AWSISA_SUPABASE_SERVICE_KEY' ) )  define( 'AWSISA_SUPABASE_SERVICE_KEY', '' );

// Load includes.
require_once AWSISA_EVENTS_PATH . 'includes/rest-api.php';
require_once AWSISA_EVENTS_PATH . 'includes/rest-contact.php';
require_once AWSISA_EVENTS_PATH . 'includes/shortcodes.php';
require_once AWSISA_EVENTS_PATH . 'includes/email.php';

/**
 * Plugin activation: flush rewrite rules.
 */
function awsisa_events_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'awsisa_events_activate' );

/**
 * Plugin deactivation: flush rewrite rules.
 */
function awsisa_events_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'awsisa_events_deactivate' );

// ============================================================
// Supabase REST Helper
// ============================================================

/**
 * Makes a request to the Supabase REST API.
 *
 * @param string $table           Table name.
 * @param string $method          HTTP method: GET, POST, PATCH, DELETE.
 * @param array  $data            Data payload for POST/PATCH.
 * @param string $query           URL query string.
 * @param bool   $use_service_key Whether to use the service role key.
 * @return array|WP_Error
 */
function awsisa_supabase( $table, $method = 'GET', $data = array(), $query = '', $use_service_key = false ) {
	$base_url = rtrim( AWSISA_SUPABASE_URL, '/' );
	$api_key  = $use_service_key ? AWSISA_SUPABASE_SERVICE_KEY : AWSISA_SUPABASE_ANON_KEY;

	if ( empty( $base_url ) || empty( $api_key ) ) {
		return new WP_Error( 'awsisa_config', __( 'Supabase credentials are not configured.', 'awsisa-events' ) );
	}

	$url  = $base_url . '/rest/v1/' . rawurlencode( $table );
	$url .= $query ? '?' . $query : '';

	$args = array(
		'method'  => strtoupper( $method ),
		'headers' => array(
			'apikey'        => $api_key,
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type'  => 'application/json',
			'Prefer'        => 'return=representation',
		),
		'timeout' => 15,
	);

	if ( ! empty( $data ) && in_array( strtoupper( $method ), array( 'POST', 'PATCH', 'PUT' ), true ) ) {
		$args['body'] = wp_json_encode( $data );
	}

	$response = wp_remote_request( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );
	$decoded = json_decode( $body, true );

	if ( $code >= 400 ) {
		$msg = is_array( $decoded ) && isset( $decoded['message'] )
			? $decoded['message']
			: sprintf( __( 'Supabase returned HTTP %d.', 'awsisa-events' ), $code );
		return new WP_Error( 'supabase_error', $msg, array( 'http_status' => $code ) );
	}

	return $decoded;
}

// ============================================================
// Admin Settings Page
// ============================================================

/**
 * Adds the plugin settings page to WP Admin.
 */
function awsisa_events_add_settings_page() {
	add_options_page(
		esc_html__( 'Awsisa Events Settings', 'awsisa-events' ),
		esc_html__( 'Awsisa Events', 'awsisa-events' ),
		'manage_options',
		'awsisa-events',
		'awsisa_events_settings_page_html'
	);
}
add_action( 'admin_menu', 'awsisa_events_add_settings_page' );

/**
 * Renders the settings page HTML.
 */
function awsisa_events_settings_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Connection status indicator.
	$status_msg  = '';
	$status_type = 'info';

	if ( ! empty( AWSISA_SUPABASE_URL ) && ! empty( AWSISA_SUPABASE_ANON_KEY ) ) {
		$test = awsisa_supabase( 'sponsors', 'GET', array(), 'select=id&limit=1' );
		if ( is_wp_error( $test ) ) {
			$status_msg  = sprintf( __( 'Connection failed: %s', 'awsisa-events' ), $test->get_error_message() );
			$status_type = 'error';
		} else {
			$status_msg  = __( '✓ Supabase connection is working.', 'awsisa-events' );
			$status_type = 'updated';
		}
	} else {
		$status_msg  = __( 'Supabase credentials are not configured. See instructions below.', 'awsisa-events' );
		$status_type = 'warning';
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Awsisa Events — Supabase Integration', 'awsisa-events' ); ?></h1>

		<div class="notice notice-<?php echo esc_attr( $status_type ); ?> inline">
			<p><?php echo esc_html( $status_msg ); ?></p>
		</div>

		<h2><?php esc_html_e( 'Configuration', 'awsisa-events' ); ?></h2>
		<p>
			<?php esc_html_e( 'Add the following constants to your', 'awsisa-events' ); ?>
			<code>wp-config.php</code>:
		</p>
		<pre style="background:#f0f0f1;padding:1rem;border-radius:4px;overflow:auto;">
define( 'AWSISA_SUPABASE_URL',         'https://your-project-id.supabase.co' );
define( 'AWSISA_SUPABASE_ANON_KEY',    'your-anon-public-key' );
define( 'AWSISA_SUPABASE_SERVICE_KEY', 'your-service-role-key' );
		</pre>

		<h2><?php esc_html_e( 'Available Shortcodes', 'awsisa-events' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Shortcode', 'awsisa-events' ); ?></th><th><?php esc_html_e( 'Description', 'awsisa-events' ); ?></th></tr></thead>
			<tbody>
				<tr><td><code>[awsisa_registration_form]</code></td><td><?php esc_html_e( 'Multi-step delegate registration form.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_donation_form]</code></td><td><?php esc_html_e( 'Legacy Initiative donation form.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_accommodation_list]</code></td><td><?php esc_html_e( 'Hotel package listing with booking forms.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_swag_bag]</code></td><td><?php esc_html_e( 'Sponsor brochure download gallery.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_agenda]</code></td><td><?php esc_html_e( 'Full 4-day conference agenda with tabs.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_sponsors]</code></td><td><?php esc_html_e( 'Tiered sponsor logo grid.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_delegate_card id="UUID"]</code></td><td><?php esc_html_e( 'NFC delegate contact card.', 'awsisa-events' ); ?></td></tr>
				<tr><td><code>[awsisa_booth_landing slug="slug"]</code></td><td><?php esc_html_e( 'NFC sponsor booth landing page.', 'awsisa-events' ); ?></td></tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'REST API Endpoints', 'awsisa-events' ); ?></h2>
		<p><?php esc_html_e( 'All endpoints are prefixed with:', 'awsisa-events' ); ?> <code><?php echo esc_url( rest_url( 'awsisa/v1/' ) ); ?></code></p>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Method', 'awsisa-events' ); ?></th><th><?php esc_html_e( 'Endpoint', 'awsisa-events' ); ?></th><th><?php esc_html_e( 'Description', 'awsisa-events' ); ?></th></tr></thead>
			<tbody>
				<tr><td>POST</td><td><code>/register</code></td><td><?php esc_html_e( 'Create a delegate, log POPIA consent, queue confirmation email.', 'awsisa-events' ); ?></td></tr>
				<tr><td>POST</td><td><code>/donate</code></td><td><?php esc_html_e( 'Create a donation record.', 'awsisa-events' ); ?></td></tr>
				<tr><td>POST</td><td><code>/accommodation/book</code></td><td><?php esc_html_e( 'Create an accommodation booking.', 'awsisa-events' ); ?></td></tr>
				<tr><td>POST</td><td><code>/nfc/tap</code></td><td><?php esc_html_e( 'Log an NFC tap event.', 'awsisa-events' ); ?></td></tr>
				<tr><td>POST</td><td><code>/payment/payfast</code></td><td><?php esc_html_e( 'PayFast ITN webhook handler.', 'awsisa-events' ); ?></td></tr>
				<tr><td>POST</td><td><code>/payment/peachpayments</code></td><td><?php esc_html_e( 'Peachpayments webhook handler.', 'awsisa-events' ); ?></td></tr>
				<tr><td>GET</td><td><code>/agenda</code></td><td><?php esc_html_e( 'Fetch published agenda sessions.', 'awsisa-events' ); ?></td></tr>
				<tr><td>GET</td><td><code>/sponsors</code></td><td><?php esc_html_e( 'Fetch active sponsors.', 'awsisa-events' ); ?></td></tr>
			</tbody>
		</table>
	</div>
	<?php
}
