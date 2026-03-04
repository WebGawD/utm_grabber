<?php
/**
 * WordPress Shortcodes for Awsisa Events.
 *
 * Each shortcode renders the appropriate content, pulling live data
 * from Supabase via the awsisa_supabase() helper.
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// [awsisa_agenda]
// ============================================================

/**
 * Renders the full 4-day conference agenda.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function awsisa_shortcode_agenda( $atts ) {
	$atts = shortcode_atts( array( 'show_breaks' => 'yes' ), $atts, 'awsisa_agenda' );

	$sessions = awsisa_supabase( 'agenda_sessions', 'GET', array(), 'is_published=eq.true&order=day.asc,sort_order.asc&select=*' );

	if ( is_wp_error( $sessions ) || empty( $sessions ) ) {
		return '<p class="notice notice--warning">' . esc_html__( 'Agenda coming soon.', 'awsisa-events' ) . '</p>';
	}

	// Group sessions by day.
	$days = array();
	foreach ( $sessions as $session ) {
		$days[ $session['day'] ][] = $session;
	}

	$day_labels = array(
		'2026-11-09' => __( 'Day 1 · 9 November', 'awsisa-events' ),
		'2026-11-10' => __( 'Day 2 · 10 November', 'awsisa-events' ),
		'2026-11-11' => __( 'Day 3 · 11 November', 'awsisa-events' ),
		'2026-11-12' => __( 'Day 4 · 12 November', 'awsisa-events' ),
	);

	ob_start();
	?>
	<div class="awsisa-agenda">
		<div class="agenda-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Agenda by day', 'awsisa-events' ); ?>">
			<?php $first = true; foreach ( $days as $date => $_ ) : ?>
				<button
					class="agenda-tab <?php echo $first ? 'is-active' : ''; ?>"
					role="tab"
					aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
					aria-controls="agenda-panel-<?php echo esc_attr( str_replace( '-', '', $date ) ); ?>"
					id="agenda-tab-<?php echo esc_attr( str_replace( '-', '', $date ) ); ?>"
					data-day="<?php echo esc_attr( $date ); ?>"
				>
					<?php echo esc_html( $day_labels[ $date ] ?? $date ); ?>
				</button>
				<?php $first = false; endforeach; ?>
		</div>

		<div id="agenda-panels">
			<?php $first = true; foreach ( $days as $date => $day_sessions ) : ?>
				<div
					id="agenda-panel-<?php echo esc_attr( str_replace( '-', '', $date ) ); ?>"
					role="tabpanel"
					aria-labelledby="agenda-tab-<?php echo esc_attr( str_replace( '-', '', $date ) ); ?>"
					class="agenda-day-panel"
					<?php echo $first ? '' : 'hidden'; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				>
					<?php foreach ( $day_sessions as $session ) :
						$is_break   = in_array( $session['session_type'], array( 'break', 'networking' ), true );
						if ( $is_break && 'no' === $atts['show_breaks'] ) continue;
						?>
						<div class="agenda-session <?php echo $is_break ? 'agenda-session--break' : ''; ?>">
							<div class="agenda-session__time">
								<?php echo esc_html( substr( $session['start_time'], 0, 5 ) . ' – ' . substr( $session['end_time'], 0, 5 ) ); ?>
							</div>
							<div>
								<div class="agenda-session__title">
									<?php echo esc_html( $session['title'] ); ?>
									<?php if ( 'keynote' === $session['session_type'] ) : ?>
										<span class="badge badge--accent"><?php esc_html_e( 'Keynote', 'awsisa-events' ); ?></span>
									<?php elseif ( 'panel' === $session['session_type'] ) : ?>
										<span class="badge badge--primary"><?php esc_html_e( 'Panel', 'awsisa-events' ); ?></span>
									<?php elseif ( 'workshop' === $session['session_type'] ) : ?>
										<span class="badge badge--earth"><?php esc_html_e( 'Workshop', 'awsisa-events' ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( ! empty( $session['speaker_name'] ) ) : ?>
									<div class="agenda-session__speaker">
										<?php echo esc_html( $session['speaker_name'] ); ?>
										<?php if ( ! empty( $session['speaker_org'] ) ) : ?>
											· <?php echo esc_html( $session['speaker_org'] ); ?>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $session['room'] ) ) : ?>
									<div class="agenda-session__speaker" style="margin-top: 0.25rem; font-size: 0.8rem;">
										📍 <?php echo esc_html( $session['room'] ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php $first = false; endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'awsisa_agenda', 'awsisa_shortcode_agenda' );

// ============================================================
// [awsisa_sponsors]
// ============================================================

/**
 * Renders the tiered sponsor logo grid.
 *
 * @return string HTML output.
 */
