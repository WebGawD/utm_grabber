<?php
/**
 * Template Name: Accommodation Booking
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

// Enqueue the accommodation booking app.
wp_enqueue_script(
	'awsisa-accommodation-app',
	get_template_directory_uri() . '/assets/js/accommodation-app.js',
	array(),
	'1.0.0',
	true
);
wp_localize_script(
	'awsisa-accommodation-app',
	'awsisaAccom',
	array(
		'supabaseUrl'  => defined( 'AWSISA_SUPABASE_URL' ) ? AWSISA_SUPABASE_URL : '',
		'supabaseKey'  => defined( 'AWSISA_SUPABASE_ANON_KEY' ) ? AWSISA_SUPABASE_ANON_KEY : '',
		'restUrl'      => rest_url( 'awsisa/v1' ),
		'nonce'        => wp_create_nonce( 'wp_rest' ),
		'successUrl'   => home_url( '/accommodation/confirmation/' ),
		'privacyUrl'   => get_privacy_policy_url(),
	)
);
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#115E59 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Watersan Dialogue 2026</p>
		<h1 class="page-hero__title">Conference Accommodation</h1>
		<p class="page-hero__subtitle">Exclusive rates at Emperors Palace &amp; partner hotels for registered delegates.</p>
	</div>
</section>

<!-- React mount point -->
<div id="accommodation-app" style="min-height:60vh;">

	<!-- SSR fallback shown until JS hydrates -->
	<section class="section">
		<div class="container" style="max-width:900px;">

			<div class="section__header">
				<h2 class="section__title">Choose Your Package</h2>
				<p class="section__subtitle">All rates are per room per night and include breakfast. Conference shuttle runs every 30 minutes between Birchwood and Emperors Palace.</p>
			</div>

			<?php
			// Fetch packages server-side as fallback.
			$packages = array();
			if ( defined( 'AWSISA_SUPABASE_URL' ) ) {
				$response = awsisa_supabase_request(
					'accommodation_packages',
					'GET',
					array(),
					'is_active=eq.true&order=price_zar.asc'
				);
				if ( ! is_wp_error( $response ) ) {
					$packages = is_array( $response ) ? $response : array();
				}
			}

			if ( empty( $packages ) ) :
				// Hardcoded fallback.
				$packages = array(
					array(
						'id'                  => '',
						'hotel_name'          => 'Emperors Palace — Standard Room',
						'description'         => 'Elegant standard room with garden view, conference Wi-Fi, and daily breakfast.',
						'price_zar' => 2400,
						'price_usd' => 130,
						'total_rooms'         => 150,
						'booked_count'        => 0,
						'amenities'           => array( 'Breakfast', 'Wi-Fi', 'Parking', 'Pool access' ),
						'tier'                => 'standard',
					),
					array(
						'id'                  => '',
						'hotel_name'          => 'Emperors Palace — Deluxe Room',
						'description'         => 'Spacious deluxe room with casino resort views, lounge access, and premium amenities.',
						'price_zar' => 3200,
						'price_usd' => 175,
						'total_rooms'         => 80,
						'booked_count'        => 0,
						'amenities'           => array( 'Breakfast', 'Wi-Fi', 'Lounge access', 'Parking', 'Spa discount' ),
						'tier'                => 'deluxe',
					),
					array(
						'id'                  => '',
						'hotel_name'          => 'Birchwood Hotel — Standard Room',
						'description'         => 'Comfortable budget option 15 min from venue. Shuttle included.',
						'price_zar' => 1400,
						'price_usd' => 76,
						'total_rooms'         => 120,
						'booked_count'        => 0,
						'amenities'           => array( 'Breakfast', 'Wi-Fi', 'Conference shuttle', 'Parking' ),
						'tier'                => 'standard',
					),
				);
			endif;
			?>

			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;margin-bottom:3rem;">
				<?php foreach ( $packages as $pkg ) :
					$available = ( $pkg['total_rooms'] - $pkg['booked_count'] );
					$sold_out  = $available <= 0;
					$urgent    = ! $sold_out && $available <= 15;
					$amenities = is_array( $pkg['amenities'] ) ? $pkg['amenities'] : json_decode( $pkg['amenities'] ?? '[]', true );
					?>
					<div class="card" style="border:2px solid <?php echo $sold_out ? '#E2E8F0' : '#99F6E4'; ?>;position:relative;">
						<?php if ( $urgent ) : ?>
							<span class="badge badge--warning" style="position:absolute;top:1rem;right:1rem;">Only <?php echo esc_html( $available ); ?> left</span>
						<?php endif; ?>
						<?php if ( $sold_out ) : ?>
							<span class="badge badge--error" style="position:absolute;top:1rem;right:1rem;">Sold Out</span>
						<?php endif; ?>

						<h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0 0 .5rem;"><?php echo esc_html( $pkg['hotel_name'] ); ?></h3>
						<p style="font-size:.875rem;color:#475569;margin:0 0 1rem;"><?php echo esc_html( $pkg['description'] ); ?></p>

						<div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
							<?php foreach ( (array) $amenities as $amenity ) : ?>
								<span class="badge badge--primary" style="font-size:.75rem;"><?php echo esc_html( $amenity ); ?></span>
							<?php endforeach; ?>
						</div>

						<div style="display:flex;align-items:flex-end;justify-content:space-between;border-top:1px solid #F1F5F9;padding-top:1rem;">
							<div>
								<div style="font-size:1.5rem;font-weight:800;color:#0D9488;">R <?php echo esc_html( number_format( $pkg['price_zar'] ) ); ?></div>
								<div style="font-size:.8rem;color:#94A3B8;">per night &nbsp;·&nbsp; ~$<?php echo esc_html( $pkg['price_usd'] ); ?> USD</div>
							</div>
							<?php if ( ! $sold_out && ! empty( $pkg['id'] ) ) : ?>
								<button class="btn btn--primary js-book-room" data-package-id="<?php echo esc_attr( $pkg['id'] ); ?>" data-hotel="<?php echo esc_attr( $pkg['hotel_name'] ); ?>" data-price="<?php echo esc_attr( $pkg['price_zar'] ); ?>">Book Now</button>
							<?php elseif ( $sold_out ) : ?>
								<button class="btn btn--outline" disabled>Unavailable</button>
							<?php else : ?>
								<button class="btn btn--primary" disabled>Loading…</button>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Booking form (shown when JS unavailable) -->
			<noscript>
				<div class="card" style="max-width:580px;margin:0 auto;">
					<h3 style="font-size:1.25rem;font-weight:700;color:#0F172A;margin:0 0 1.5rem;">Reserve Your Room</h3>
					<form method="post" action="<?php echo esc_url( rest_url( 'awsisa/v1/accommodation/book' ) ); ?>">
						<?php wp_nonce_field( 'awsisa_book_accommodation', 'awsisa_nonce' ); ?>
						<div class="form-group">
							<label class="form-label" for="acc-delegate-id">Delegate ID / Email</label>
							<input type="text" id="acc-delegate-id" name="delegate_identifier" class="form-control" placeholder="Your registration email" required>
						</div>
						<div class="form-group">
							<label class="form-label" for="acc-checkin">Check-in Date</label>
							<input type="date" id="acc-checkin" name="check_in_date" class="form-control" value="2026-11-08" min="2026-11-08" max="2026-11-13" required>
						</div>
						<div class="form-group">
							<label class="form-label" for="acc-checkout">Check-out Date</label>
							<input type="date" id="acc-checkout" name="check_out_date" class="form-control" value="2026-11-13" min="2026-11-09" max="2026-11-14" required>
						</div>
						<div class="form-group">
							<label class="form-label" for="acc-notes">Special Requests <span style="color:#94A3B8;font-weight:400;">(optional)</span></label>
							<textarea id="acc-notes" name="notes" class="form-control" rows="3" placeholder="Accessibility needs, dietary requirements, etc."></textarea>
						</div>
						<button type="submit" class="btn btn--primary" style="width:100%;">Request Booking</button>
					</form>
				</div>
			</noscript>

			<!-- Info strip -->
			<div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:1.25rem 1.5rem;margin-top:2rem;">
				<h4 style="color:#1D4ED8;font-size:.9rem;font-weight:700;margin:0 0 .5rem;">📌 Booking Information</h4>
				<ul style="color:#1E40AF;font-size:.875rem;margin:0;padding-left:1.25rem;line-height:1.9;">
					<li>Bookings require a confirmed delegate registration</li>
					<li>Payment is settled directly with the hotel at check-in</li>
					<li>Cancellations accepted up to <strong>31 October 2026</strong></li>
					<li>Conference shuttle departs every 30 min from Birchwood from 07:00–20:00</li>
					<li>Contact <a href="mailto:accommodation@afriwater-san.africa" style="color:#0D9488;">accommodation@afriwater-san.africa</a> for group bookings (10+ rooms)</li>
				</ul>
			</div>

		</div>
	</section>

</div><!-- #accommodation-app -->

<?php get_footer(); ?>
