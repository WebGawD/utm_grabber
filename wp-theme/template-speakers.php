<?php
/**
 * Template Name: Speakers
 *
 * Listing page for all awsisa_speaker CPT entries.
 * Reads meta: _awsisa_speaker_title, _awsisa_speaker_org,
 *             _awsisa_speaker_country, _awsisa_speaker_keynote, _awsisa_speaker_day
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

// ── Fetch all speakers ──────────────────────────────────────────────────────
$speakers_query = new WP_Query( array(
	'post_type'      => 'awsisa_speaker',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_query'     => array( 'relation' => 'OR',
		array( 'key' => '_awsisa_speaker_keynote', 'value' => '1', 'compare' => '=' ),
		array( 'key' => '_awsisa_speaker_keynote', 'compare' => 'EXISTS' ),
	),
	'orderby' => array( 'meta_value' => 'DESC', 'title' => 'ASC' ),
	'meta_key' => '_awsisa_speaker_keynote',
) );

// Group for filter tabs.
$all_speakers     = array();
$keynote_speakers = array();
$by_day           = array( 'day1' => array(), 'day2' => array(), 'day3' => array(), 'day4' => array() );

if ( $speakers_query->have_posts() ) {
	while ( $speakers_query->have_posts() ) {
		$speakers_query->the_post();
		$id   = get_the_ID();
		$data = array(
			'id'           => $id,
			'name'         => get_the_title(),
			'permalink'    => get_permalink( $id ),
			'thumbnail'    => get_the_post_thumbnail_url( $id, 'awsisa-avatar' ),
			'excerpt'      => get_the_excerpt(),
			'job_title'    => get_post_meta( $id, '_awsisa_speaker_title',   true ),
			'organisation' => get_post_meta( $id, '_awsisa_speaker_org',     true ),
			'country'      => get_post_meta( $id, '_awsisa_speaker_country', true ),
			'is_keynote'   => (bool) get_post_meta( $id, '_awsisa_speaker_keynote', true ),
			'day'          => get_post_meta( $id, '_awsisa_speaker_day',     true ),
			'linkedin'     => get_post_meta( $id, '_awsisa_speaker_linkedin', true ),
		);
		$all_speakers[] = $data;
		if ( $data['is_keynote'] ) {
			$keynote_speakers[] = $data;
		}
		if ( isset( $by_day[ $data['day'] ] ) ) {
			$by_day[ $data['day'] ][] = $data;
		}
	}
	wp_reset_postdata();
}

// Speaker counts for tab badges.
$total_speakers   = count( $all_speakers );
$keynote_count    = count( $keynote_speakers );
$day_labels       = array(
	'day1' => '9 Nov', 'day2' => '10 Nov', 'day3' => '11 Nov', 'day4' => '12 Nov',
);
?>

<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#134E4A 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Watersan Dialogue 2026</p>
		<h1 class="page-hero__title"><?php esc_html_e( 'Speakers & Panellists', 'awsisa' ); ?></h1>
		<p class="page-hero__subtitle">
			<?php
			/* translators: %d: total speaker count */
			printf( esc_html( _n( '%d confirmed speaker', '%d confirmed speakers', $total_speakers, 'awsisa' ) ), $total_speakers );
			?>
			<?php esc_html_e( ' from across Africa and the Global South', 'awsisa' ); ?>
		</p>
	</div>
</section>