function awsisa_shortcode_sponsors() {
	$sponsors = awsisa_supabase( 'sponsors', 'GET', array(), 'is_active=eq.true&order=sort_order.asc&select=*' );

	if ( is_wp_error( $sponsors ) || empty( $sponsors ) ) {
		return '<p>' . esc_html__( 'Sponsors to be announced.', 'awsisa-events' ) . '</p>';
	}

	$tiers = array( 'platinum', 'gold', 'silver', 'bronze', 'exhibitor', 'partner' );
	$tier_labels = array(
		'platinum' => __( 'Platinum Partners', 'awsisa-events' ),
		'gold'     => __( 'Gold Partners', 'awsisa-events' ),
		'silver'   => __( 'Silver Partners', 'awsisa-events' ),
		'bronze'   => __( 'Bronze Sponsors', 'awsisa-events' ),
		'exhibitor' => __( 'Exhibitors', 'awsisa-events' ),
		'partner'  => __( 'Supporting Partners', 'awsisa-events' ),
	);

	ob_start();
	foreach ( $tiers as $tier ) :
		$tier_sponsors = array_filter( $sponsors, fn( $s ) => $s['tier'] === $tier );
		if ( empty( $tier_sponsors ) ) continue;
		?>
		<div class="sponsors-tier">
			<div class="sponsors-tier__label">
				<span class="badge badge--<?php echo esc_attr( $tier ); ?>"><?php echo esc_html( $tier_labels[ $tier ] ?? ucfirst( $tier ) ); ?></span>
			</div>
			<div class="sponsors-tier__grid">
				<?php foreach ( $tier_sponsors as $sponsor ) : ?>
					<a
						href="<?php echo ! empty( $sponsor['website_url'] ) ? esc_url( $sponsor['website_url'] ) : '#'; ?>"
						class="sponsor-logo"
						target="_blank"
						rel="noopener noreferrer"
						title="<?php echo esc_attr( $sponsor['name'] ); ?>"
					>
						<?php if ( ! empty( $sponsor['logo_url'] ) ) : ?>
							<img src="<?php echo esc_url( $sponsor['logo_url'] ); ?>" alt="<?php echo esc_attr( $sponsor['name'] ); ?>" loading="lazy">
						<?php else : ?>
							<span style="font-weight: 700; color: #334155;"><?php echo esc_html( $sponsor['name'] ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach;
	return ob_get_clean();
}
add_shortcode( 'awsisa_sponsors', 'awsisa_shortcode_sponsors' );

// ============================================================
// [awsisa_swag_bag]
// ============================================================

/**
 * Renders the digital swag bag brochure gallery.
 *
 * @return string HTML output.
 */
function awsisa_shortcode_swag_bag() {
	$items = awsisa_supabase(
		'swag_bag_items',
		'GET',
		array(),
		'is_active=eq.true&order=created_at.desc&select=*,sponsor:sponsors(name,logo_url)'
	);

	if ( is_wp_error( $items ) || empty( $items ) ) {
		return '<p class="notice notice--info">' . esc_html__( 'Digital brochures will be available closer to the event.', 'awsisa-events' ) . '</p>';
	}

	ob_start();
	?>
	<div class="swag-grid">
		<?php foreach ( $items as $item ) : ?>
			<div class="card">
				<div class="card__body">
					<div class="swag-card__icon">
						<?php echo 'pdf' === $item['file_type'] ? '📄' : '🖼'; // PHPCS:Ignore ?>
					</div>
					<h3 style="font-size: 1rem; margin-bottom: 0.5rem;"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php if ( ! empty( $item['sponsor']['name'] ) ) : ?>
						<p style="font-size: 0.85rem; color: #64748B; margin-bottom: 0.75rem;">
							<?php echo esc_html( $item['sponsor']['name'] ); ?>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $item['description'] ) ) : ?>
						<p style="font-size: 0.875rem; color: #475569;"><?php echo esc_html( $item['description'] ); ?></p>
					<?php endif; ?>
					<div class="swag-card__downloads" style="margin: 0.75rem 0 1rem;">
						<?php
						printf(
							/* translators: %d: download count */
							esc_html( _n( '%d download', '%d downloads', $item['download_count'], 'awsisa-events' ) ),
							intval( $item['download_count'] )
						);
						?>
						<?php if ( $item['file_size_kb'] ) : ?>
							· <?php echo esc_html( number_format( $item['file_size_kb'] ) ); ?> KB
						<?php endif; ?>
					</div>
					<a
						href="<?php echo esc_url( rest_url( 'awsisa/v1/swag-bag/' . $item['id'] . '/download' ) ); ?>"
						class="btn btn--primary btn--sm"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'Download', 'awsisa-events' ); ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'awsisa_swag_bag', 'awsisa_shortcode_swag_bag' );

