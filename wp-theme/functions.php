<?php
/**
 * Awsisa Watersan Dialogue 2026 - Theme Functions
 *
 * @package Awsisa
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// Theme Setup
// ============================================================

/**
 * Sets up theme defaults and registers support for WordPress features.
 */
function awsisa_setup() {
	load_theme_textdomain( 'awsisa', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Featured image sizes.
	add_image_size( 'awsisa-hero',     1920, 800, true );
	add_image_size( 'awsisa-card',     600,  400, true );
	add_image_size( 'awsisa-avatar',   200,  200, true );
	add_image_size( 'awsisa-sponsor',  400,  200, false );

	// Navigation menus.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Navigation', 'awsisa' ),
		'footer'  => esc_html__( 'Footer Navigation', 'awsisa' ),
	) );
}
add_action( 'after_setup_theme', 'awsisa_setup' );

// ============================================================
// Enqueue Scripts & Styles
// ============================================================

/**
 * Enqueues theme stylesheets and scripts.
 */
function awsisa_enqueue_assets() {
	$ver = wp_get_theme()->get( 'Version' );

	// Google Fonts: Outfit + Inter.
	wp_enqueue_style(
		'awsisa-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap',
		array(),
		null
	);

	// Main stylesheet (style.css contains all theme CSS).
	wp_enqueue_style(
		'awsisa-style',
		get_stylesheet_uri(),
		array( 'awsisa-fonts' ),
		$ver
	);

	// Main JavaScript.
	wp_enqueue_script(
		'awsisa-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		$ver,
		true
	);

	// Localize script for AJAX/API usage.
	wp_localize_script( 'awsisa-main', 'awsisaData', array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'restUrl'    => esc_url_raw( rest_url() ),
		'nonce'      => wp_create_nonce( 'awsisa_rest' ),
		'pluginUrl'  => defined( 'AWSISA_EVENTS_URL' ) ? AWSISA_EVENTS_URL : '',
		'supabaseUrl' => defined( 'AWSISA_SUPABASE_URL' ) ? AWSISA_SUPABASE_URL : '',
		'supabaseKey' => defined( 'AWSISA_SUPABASE_ANON_KEY' ) ? AWSISA_SUPABASE_ANON_KEY : '',
	) );
}
add_action( 'wp_enqueue_scripts', 'awsisa_enqueue_assets' );

// ============================================================
// Supabase Configuration
// ============================================================

/**
 * Supabase connection constants — define in wp-config.php or .env.
 * Example:
 *   define( 'AWSISA_SUPABASE_URL',      'https://xxxx.supabase.co' );
 *   define( 'AWSISA_SUPABASE_ANON_KEY', 'your-anon-key' );
 *   define( 'AWSISA_SUPABASE_SERVICE_KEY', 'your-service-role-key' );
 */
if ( ! defined( 'AWSISA_SUPABASE_URL' ) ) {
	define( 'AWSISA_SUPABASE_URL', '' );
}
if ( ! defined( 'AWSISA_SUPABASE_ANON_KEY' ) ) {
	define( 'AWSISA_SUPABASE_ANON_KEY', '' );
}
if ( ! defined( 'AWSISA_SUPABASE_SERVICE_KEY' ) ) {
	define( 'AWSISA_SUPABASE_SERVICE_KEY', '' );
}

// ============================================================
// Custom Post Types
// ============================================================

/**
 * Registers the Speakers custom post type.
 */
