<?php
/**
 * Template Name: Conference Agenda
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

$days = array(
	'2026-11-09' => 'Sunday 9 Nov — Pre-Conference & Welcome',
	'2026-11-10' => 'Monday 10 Nov — Opening Day',
	'2026-11-11' => 'Tuesday 11 Nov — Technical Sessions',
	'2026-11-12' => 'Wednesday 12 Nov — Closing & Action Plans',
);

$sessions = array();
if ( defined( 'AWSISA_SUPABASE_URL' ) ) {
	$response = awsisa_supabase_request(
		'agenda_sessions',
		'GET',
		array(),
		'is_published=eq.true&order=day.asc,start_time.asc'
	);
	if ( ! is_wp_error( $response ) ) {
		$raw = is_array( $response ) ? $response : array();
		foreach ( $raw as $s ) {
			$sessions[ $s['day'] ][] = $s;
		}
	}
}
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#0D9488 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Watersan Dialogue 2026</p>
		<h1 class="page-hero__title">Conference Programme</h1>
		<p class="page-hero__subtitle">9 – 12 November 2026 · Emperors Palace, Johannesburg</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:960px;">

		<!-- Day tabs -->
		<div class="agenda-tabs" style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:2.5rem;" role="tablist">
			<?php $i = 0; foreach ( $days as $date => $label ) : $i++; ?>
				<button
					class="agenda-tab btn <?php echo 0 === ( $i - 1 ) ? 'btn--primary' : 'btn--outline'; ?>"
					data-date="<?php echo esc_attr( $date ); ?>"
					role="tab"
					aria-selected="<?php echo 0 === ( $i - 1 ) ? 'true' : 'false'; ?>"
					aria-controls="day-<?php echo esc_attr( $date ); ?>">
					<?php echo esc_html( date( 'D j M', strtotime( $date ) ) ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php $i = 0; foreach ( $days as $date => $label ) : $i++; ?>
			<div
				id="day-<?php echo esc_attr( $date ); ?>"
				class="agenda-day-panel"
				role="tabpanel"
				<?php echo 0 !== ( $i - 1 ) ? 'hidden' : ''; ?>>

				<h2 style="font-size:1.25rem;font-weight:700;color:#0F172A;margin:0 0 1.5rem;"><?php echo esc_html( $label ); ?></h2>

				<?php
				$day_sessions = $sessions[ $date ] ?? array();
				$type_colors  = array(
					'plenary'    => array( 'bg' => '#ECFDF5', 'text' => '#065F46', 'border' => '#6EE7B7' ),
					'workshop'   => array( 'bg' => '#EFF6FF', 'text' => '#1E3A8A', 'border' => '#93C5FD' ),
					'breakout'   => array( 'bg' => '#FFF7ED', 'text' => '#9A3412', 'border' => '#FED7AA' ),
					'networking' => array( 'bg' => '#F5F3FF', 'text' => '#5B21B6', 'border' => '#C4B5FD' ),
					'exhibition' => array( 'bg' => '#FFFBEB', 'text' => '#92400E', 'border' => '#FDE68A' ),
					'ceremony'   => array( 'bg' => '#FDF4FF', 'text' => '#6B21A8', 'border' => '#E9D5FF' ),
				);

				if ( empty( $day_sessions ) ) :
					?>
					<div style="padding:3rem;text-align:center;background:#F8FAFC;border-radius:12px;">
						<p style="color:#64748B;margin:0;">Programme for this day will be published soon.</p>
					</div>
				<?php else : ?>
					<div style="display:flex;flex-direction:column;gap:1rem;">
						<?php foreach ( $day_sessions as $s ) :
							$colors = $type_colors[ $s['session_type'] ?? '' ] ?? array( 'bg' => '#F8FAFC', 'text' => '#374151', 'border' => '#E2E8F0' );
							$start  = substr( $s['start_time'] ?? '', 0, 5 );
							$end    = substr( $s['end_time'] ?? '', 0, 5 );
							?>
							<div style="display:grid;grid-template-columns:80px 1fr;gap:1rem;background:<?php echo esc_attr( $colors['bg'] ); ?>;border:1px solid <?php echo esc_attr( $colors['border'] ); ?>;border-left:4px solid <?php echo esc_attr( $colors['border'] ); ?>;border-radius:8px;padding:1rem;">
								<div style="text-align:center;">
									<div style="font-size:.875rem;font-weight:700;color:#0F172A;"><?php echo esc_html( $start ); ?></div>
									<?php if ( $end ) : ?>
										<div style="font-size:.75rem;color:#94A3B8;">– <?php echo esc_html( $end ); ?></div>
									<?php endif; ?>
								</div>
								<div>
									<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
										<span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:<?php echo esc_attr( $colors['text'] ); ?>;">
											<?php echo esc_html( ucfirst( $s['session_type'] ?? '' ) ); ?>
										</span>
										<?php if ( ! empty( $s['track'] ) ) : ?>
											<span style="font-size:.7rem;color:#94A3B8;">· <?php echo esc_html( $s['track'] ); ?></span>
										<?php endif; ?>
									</div>
									<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .25rem;"><?php echo esc_html( $s['title'] ); ?></h3>
									<?php if ( ! empty( $s['speaker_name'] ) ) : ?>
										<p style="font-size:.8rem;color:#475569;margin:0 0 .25rem;">
											<?php
											$spk = esc_html( $s['speaker_name'] );
											if ( ! empty( $s['speaker_org'] ) ) {
												$spk .= ' · ' . esc_html( $s['speaker_org'] );
											}
											echo $spk;
											?>
										</p>
									<?php endif; ?>
									<?php if ( ! empty( $s['room'] ) ) : ?>
										<p style="font-size:.75rem;color:#94A3B8;margin:0;">📍 <?php echo esc_html( $s['room'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<!-- Download CTA -->
		<div style="margin-top:3rem;text-align:center;padding:2rem;background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;">
			<h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0 0 .5rem;">Take the Agenda With You</h3>
			<p style="color:#475569;font-size:.875rem;margin:0 0 1.25rem;">Download the full programme PDF for offline reference or share with colleagues.</p>
			<a href="<?php echo esc_url( home_url( '/wp-json/awsisa/v1/agenda?format=pdf' ) ); ?>" class="btn btn--primary" download>Download Programme PDF</a>
		</div>

	</div>
</section>

<script>
( function () {
	const tabs   = document.querySelectorAll( '.agenda-tab' );
	const panels = document.querySelectorAll( '.agenda-day-panel' );

	tabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function () {
			const date = tab.dataset.date;

			tabs.forEach( function ( t ) {
				t.setAttribute( 'aria-selected', 'false' );
				t.classList.remove( 'btn--primary' );
				t.classList.add( 'btn--outline' );
			} );
			tab.setAttribute( 'aria-selected', 'true' );
			tab.classList.add( 'btn--primary' );
			tab.classList.remove( 'btn--outline' );

			panels.forEach( function ( p ) {
				p.hidden = p.id !== 'day-' + date;
			} );
		} );
	} );
} )();
</script>

<?php get_footer(); ?>
