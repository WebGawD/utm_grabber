<?php
/**
 * The front page template.
 *
 * @package Awsisa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero" aria-labelledby="hero-title">
	<div class="container">
		<div class="hero__content">

			<span class="hero__eyebrow">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
					<path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
				</svg>
				<?php esc_html_e( 'ICC Durban · 9–12 November 2026', 'awsisa' ); ?>
			</span>

			<h1 class="hero__title" id="hero-title">
				<?php esc_html_e( 'AWSISA Africa &', 'awsisa' ); ?><br>
				<span><?php esc_html_e( 'Global South', 'awsisa' ); ?></span><br>
				<?php esc_html_e( 'Water & Sanitation Dialogue', 'awsisa' ); ?>
			</h1>

			<p class="hero__subtitle">
				<?php esc_html_e( 'Water security in Africa will take centre stage at this landmark gathering — bringing together 5,000+ water and sanitation stakeholders from across the Global South value chain.', 'awsisa' ); ?>
			</p>

			<!-- Countdown Timer -->
			<div class="countdown" id="countdown" aria-label="<?php esc_attr_e( 'Time until the event', 'awsisa' ); ?>">
				<div class="countdown__unit">
					<span class="countdown__number" id="countdown-days">--</span>
					<span class="countdown__label"><?php esc_html_e( 'Days', 'awsisa' ); ?></span>
				</div>
				<div class="countdown__unit">
					<span class="countdown__number" id="countdown-hours">--</span>
					<span class="countdown__label"><?php esc_html_e( 'Hours', 'awsisa' ); ?></span>
				</div>
				<div class="countdown__unit">
					<span class="countdown__number" id="countdown-minutes">--</span>
					<span class="countdown__label"><?php esc_html_e( 'Minutes', 'awsisa' ); ?></span>
				</div>
				<div class="countdown__unit">
					<span class="countdown__number" id="countdown-seconds">--</span>
					<span class="countdown__label"><?php esc_html_e( 'Seconds', 'awsisa' ); ?></span>
				</div>
			</div>

			<div class="hero__actions">
				<a href="<?php echo esc_url( awsisa_register_url() ); ?>" class="btn btn--accent btn--lg">
					<?php esc_html_e( 'Register as Delegate', 'awsisa' ); ?>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
				<a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>" class="btn btn--outline-white btn--lg">
					<?php esc_html_e( 'View Programme', 'awsisa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ============================================================
     STATS BANNER
     ============================================================ -->
<section class="stats" aria-label="<?php esc_attr_e( 'Event statistics', 'awsisa' ); ?>">
	<div class="container">
		<div class="stats__grid">
			<div>
				<span class="stats__number">5,000+</span>
				<span class="stats__label"><?php esc_html_e( 'Delegates Expected', 'awsisa' ); ?></span>
			</div>
			<div>
				<span class="stats__number">54</span>
				<span class="stats__label"><?php esc_html_e( 'African Countries', 'awsisa' ); ?></span>
			</div>
			<div>
				<span class="stats__number">200+</span>
				<span class="stats__label"><?php esc_html_e( 'Speakers & Panellists', 'awsisa' ); ?></span>
			</div>
			<div>
				<span class="stats__number">4</span>
				<span class="stats__label"><?php esc_html_e( 'Days of Dialogue', 'awsisa' ); ?></span>
			</div>
			<div>
				<span class="stats__number">80+</span>
				<span class="stats__label"><?php esc_html_e( 'Exhibitors & Sponsors', 'awsisa' ); ?></span>
			</div>
		</div>
	</div>
</section>

<!-- ============================================================
     ABOUT / OVERVIEW
     ============================================================ -->
<section class="section" aria-labelledby="about-title">
	<div class="container">
		<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">

			<div>
				<span class="badge badge--primary" style="margin-bottom: 1rem;"><?php esc_html_e( 'About the Dialogue', 'awsisa' ); ?></span>
				<h2 id="about-title"><?php esc_html_e( 'A Landmark Event for the Global South', 'awsisa' ); ?></h2>
				<p style="font-size: 1.125rem; color: #475569; margin-top: 1rem; line-height: 1.7;">
					<?php esc_html_e( 'The AWSISA Africa & Global South Water & Sanitation Dialogue 2026 brings together government officials, utility executives, NGO leaders, academics, and private sector innovators to advance water security and sanitation access across the continent.', 'awsisa' ); ?>
				</p>
				<p style="color: #475569; margin-top: 1rem; line-height: 1.7;">
					<?php esc_html_e( 'Through high-level panels, technical workshops, and the adoption of the Johannesburg Declaration, participants will shape the water and sanitation agenda for Sub-Saharan Africa.', 'awsisa' ); ?>
				</p>

				<div style="display: flex; gap: 1.5rem; margin-top: 2rem; flex-wrap: wrap;">
					<div style="display: flex; align-items: center; gap: 0.75rem;">
						<div style="width: 48px; height: 48px; background: #F0FDFA; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2" aria-hidden="true"><path d="M8 6H21M8 12H21M8 18H21M3 6h.01M3 12h.01M3 18h.01"/></svg>
						</div>
						<div>
							<div style="font-weight: 700; font-size: 0.9375rem;"><?php esc_html_e( '40+ Sessions', 'awsisa' ); ?></div>
							<div style="font-size: 0.8125rem; color: #64748B;"><?php esc_html_e( 'Over 4 days', 'awsisa' ); ?></div>
						</div>
					</div>
					<div style="display: flex; align-items: center; gap: 0.75rem;">
						<div style="width: 48px; height: 48px; background: #FEF3C7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#B45309" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
						</div>
						<div>
							<div style="font-weight: 700; font-size: 0.9375rem;"><?php esc_html_e( '5,000+ Delegates', 'awsisa' ); ?></div>
							<div style="font-size: 0.8125rem; color: #64748B;"><?php esc_html_e( 'From 54+ countries', 'awsisa' ); ?></div>
						</div>
					</div>
				</div>

				<div style="margin-top: 2rem;">
					<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn--outline">
						<?php esc_html_e( 'Learn More', 'awsisa' ); ?>
					</a>
				</div>
			</div>

			<div>
				<!-- Map / Venue visual -->
				<div style="border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2);">
					<iframe
						src="https://maps.google.com/maps?q=ICC+Durban+45+Bram+Fischer+Road+Durban&t=&z=15&ie=UTF8&iwloc=&output=embed"
						width="100%"
						height="380"
						style="border: 0; display: block;"
						allowfullscreen=""
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						title="<?php esc_attr_e( 'ICC Durban — Event Venue', 'awsisa' ); ?>"
					></iframe>
				</div>
				<p style="text-align: center; font-size: 0.875rem; color: #64748B; margin-top: 0.75rem;">
					<?php esc_html_e( 'Inkosi Albert Luthuli ICC, 45 Bram Fischer Road, Durban', 'awsisa' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>

<!-- ============================================================
     AGENDA PREVIEW
     ============================================================ -->
<section class="section section--surface" aria-labelledby="agenda-preview-title">
	<div class="container">
		<div style="text-align: center; margin-bottom: 3rem;">
			<span class="badge badge--primary" style="margin-bottom: 1rem;"><?php esc_html_e( 'Programme', 'awsisa' ); ?></span>
			<h2 id="agenda-preview-title"><?php esc_html_e( '4 Days of Water & Sanitation Leadership', 'awsisa' ); ?></h2>
			<p style="color: #64748B; max-width: 600px; margin: 1rem auto 0;"><?php esc_html_e( 'From keynote addresses to technical workshops, the programme covers the full spectrum of water and sanitation challenges facing the Global South.', 'awsisa' ); ?></p>
		</div>

		<!-- Day tabs -->
		<div class="agenda-tabs" id="agenda-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Agenda days', 'awsisa' ); ?>">
			<button class="agenda-tab is-active" role="tab" aria-selected="true" aria-controls="day-1-panel" id="day-1-tab" data-day="day1">
				<?php esc_html_e( 'Day 1 · 9 Nov', 'awsisa' ); ?>
			</button>
			<button class="agenda-tab" role="tab" aria-selected="false" aria-controls="day-2-panel" id="day-2-tab" data-day="day2">
				<?php esc_html_e( 'Day 2 · 10 Nov', 'awsisa' ); ?>
			</button>
			<button class="agenda-tab" role="tab" aria-selected="false" aria-controls="day-3-panel" id="day-3-tab" data-day="day3">
				<?php esc_html_e( 'Day 3 · 11 Nov', 'awsisa' ); ?>
			</button>
			<button class="agenda-tab" role="tab" aria-selected="false" aria-controls="day-4-panel" id="day-4-tab" data-day="day4">
				<?php esc_html_e( 'Day 4 · 12 Nov', 'awsisa' ); ?>
			</button>
		</div>

		<div id="agenda-panels">
			<?php
			$days = array(
				'day1' => array( 'date' => '2026-11-09', 'label' => '9 November 2026' ),
				'day2' => array( 'date' => '2026-11-10', 'label' => '10 November 2026' ),
				'day3' => array( 'date' => '2026-11-11', 'label' => '11 November 2026' ),
				'day4' => array( 'date' => '2026-11-12', 'label' => '12 November 2026' ),
			);

			// Use the Supabase helper to fetch sessions, or use CPT as fallback.
			$sessions_by_day = array();
			$supabase_result  = awsisa_supabase_request(
				'agenda_sessions',
				'GET',
				array(),
				'is_published=eq.true&order=day.asc,sort_order.asc&select=*'
			);

			if ( ! is_wp_error( $supabase_result ) && is_array( $supabase_result ) ) {
				foreach ( $supabase_result as $session ) {
					$sessions_by_day[ $session['day'] ][] = $session;
				}
			}

			$first = true;
			foreach ( $days as $day_key => $day_info ) :
				$hidden = $first ? '' : 'hidden';
				?>
				<div
					id="<?php echo esc_attr( $day_key ); ?>-panel"
					role="tabpanel"
					aria-labelledby="<?php echo esc_attr( $day_key ); ?>-tab"
					class="agenda-day-panel"
					<?php echo $hidden ? 'hidden' : ''; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				>
					<?php
					$day_sessions = isset( $sessions_by_day[ $day_info['date'] ] ) ? $sessions_by_day[ $day_info['date'] ] : array();

					if ( ! empty( $day_sessions ) ) :
						foreach ( $day_sessions as $session ) :
							$is_break   = in_array( $session['session_type'], array( 'break', 'networking' ), true );
							$type_badge = '';
							if ( 'keynote' === $session['session_type'] ) {
								$type_badge = '<span class="badge badge--accent" style="margin-left: 0.5rem;">' . esc_html__( 'Keynote', 'awsisa' ) . '</span>';
							} elseif ( 'panel' === $session['session_type'] ) {
								$type_badge = '<span class="badge badge--primary" style="margin-left: 0.5rem;">' . esc_html__( 'Panel', 'awsisa' ) . '</span>';
							} elseif ( 'workshop' === $session['session_type'] ) {
								$type_badge = '<span class="badge badge--earth" style="margin-left: 0.5rem;">' . esc_html__( 'Workshop', 'awsisa' ) . '</span>';
							}
							?>
							<div class="agenda-session <?php echo $is_break ? 'agenda-session--break' : ''; ?>">
								<div class="agenda-session__time">
									<?php echo esc_html( substr( $session['start_time'], 0, 5 ) . ' – ' . substr( $session['end_time'], 0, 5 ) ); ?>
								</div>
								<div>
									<div class="agenda-session__title">
										<?php echo esc_html( $session['title'] ); ?>
										<?php echo $type_badge; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
										<div class="agenda-session__speaker" style="margin-top: 0.25rem;">
											<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
											<?php echo esc_html( $session['room'] ); ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<?php
						endforeach;
					else :
						// Show placeholder sessions if Supabase is not yet configured.
						$placeholder_sessions = array(
							array( 'time' => '08:30 – 09:00', 'title' => __( 'Opening Ceremony & Cultural Performance', 'awsisa' ), 'type' => 'plenary' ),
							array( 'time' => '09:00 – 10:00', 'title' => __( 'Keynote: Water Security in the Global South', 'awsisa' ), 'type' => 'keynote', 'speaker' => 'To be announced' ),
							array( 'time' => '10:00 – 10:30', 'title' => __( 'Tea & Networking Break', 'awsisa' ), 'type' => 'break' ),
							array( 'time' => '10:30 – 12:30', 'title' => __( 'Panel: National Water Strategies — Lessons from Africa', 'awsisa' ), 'type' => 'panel' ),
							array( 'time' => '12:30 – 14:00', 'title' => __( 'Networking Lunch', 'awsisa' ), 'type' => 'break' ),
							array( 'time' => '14:00 – 17:00', 'title' => __( 'Afternoon Sessions & Exhibition', 'awsisa' ), 'type' => 'session' ),
							array( 'time' => '19:00 – 22:00', 'title' => __( 'Welcome Gala Dinner — The Legacy Table', 'awsisa' ), 'type' => 'networking' ),
						);
						foreach ( $placeholder_sessions as $ps ) :
							?>
							<div class="agenda-session <?php echo in_array( $ps['type'], array( 'break', 'networking' ), true ) ? 'agenda-session--break' : ''; ?>">
								<div class="agenda-session__time"><?php echo esc_html( $ps['time'] ); ?></div>
								<div>
									<div class="agenda-session__title">
										<?php echo esc_html( $ps['title'] ); ?>
										<?php if ( 'keynote' === $ps['type'] ) : ?>
											<span class="badge badge--accent" style="margin-left: 0.5rem;"><?php esc_html_e( 'Keynote', 'awsisa' ); ?></span>
										<?php elseif ( 'panel' === $ps['type'] ) : ?>
											<span class="badge badge--primary" style="margin-left: 0.5rem;"><?php esc_html_e( 'Panel', 'awsisa' ); ?></span>
										<?php endif; ?>
									</div>
									<?php if ( ! empty( $ps['speaker'] ) ) : ?>
										<div class="agenda-session__speaker"><?php echo esc_html( $ps['speaker'] ); ?></div>
									<?php endif; ?>
								</div>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<?php
				$first = false;
			endforeach;
			?>
		</div>

		<div style="text-align: center; margin-top: 2.5rem;">
			<a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>" class="btn btn--primary">
				<?php esc_html_e( 'View Full Programme', 'awsisa' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ============================================================
     REGISTRATION CTA BANNER
     ============================================================ -->
<section class="section section--primary" aria-labelledby="cta-title">
	<div class="container" style="text-align: center;">
		<h2 id="cta-title" style="color: white; margin-bottom: 1rem;">
			<?php esc_html_e( 'Secure Your Place at the Dialogue', 'awsisa' ); ?>
		</h2>
		<p style="color: rgba(255,255,255,0.85); font-size: 1.125rem; max-width: 640px; margin: 0 auto 2.5rem;">
			<?php esc_html_e( 'Join water and sanitation leaders from across Africa and the Global South. Early registration is now open.', 'awsisa' ); ?>
		</p>
		<div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
			<a href="<?php echo esc_url( awsisa_register_url() ); ?>" class="btn btn--accent btn--lg">
				<?php esc_html_e( 'Register as Delegate', 'awsisa' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/sponsors/' ) ); ?>" class="btn btn--outline-white btn--lg">
				<?php esc_html_e( 'Become a Sponsor', 'awsisa' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ============================================================
     SPONSORS PREVIEW
     ============================================================ -->
<section class="section" aria-labelledby="sponsors-title">
	<div class="container">
		<div style="text-align: center; margin-bottom: 3rem;">
			<span class="badge badge--primary" style="margin-bottom: 1rem;"><?php esc_html_e( 'Partners & Sponsors', 'awsisa' ); ?></span>
			<h2 id="sponsors-title"><?php esc_html_e( 'Supported by Leaders in Water & Sanitation', 'awsisa' ); ?></h2>
		</div>

		<?php
		// Fetch sponsors from Supabase.
		$sponsors = awsisa_supabase_request( 'sponsors', 'GET', array(), 'is_active=eq.true&order=sort_order.asc&select=*' );

		if ( ! is_wp_error( $sponsors ) && is_array( $sponsors ) && ! empty( $sponsors ) ) :
			$tiers = array( 'platinum', 'gold', 'silver', 'bronze', 'partner' );

			foreach ( $tiers as $tier ) :
				$tier_sponsors = array_filter( $sponsors, function ( $s ) use ( $tier ) {
					return $s['tier'] === $tier;
				} );

				if ( empty( $tier_sponsors ) ) {
					continue;
				}
				?>
				<div class="sponsors-tier">
					<div class="sponsors-tier__label">
						<span class="badge badge--<?php echo esc_attr( $tier ); ?>" style="font-size: 0.75rem;"><?php echo esc_html( ucfirst( $tier ) ); ?></span>
					</div>
					<div class="sponsors-tier__grid">
						<?php foreach ( $tier_sponsors as $sponsor ) : ?>
							<a href="<?php echo esc_url( $sponsor['website_url'] ); ?>" class="sponsor-logo" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $sponsor['name'] ); ?>">
								<?php if ( ! empty( $sponsor['logo_url'] ) ) : ?>
									<img src="<?php echo esc_url( $sponsor['logo_url'] ); ?>" alt="<?php echo esc_attr( $sponsor['name'] ); ?>" loading="lazy">
								<?php else : ?>
									<span style="font-weight: 700; color: #334155; font-size: 0.875rem;"><?php echo esc_html( $sponsor['name'] ); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			endforeach;
		else :
			// Placeholder sponsor logos.
			$placeholder_sponsors = array(
				array( 'tier' => 'platinum', 'sponsors' => array( 'Dept. of Water & Sanitation SA', 'African Development Bank' ) ),
				array( 'tier' => 'gold', 'sponsors' => array( 'GIZ', 'UNICEF', 'Water Research Commission' ) ),
				array( 'tier' => 'silver', 'sponsors' => array( 'Grundfos', 'Xylem' ) ),
			);
			foreach ( $placeholder_sponsors as $tier_group ) :
				?>
				<div class="sponsors-tier">
					<div class="sponsors-tier__label">
						<span class="badge badge--<?php echo esc_attr( $tier_group['tier'] ); ?>"><?php echo esc_html( ucfirst( $tier_group['tier'] ) ); ?></span>
					</div>
					<div class="sponsors-tier__grid">
						<?php foreach ( $tier_group['sponsors'] as $name ) : ?>
							<div class="sponsor-logo">
								<span style="font-weight: 700; color: #334155; font-size: 0.875rem;"><?php echo esc_html( $name ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			endforeach;
		endif;
		?>

		<div style="text-align: center; margin-top: 2.5rem;">
			<a href="<?php echo esc_url( home_url( '/sponsors/' ) ); ?>" class="btn btn--outline">
				<?php esc_html_e( 'View All Partners', 'awsisa' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ============================================================
     LEGACY INITIATIVE (DONATION) TEASER
     ============================================================ -->
<section class="section" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);" aria-labelledby="legacy-title">
	<div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
		<div>
			<span class="badge badge--success" style="margin-bottom: 1rem;"><?php esc_html_e( 'Legacy Initiative', 'awsisa' ); ?></span>
			<h2 id="legacy-title" style="color: #14532D;"><?php esc_html_e( 'Leave a Legacy Beyond the Dialogue', 'awsisa' ); ?></h2>
			<p style="color: #166534; font-size: 1.0625rem; margin-top: 1rem; line-height: 1.7;">
				<?php esc_html_e( 'Your contribution to the Legacy Initiative funds rural sanitation infrastructure in underserved communities — turning conversations into concrete change.', 'awsisa' ); ?>
			</p>
			<ul style="list-style: none; padding: 0; margin: 1.5rem 0; color: #166534;">
				<li style="padding: 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
					<span style="color: #16A34A; font-size: 1.25rem;">✓</span>
					<?php esc_html_e( 'Instant digital receipt for tax purposes', 'awsisa' ); ?>
				</li>
				<li style="padding: 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
					<span style="color: #16A34A; font-size: 1.25rem;">✓</span>
					<?php esc_html_e( 'Donor recognition in the conference proceedings', 'awsisa' ); ?>
				</li>
				<li style="padding: 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
					<span style="color: #16A34A; font-size: 1.25rem;">✓</span>
					<?php esc_html_e( 'POPIA-compliant, secure donations in ZAR or USD', 'awsisa' ); ?>
				</li>
			</ul>
			<a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="btn btn--legacy btn--lg">
				<?php esc_html_e( 'Contribute to the Legacy', 'awsisa' ); ?>
			</a>
		</div>
		<div>
			<!-- Impact visual -->
			<div style="background: white; border-radius: 1.5rem; padding: 2.5rem; box-shadow: 0 20px 40px -10px rgba(22,163,74,0.2);">
				<div style="font-family: 'Outfit', sans-serif; font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #16A34A; margin-bottom: 1.5rem;">
					<?php esc_html_e( 'Your Contribution Builds...', 'awsisa' ); ?>
				</div>
				<?php
				$impacts = array(
					array( 'amount' => 'R100', 'impact' => __( 'Sanitation hygiene kit for 1 family', 'awsisa' ) ),
					array( 'amount' => 'R500', 'impact' => __( 'Hand-washing station for a school', 'awsisa' ) ),
					array( 'amount' => 'R1,000', 'impact' => __( 'Pit latrine for a rural household', 'awsisa' ) ),
					array( 'amount' => 'R5,000', 'impact' => __( 'Borehole pump for a community', 'awsisa' ) ),
				);
				foreach ( $impacts as $impact ) :
					?>
					<div style="display: flex; align-items: center; gap: 1.25rem; padding: 1rem 0; border-bottom: 1px solid #DCFCE7;">
						<div style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: #16A34A; min-width: 80px;">
							<?php echo esc_html( $impact['amount'] ); ?>
						</div>
						<div style="font-size: 0.9375rem; color: #166534;">
							<?php echo esc_html( $impact['impact'] ); ?>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