function awsisa_register_speakers_cpt() {
	$labels = array(
		'name'          => esc_html__( 'Speakers', 'awsisa' ),
		'singular_name' => esc_html__( 'Speaker', 'awsisa' ),
		'add_new_item'  => esc_html__( 'Add New Speaker', 'awsisa' ),
		'edit_item'     => esc_html__( 'Edit Speaker', 'awsisa' ),
		'search_items'  => esc_html__( 'Search Speakers', 'awsisa' ),
	);

	register_post_type( 'awsisa_speaker', array(
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-admin-users',
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'speakers' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'awsisa_register_speakers_cpt' );

/**
 * Registers the Sponsors custom post type.
 */
function awsisa_register_sponsors_cpt() {
	$labels = array(
		'name'          => esc_html__( 'Sponsors', 'awsisa' ),
		'singular_name' => esc_html__( 'Sponsor', 'awsisa' ),
		'add_new_item'  => esc_html__( 'Add New Sponsor', 'awsisa' ),
	);

	register_post_type( 'awsisa_sponsor', array(
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-building',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'sponsors' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'awsisa_register_sponsors_cpt' );

/**
 * Registers the Agenda Sessions custom post type.
 */
function awsisa_register_sessions_cpt() {
	$labels = array(
		'name'          => esc_html__( 'Agenda Sessions', 'awsisa' ),
		'singular_name' => esc_html__( 'Session', 'awsisa' ),
		'add_new_item'  => esc_html__( 'Add New Session', 'awsisa' ),
	);

	register_post_type( 'awsisa_session', array(
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'sessions' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'awsisa_register_sessions_cpt' );

// ============================================================
// Custom Meta Boxes (Speakers)
// ============================================================

/**
 * Adds meta boxes for speaker details.
 */
function awsisa_add_speaker_meta_boxes() {
	add_meta_box(
		'awsisa_speaker_details',
		esc_html__( 'Speaker Details', 'awsisa' ),
		'awsisa_speaker_details_callback',
		'awsisa_speaker',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'awsisa_add_speaker_meta_boxes' );

/**
 * Renders the speaker details meta box.
 *
 * @param WP_Post $post The current post object.
 */
function awsisa_speaker_details_callback( $post ) {
	wp_nonce_field( 'awsisa_speaker_nonce', 'awsisa_speaker_nonce_field' );

	$org         = get_post_meta( $post->ID, '_awsisa_speaker_org', true );
	$job_title   = get_post_meta( $post->ID, '_awsisa_speaker_title', true );
	$country     = get_post_meta( $post->ID, '_awsisa_speaker_country', true );
	$linkedin    = get_post_meta( $post->ID, '_awsisa_speaker_linkedin', true );
	$session_day = get_post_meta( $post->ID, '_awsisa_speaker_day', true );
	$is_keynote  = get_post_meta( $post->ID, '_awsisa_speaker_keynote', true );
	?>
	<table class="form-table">
		<tr>
			<th><?php esc_html_e( 'Organisation', 'awsisa' ); ?></th>
			<td><input type="text" name="awsisa_speaker_org" value="<?php echo esc_attr( $org ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Job Title', 'awsisa' ); ?></th>
			<td><input type="text" name="awsisa_speaker_title" value="<?php echo esc_attr( $job_title ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Country', 'awsisa' ); ?></th>
			<td><input type="text" name="awsisa_speaker_country" value="<?php echo esc_attr( $country ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'LinkedIn URL', 'awsisa' ); ?></th>
			<td><input type="url" name="awsisa_speaker_linkedin" value="<?php echo esc_attr( $linkedin ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Session Day', 'awsisa' ); ?></th>
			<td>
				<select name="awsisa_speaker_day">
					<option value=""><?php esc_html_e( 'Select day', 'awsisa' ); ?></option>
					<option value="day1" <?php selected( $session_day, 'day1' ); ?>><?php esc_html_e( 'Day 1 — 9 Nov', 'awsisa' ); ?></option>
					<option value="day2" <?php selected( $session_day, 'day2' ); ?>><?php esc_html_e( 'Day 2 — 10 Nov', 'awsisa' ); ?></option>
					<option value="day3" <?php selected( $session_day, 'day3' ); ?>><?php esc_html_e( 'Day 3 — 11 Nov', 'awsisa' ); ?></option>
					<option value="day4" <?php selected( $session_day, 'day4' ); ?>><?php esc_html_e( 'Day 4 — 12 Nov', 'awsisa' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Keynote Speaker?', 'awsisa' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="awsisa_speaker_keynote" value="1" <?php checked( $is_keynote, '1' ); ?> />
					<?php esc_html_e( 'Yes, this is a keynote speaker', 'awsisa' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Saves speaker meta box data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function awsisa_save_speaker_meta( $post_id ) {
	if ( ! isset( $_POST['awsisa_speaker_nonce_field'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awsisa_speaker_nonce_field'] ) ), 'awsisa_speaker_nonce' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'awsisa_speaker_org'     => '_awsisa_speaker_org',
		'awsisa_speaker_title'   => '_awsisa_speaker_title',
		'awsisa_speaker_country' => '_awsisa_speaker_country',
		'awsisa_speaker_day'     => '_awsisa_speaker_day',
	);

	foreach ( $fields as $post_key => $meta_key ) {
		if ( isset( $_POST[ $post_key ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) );
		}
	}

	if ( isset( $_POST['awsisa_speaker_linkedin'] ) ) {
		update_post_meta( $post_id, '_awsisa_speaker_linkedin', esc_url_raw( wp_unslash( $_POST['awsisa_speaker_linkedin'] ) ) );
	}

	update_post_meta( $post_id, '_awsisa_speaker_keynote', isset( $_POST['awsisa_speaker_keynote'] ) ? '1' : '0' );
}
add_action( 'save_post_awsisa_speaker', 'awsisa_save_speaker_meta' );

// ============================================================
// Widget Areas (Sidebars)
// ============================================================

/**
 * Registers widget areas.
 */
function awsisa_register_sidebars() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 1', 'awsisa' ),
		'id'            => 'footer-1',
		'before_widget' => '<div class="footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer__heading">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'awsisa_register_sidebars' );

// ============================================================
// Template Functions (used in template files)
// ============================================================

/**
 * Outputs the event dates string.
 *
 * @return string
 */
function awsisa_event_dates() {
	return esc_html__( '9 – 12 November 2026', 'awsisa' );
}

/**
 * Outputs the venue name.
 *
 * @return string
 */
function awsisa_venue() {
	return esc_html__( 'Emperors Palace, Johannesburg, South Africa', 'awsisa' );
}

/**
 * Outputs the registration URL (defaults to /register page).
 *
 * @return string
 */
function awsisa_register_url() {
	$page = get_page_by_path( 'register' );
	if ( $page ) {
		return get_permalink( $page->ID );
	}
	return home_url( '/register/' );
}

/**
 * Outputs the delegate type badge HTML.
 *
 * @param string $type Delegate type slug.
 * @return string Badge HTML.
 */
function awsisa_delegate_type_badge( $type ) {
	$labels = array(
		'government'     => esc_html__( 'Government', 'awsisa' ),
		'utility'        => esc_html__( 'Utility', 'awsisa' ),
		'private_sector' => esc_html__( 'Private Sector', 'awsisa' ),
		'ngo'            => esc_html__( 'NGO / NPO', 'awsisa' ),
		'academic'       => esc_html__( 'Academic', 'awsisa' ),
		'media'          => esc_html__( 'Media', 'awsisa' ),
		'exhibitor'      => esc_html__( 'Exhibitor', 'awsisa' ),
		'sponsor'        => esc_html__( 'Sponsor', 'awsisa' ),
	);
	$label = isset( $labels[ $type ] ) ? $labels[ $type ] : esc_html( $type );
	return '<span class="badge badge--primary">' . $label . '</span>';
}

/**
 * Returns delegate registration pricing by type.
 *
 * @param string $type Delegate type.
 * @return array price_zar and price_usd.
 */
function awsisa_delegate_pricing( $type ) {
	$pricing = array(
		'government'     => array( 'zar' => 4500,  'usd' => 250 ),
		'utility'        => array( 'zar' => 5500,  'usd' => 305 ),
		'private_sector' => array( 'zar' => 8500,  'usd' => 472 ),
		'ngo'            => array( 'zar' => 3500,  'usd' => 194 ),
		'academic'       => array( 'zar' => 2800,  'usd' => 155 ),
		'media'          => array( 'zar' => 0,     'usd' => 0 ),
		'exhibitor'      => array( 'zar' => 12000, 'usd' => 667 ),
		'sponsor'        => array( 'zar' => 0,     'usd' => 0 ),
	);
	return isset( $pricing[ $type ] ) ? $pricing[ $type ] : array( 'zar' => 0, 'usd' => 0 );
}

// ============================================================
// Supabase REST Helper (Server-side PHP calls)
// ============================================================

/**
 * Makes a request to the Supabase REST API.
 *
 * @param string $table  Table name.
 * @param string $method HTTP method: GET, POST, PATCH, DELETE.
 * @param array  $data   Data payload (for POST/PATCH).
 * @param string $query  URL query string (for GET, e.g. 'select=*&email=eq.foo@bar.com').
 * @param bool   $use_service_key Whether to use the service role key (bypasses RLS).
 * @return array|WP_Error Decoded response or WP_Error.
 */
function awsisa_supabase_request( $table, $method = 'GET', $data = array(), $query = '', $use_service_key = false ) {
	$base_url = rtrim( AWSISA_SUPABASE_URL, '/' );
	$api_key  = $use_service_key ? AWSISA_SUPABASE_SERVICE_KEY : AWSISA_SUPABASE_ANON_KEY;

	if ( empty( $base_url ) || empty( $api_key ) ) {
		return new WP_Error( 'awsisa_config', esc_html__( 'Supabase is not configured.', 'awsisa' ) );
	}

	$url = $base_url . '/rest/v1/' . esc_attr( $table );
	if ( ! empty( $query ) ) {
		$url .= '?' . $query;
	}

	$args = array(
		'method'  => $method,
		'headers' => array(
			'apikey'        => $api_key,
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type'  => 'application/json',
			'Prefer'        => 'return=representation',
		),
		'timeout' => 15,
	);

	if ( ! empty( $data ) && in_array( $method, array( 'POST', 'PATCH', 'PUT' ), true ) ) {
		$args['body'] = wp_json_encode( $data );
	}

	$response = wp_remote_request( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = wp_remote_retrieve_body( $response );
	$code = wp_remote_retrieve_response_code( $response );

	if ( $code >= 400 ) {
		$error_data = json_decode( $body, true );
		$message    = isset( $error_data['message'] ) ? $error_data['message'] : esc_html__( 'Unknown Supabase error.', 'awsisa' );
		return new WP_Error( 'supabase_error', $message, array( 'status' => $code ) );
	}

	return json_decode( $body, true );
}

// ============================================================
// Excerpt Length
// ============================================================

/**
 * Customises excerpt length.
 *
 * @param int $length Default excerpt length.
 * @return int
 */
function awsisa_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'awsisa_excerpt_length' );

/**
 * Customises excerpt "more" string.
 *
 * @param string $more Default more string.
 * @return string
 */
function awsisa_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'awsisa_excerpt_more' );

// ============================================================
// Body Classes
// ============================================================

/**
 * Adds custom body classes.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function awsisa_body_classes( $classes ) {
	if ( is_singular() ) {
		$classes[] = 'awsisa-singular';
	}
	if ( is_front_page() ) {
		$classes[] = 'awsisa-home';
	}
	return $classes;
}
add_filter( 'body_class', 'awsisa_body_classes' );

// ============================================================
// Disable Gutenberg for CPTs (use classic editor for simplicity)
// ============================================================

/**
 * Disables Gutenberg for custom post types that use meta boxes.
 *
 * @param bool   $use_block_editor Whether to use the block editor.
 * @param string $post_type        Post type slug.
 * @return bool
 */
function awsisa_disable_gutenberg_for_cpts( $use_block_editor, $post_type ) {
	$no_gutenberg = array( 'awsisa_speaker', 'awsisa_session' );
	if ( in_array( $post_type, $no_gutenberg, true ) ) {
		return false;
	}
	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'awsisa_disable_gutenberg_for_cpts', 10, 2 );