<!-- ── Filter tabs + search ──────────────────────────────────────────────── -->
<section class="section" style="padding-bottom:0;">
	<div class="container">

		<!-- Search bar -->
		<div style="margin-bottom:1.5rem;position:relative;max-width:440px;">
			<svg style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
			<input
				id="speaker-search"
				type="search"
				placeholder="<?php esc_attr_e( 'Search speakers…', 'awsisa' ); ?>"
				autocomplete="off"
				style="width:100%;padding:.625rem .875rem .625rem 2.5rem;border:1.5px solid #CBD5E1;border-radius:8px;font-size:.9rem;outline:none;transition:border-color .15s;"
				aria-label="<?php esc_attr_e( 'Search speakers by name or organisation', 'awsisa' ); ?>"
			>
		</div>

		<!-- Day / keynote filter tabs -->
		<div role="tablist" aria-label="<?php esc_attr_e( 'Filter speakers', 'awsisa' ); ?>" style="display:flex;flex-wrap:wrap;gap:.5rem;border-bottom:2px solid #E2E8F0;padding-bottom:0;">

			<button
				class="speaker-tab is-active"
				role="tab"
				aria-selected="true"
				aria-controls="speakers-panel"
				data-filter="all"
				style="padding:.5rem 1.125rem;border:none;background:none;font-weight:600;font-size:.875rem;cursor:pointer;border-bottom:2px solid #0D9488;margin-bottom:-2px;color:#0D9488;"
			>
				<?php esc_html_e( 'All', 'awsisa' ); ?>
				<span style="display:inline-flex;align-items:center;justify-content:center;background:#E2E8F0;border-radius:99px;font-size:.7rem;font-weight:700;min-width:1.4em;height:1.4em;padding:0 .3em;margin-left:.375rem;color:#475569;">
					<?php echo esc_html( $total_speakers ); ?>
				</span>
			</button>

			<?php if ( $keynote_count > 0 ) : ?>
				<button
					class="speaker-tab"
					role="tab"
					aria-selected="false"
					aria-controls="speakers-panel"
					data-filter="keynote"
					style="padding:.5rem 1.125rem;border:none;background:none;font-weight:600;font-size:.875rem;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;color:#64748B;"
				>
					⭐ <?php esc_html_e( 'Keynote', 'awsisa' ); ?>
					<span style="display:inline-flex;align-items:center;justify-content:center;background:#FEF3C7;border-radius:99px;font-size:.7rem;font-weight:700;min-width:1.4em;height:1.4em;padding:0 .3em;margin-left:.375rem;color:#92400E;">
						<?php echo esc_html( $keynote_count ); ?>
					</span>
				</button>
			<?php endif; ?>

			<?php foreach ( $day_labels as $day_key => $day_short ) :
				$day_count = count( $by_day[ $day_key ] );
				if ( $day_count === 0 ) continue;
				?>
				<button
					class="speaker-tab"
					role="tab"
					aria-selected="false"
					aria-controls="speakers-panel"
					data-filter="<?php echo esc_attr( $day_key ); ?>"
					style="padding:.5rem 1.125rem;border:none;background:none;font-weight:600;font-size:.875rem;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;color:#64748B;"
				>
					<?php echo esc_html( $day_short ); ?>
					<span style="display:inline-flex;align-items:center;justify-content:center;background:#E2E8F0;border-radius:99px;font-size:.7rem;font-weight:700;min-width:1.4em;height:1.4em;padding:0 .3em;margin-left:.375rem;color:#475569;">
						<?php echo esc_html( $day_count ); ?>
					</span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ── Speaker grid ───────────────────────────────────────────────────────── -->