// ============================================================
// [awsisa_accommodation_list]
// ============================================================

/**
 * Renders available hotel packages with a booking CTA.
 *
 * @return string HTML output.
 */
function awsisa_shortcode_accommodation_list() {
	$packages = awsisa_supabase( 'accommodation_packages', 'GET', array(), 'is_active=eq.true&order=sort_order.asc&select=*' );

	if ( is_wp_error( $packages ) || empty( $packages ) ) {
		return '<p>' . esc_html__( 'Accommodation packages coming soon.', 'awsisa-events' ) . '</p>';
	}

	ob_start();
	echo '<div class="package-grid">';
	foreach ( $packages as $pkg ) :
		$available = $pkg['total_rooms'] - $pkg['booked_count'];
		?>
		<div class="card">
			<?php if ( ! empty( $pkg['image_url'] ) ) : ?>
				<img src="<?php echo esc_url( $pkg['image_url'] ); ?>" alt="<?php echo esc_attr( $pkg['hotel_name'] ); ?>" class="card__image" loading="lazy">
			<?php endif; ?>
			<div class="card__body">
				<span class="badge badge--primary" style="margin-bottom: 0.75rem;">
					<?php echo esc_html( $pkg['hotel_name'] ); ?>
				</span>
				<h3 style="margin-bottom: 0.5rem; font-size: 1.125rem;"><?php echo esc_html( $pkg['name'] ); ?></h3>
				<div class="package-card__price">
					R <?php echo esc_html( number_format( $pkg['price_zar'] ) ); ?>
					<span class="package-card__price-sub"> / <?php echo esc_html( $pkg['nights'] ); ?> nights</span>
				</div>
				<?php if ( ! empty( $pkg['amenities'] ) ) : ?>
					<ul class="package-card__amenities">
						<?php foreach ( $pkg['amenities'] as $amenity ) : ?>
							<li><?php echo esc_html( $amenity ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $available > 0 ) : ?>
					<a href="<?php echo esc_url( home_url( '/accommodation/' . $pkg['id'] . '/' ) ); ?>" class="btn btn--primary" style="margin-top: 1rem;">
						<?php esc_html_e( 'Book Now', 'awsisa-events' ); ?>
					</a>
					<p style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
						<?php printf(
							/* translators: %d: available rooms */
							esc_html( _n( '%d room remaining', '%d rooms remaining', $available, 'awsisa-events' ) ),
							intval( $available )
						); ?>
					</p>
				<?php else : ?>
					<button disabled class="btn btn--outline" style="margin-top: 1rem; opacity: 0.5; cursor: not-allowed;">
						<?php esc_html_e( 'Fully Booked', 'awsisa-events' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach;
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'awsisa_accommodation_list', 'awsisa_shortcode_accommodation_list' );

// ============================================================
// [awsisa_delegate_card id="UUID"]
// ============================================================

/**
 * Renders an NFC delegate contact card.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function awsisa_shortcode_delegate_card( $atts ) {
	$atts = shortcode_atts( array( 'id' => '' ), $atts, 'awsisa_delegate_card' );

	if ( empty( $atts['id'] ) ) {
		return '';
	}

	$delegate_id = sanitize_text_field( $atts['id'] );

	// Log the tap (anonymously).
	awsisa_supabase( 'nfc_taps', 'POST', array(
		'tap_type'    => 'delegate_view',
		'delegate_id' => $delegate_id,
	) );

	// Fetch public-safe fields only.
	$result = awsisa_supabase(
		'delegates',
		'GET',
		array(),
		'id=eq.' . rawurlencode( $delegate_id ) . '&profile_public=eq.true&select=first_name,last_name,organisation,job_title,country,delegate_type&limit=1'
	);

	if ( is_wp_error( $result ) || empty( $result ) ) {
		return '<p>' . esc_html__( 'Profile not found or not publicly visible.', 'awsisa-events' ) . '</p>';
	}

	$d = $result[0];

	ob_start();
	?>
	<div style="max-width: 420px; margin: 2rem auto; text-align: center;">
		<div class="card" style="padding: 2.5rem;">
			<div style="width: 80px; height: 80px; background: var(--clr-primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; font-weight: 800; color: var(--clr-primary-dark);">
				<?php echo esc_html( mb_strtoupper( mb_substr( $d['first_name'], 0, 1 ) . mb_substr( $d['last_name'], 0, 1 ) ) ); ?>
			</div>
			<h2 style="margin-bottom: 0.25rem;"><?php echo esc_html( $d['first_name'] . ' ' . $d['last_name'] ); ?></h2>
			<?php if ( ! empty( $d['job_title'] ) ) : ?>
				<p style="color: #64748B; margin-bottom: 0.25rem;"><?php echo esc_html( $d['job_title'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $d['organisation'] ) ) : ?>
				<p style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo esc_html( $d['organisation'] ); ?></p>
			<?php endif; ?>
			<p style="color: #64748B;"><?php echo esc_html( $d['country'] ); ?></p>
			<div style="margin: 1.5rem 0;">
				<span class="badge badge--primary">AWSISA Watersan 2026</span>
			</div>
			<!-- vCard download button (JS-generated) -->
			<button
				class="btn btn--primary btn--sm"
				onclick="awsisaDownloadVCard(<?php echo esc_attr( wp_json_encode( $d ) ); ?>)"
			>
				<?php esc_html_e( 'Save Contact', 'awsisa-events' ); ?>
			</button>
		</div>
	</div>
	<script>
	function awsisaDownloadVCard(d) {
		var vcard = [
			'BEGIN:VCARD', 'VERSION:3.0',
			'FN:' + d.first_name + ' ' + d.last_name,
			'N:' + d.last_name + ';' + d.first_name + ';;;',
			d.organisation ? 'ORG:' + d.organisation : '',
			d.job_title ? 'TITLE:' + d.job_title : '',
			'NOTE:AWSISA Africa Water & Sanitation Dialogue 2026',
			'END:VCARD'
		].filter(Boolean).join('\r\n');
		var blob = new Blob([vcard], { type: 'text/vcard' });
		var a = document.createElement('a');
		a.href = URL.createObjectURL(blob);
		a.download = d.first_name + '_' + d.last_name + '.vcf';
		a.click();
	}
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'awsisa_delegate_card', 'awsisa_shortcode_delegate_card' );

// ============================================================
// [awsisa_booth_landing slug="sponsor-slug"]
// ============================================================

/**
 * Renders a sponsor NFC booth landing page.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function awsisa_shortcode_booth_landing( $atts ) {
	$atts = shortcode_atts( array( 'slug' => '' ), $atts, 'awsisa_booth_landing' );

	if ( empty( $atts['slug'] ) ) {
		return '';
	}

	$slug = sanitize_text_field( $atts['slug'] );

	$sponsors = awsisa_supabase( 'sponsors', 'GET', array(), 'booth_nfc_slug=eq.' . rawurlencode( $slug ) . '&is_active=eq.true&select=*&limit=1' );

	if ( is_wp_error( $sponsors ) || empty( $sponsors ) ) {
		return '<p>' . esc_html__( 'Sponsor booth not found.', 'awsisa-events' ) . '</p>';
	}

	$sponsor = $sponsors[0];

	// Log the booth tap.
	awsisa_supabase( 'nfc_taps', 'POST', array(
		'tap_type'   => 'booth_tap',
		'sponsor_id' => $sponsor['id'],
	) );

	// Fetch active swag items for this sponsor.
	$items = awsisa_supabase(
		'swag_bag_items',
		'GET',
		array(),
		'sponsor_id=eq.' . rawurlencode( $sponsor['id'] ) . '&is_active=eq.true&select=*'
	);

	ob_start();
	?>
	<div style="max-width: 640px; margin: 0 auto;">
		<!-- Sponsor header -->
		<div class="card" style="text-align: center; padding: 2rem; margin-bottom: 2rem;">
			<?php if ( ! empty( $sponsor['logo_url'] ) ) : ?>
				<img src="<?php echo esc_url( $sponsor['logo_url'] ); ?>" alt="<?php echo esc_attr( $sponsor['name'] ); ?>" style="max-height: 80px; margin: 0 auto 1.5rem;" loading="eager">
			<?php endif; ?>
			<h2><?php echo esc_html( $sponsor['name'] ); ?></h2>
			<?php if ( ! empty( $sponsor['description'] ) ) : ?>
				<p style="color: #64748B; margin-top: 0.75rem;"><?php echo esc_html( $sponsor['description'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $sponsor['website_url'] ) ) : ?>
				<a href="<?php echo esc_url( $sponsor['website_url'] ); ?>" class="btn btn--outline" style="margin-top: 1rem;" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Visit Website', 'awsisa-events' ); ?>
					↗
				</a>
			<?php endif; ?>
		</div>

		<!-- Swag bag items -->
		<?php if ( ! is_wp_error( $items ) && ! empty( $items ) ) : ?>
			<h3 style="margin-bottom: 1rem;"><?php esc_html_e( 'Resources & Downloads', 'awsisa-events' ); ?></h3>
			<div class="swag-grid">
				<?php foreach ( $items as $item ) : ?>
					<div class="card">
						<div class="card__body">
							<h4 style="margin-bottom: 0.5rem;"><?php echo esc_html( $item['title'] ); ?></h4>
							<?php if ( ! empty( $item['description'] ) ) : ?>
								<p style="font-size: 0.875rem; color: #64748B;"><?php echo esc_html( $item['description'] ); ?></p>
							<?php endif; ?>
							<a href="<?php echo esc_url( $item['file_url'] ); ?>" class="btn btn--primary btn--sm" style="margin-top: 1rem;" target="_blank" rel="noopener noreferrer" download>
								📥 <?php esc_html_e( 'Download', 'awsisa-events' ); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'awsisa_booth_landing', 'awsisa_shortcode_booth_landing' );