<section class="section" id="speakers-section" style="padding-top:2rem;">
	<div class="container">

		<?php if ( ! empty( $all_speakers ) ) : ?>

			<div
				id="speakers-panel"
				role="tabpanel"
				style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.5rem;"
			>

				<?php foreach ( $all_speakers as $s ) :
					$initials = implode( '', array_map( function ( $w ) {
						return mb_strtoupper( mb_substr( $w, 0, 1 ) );
					}, explode( ' ', $s['name'], 2 ) ) );
					?>

					<article
						class="speaker-card card"
						data-name="<?php echo esc_attr( mb_strtolower( $s['name'] . ' ' . $s['organisation'] ) ); ?>"
						data-day="<?php echo esc_attr( $s['day'] ); ?>"
						data-keynote="<?php echo $s['is_keynote'] ? '1' : '0'; ?>"
						style="transition:box-shadow .2s;"
					>
						<a href="<?php echo esc_url( $s['permalink'] ); ?>" style="display:block;text-decoration:none;color:inherit;">

							<!-- Photo -->
							<div style="position:relative;padding-top:66.6%;background:#F0FDFA;border-radius:10px 10px 0 0;overflow:hidden;">
								<?php if ( $s['thumbnail'] ) : ?>
									<img
										src="<?php echo esc_url( $s['thumbnail'] ); ?>"
										alt="<?php echo esc_attr( $s['name'] ); ?>"
										loading="lazy"
										style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"
									>
								<?php else : ?>
									<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0D9488,#115E59);font-size:2.25rem;font-weight:800;color:#CCFBF1;font-family:'Outfit',sans-serif;">
										<?php echo esc_html( $initials ); ?>
									</div>
								<?php endif; ?>

								<?php if ( $s['is_keynote'] ) : ?>
									<div style="position:absolute;top:.625rem;right:.625rem;background:#F59E0B;color:#fff;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:.2rem .5rem;border-radius:20px;">
										<?php esc_html_e( 'Keynote', 'awsisa' ); ?>
									</div>
								<?php endif; ?>
							</div>

							<!-- Info -->
							<div class="card__body" style="padding:1rem 1.125rem 1.25rem;">
								<h3 style="font-size:1rem;font-weight:700;margin:0 0 .2rem;color:#0F172A;line-height:1.3;">
									<?php echo esc_html( $s['name'] ); ?>
								</h3>
								<?php if ( $s['job_title'] ) : ?>
									<p style="font-size:.8rem;color:#0D9488;font-weight:600;margin:0 0 .2rem;">
										<?php echo esc_html( $s['job_title'] ); ?>
									</p>
								<?php endif; ?>
								<?php if ( $s['organisation'] ) : ?>
									<p style="font-size:.8rem;color:#475569;margin:0 0 .35rem;">
										<?php echo esc_html( $s['organisation'] ); ?>
									</p>
								<?php endif; ?>
								<?php if ( $s['country'] ) : ?>
									<p style="font-size:.75rem;color:#94A3B8;margin:0;">
										<?php echo esc_html( $s['country'] ); ?>
									</p>
								<?php endif; ?>
							</div>
						</a>
					</article>

				<?php endforeach; ?>

			</div>

			<!-- Empty-state (shown by JS when search/filter yields zero results) -->
			<div id="speakers-empty" style="display:none;text-align:center;padding:4rem 1rem;">
				<div style="font-size:3rem;margin-bottom:1rem;">🎙️</div>
				<p style="color:#64748B;font-size:1rem;"><?php esc_html_e( 'No speakers match your current filter.', 'awsisa' ); ?></p>
			</div>

		<?php else : ?>

			<!-- No CPT entries yet — show placeholder grid -->
			<p class="notice notice--info" style="margin-bottom:2rem;">
				<?php esc_html_e( 'Speakers will be announced soon. Check back closer to the event.', 'awsisa' ); ?>
			</p>

			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.5rem;">
				<?php
				$placeholders = array(
					array( 'initials' => 'TBA', 'label' => 'Keynote Address — Water Security',           'sub' => 'To be announced', 'keynote' => true ),
					array( 'initials' => 'TBA', 'label' => 'Panel: National Water Strategies',            'sub' => 'To be announced', 'keynote' => false ),
					array( 'initials' => 'TBA', 'label' => 'Workshop: WASH in Urban Settings',           'sub' => 'To be announced', 'keynote' => false ),
					array( 'initials' => 'TBA', 'label' => 'Panel: Financing Sanitation at Scale',       'sub' => 'To be announced', 'keynote' => false ),
					array( 'initials' => 'TBA', 'label' => 'Keynote: Climate & Water Nexus',             'sub' => 'To be announced', 'keynote' => true ),
					array( 'initials' => 'TBA', 'label' => 'Workshop: Digital Utilities',                'sub' => 'To be announced', 'keynote' => false ),
				);
				foreach ( $placeholders as $ph ) : ?>
					<div class="card" style="opacity:.6;">
						<div style="position:relative;padding-top:66.6%;background:linear-gradient(135deg,#0D9488,#115E59);border-radius:10px 10px 0 0;overflow:hidden;">
							<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:800;color:#CCFBF1;font-family:'Outfit',sans-serif;">
								<?php echo esc_html( $ph['initials'] ); ?>
							</div>
							<?php if ( $ph['keynote'] ) : ?>
								<div style="position:absolute;top:.625rem;right:.625rem;background:#F59E0B;color:#fff;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:.2rem .5rem;border-radius:20px;">
									<?php esc_html_e( 'Keynote', 'awsisa' ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="card__body" style="padding:1rem 1.125rem 1.25rem;">
							<h3 style="font-size:.9rem;font-weight:700;margin:0 0 .35rem;color:#0F172A;"><?php echo esc_html( $ph['label'] ); ?></h3>
							<p style="font-size:.8rem;color:#94A3B8;margin:0;"><?php echo esc_html( $ph['sub'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		<?php endif; ?>

		<!-- Register CTA -->
		<div style="text-align:center;margin-top:4rem;padding:3rem;background:linear-gradient(135deg,#F0FDFA,#CCFBF1);border-radius:1rem;">
			<h2 style="color:#134E4A;margin:0 0 .75rem;"><?php esc_html_e( 'Interested in Speaking?', 'awsisa' ); ?></h2>
			<p style="color:#0F766E;margin:0 0 1.75rem;max-width:500px;margin-left:auto;margin-right:auto;">
				<?php esc_html_e( 'We welcome abstract submissions from water and sanitation practitioners, researchers, and policy makers.', 'awsisa' ); ?>
			</p>
			<div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'Submit Abstract', 'awsisa' ); ?></a>
				<a href="<?php echo esc_url( awsisa_register_url() ); ?>" class="btn btn--outline"><?php esc_html_e( 'Register as Delegate', 'awsisa' ); ?></a>
			</div>
		</div>

	</div><!-- .container -->
</section>

<!-- ── Filter + search JS ──────────────────────────────────────────────────── -->
<script>
(function () {
	'use strict';

	var cards    = document.querySelectorAll('.speaker-card');
	var tabs     = document.querySelectorAll('.speaker-tab');
	var searchEl = document.getElementById('speaker-search');
	var emptyEl  = document.getElementById('speakers-empty');

	var activeFilter = 'all';
	var searchQuery  = '';

	function applyFilters() {
		var visible = 0;
		cards.forEach(function (card) {
			var matchFilter = true;
			var matchSearch = true;

			// Filter by day / keynote.
			if (activeFilter === 'keynote') {
				matchFilter = card.dataset.keynote === '1';
			} else if (activeFilter !== 'all') {
				matchFilter = card.dataset.day === activeFilter;
			}

			// Search.
			if (searchQuery) {
				matchSearch = card.dataset.name.indexOf(searchQuery) !== -1;
			}

			var show = matchFilter && matchSearch;
			card.style.display = show ? '' : 'none';
			if (show) visible++;
		});

		if (emptyEl) {
			emptyEl.style.display = visible === 0 ? 'block' : 'none';
		}
	}

	// Tab clicks.
	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			tabs.forEach(function (t) {
				t.setAttribute('aria-selected', 'false');
				t.style.borderBottomColor = 'transparent';
				t.style.color = '#64748B';
			});
			tab.setAttribute('aria-selected', 'true');
			tab.style.borderBottomColor = '#0D9488';
			tab.style.color = '#0D9488';
			activeFilter = tab.dataset.filter;
			applyFilters();
		});
	});

	// Search input.
	if (searchEl) {
		searchEl.addEventListener('input', function () {
			searchQuery = this.value.trim().toLowerCase();
			applyFilters();
		});
		// Focus border.
		searchEl.addEventListener('focus', function () { this.style.borderColor = '#0D9488'; });
		searchEl.addEventListener('blur',  function () { this.style.borderColor = '#CBD5E1'; });
	}

	// Card hover elevation.
	cards.forEach(function (card) {
		card.addEventListener('mouseenter', function () { this.style.boxShadow = '0 8px 24px -4px rgba(13,148,136,.18)'; });
		card.addEventListener('mouseleave', function () { this.style.boxShadow = ''; });
	});
}());
</script>

<?php get_footer(); ?>
