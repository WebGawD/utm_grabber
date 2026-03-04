<?php
/**
 * Single post / speaker / CPT template
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_type = get_post_type();

	if ( 'awsisa_speaker' === $post_type ) :
		// ── Speaker profile ──────────────────────────────────────────────
		$title        = get_post_meta( get_the_ID(), '_awsisa_speaker_title', true );
		$organisation = get_post_meta( get_the_ID(), '_awsisa_speaker_organisation', true );
		$country      = get_post_meta( get_the_ID(), '_awsisa_speaker_country', true );
		$bio          = get_post_meta( get_the_ID(), '_awsisa_speaker_bio', true );
		$linkedin     = get_post_meta( get_the_ID(), '_awsisa_speaker_linkedin', true );
		$twitter      = get_post_meta( get_the_ID(), '_awsisa_speaker_twitter', true );
		$session_ids  = get_post_meta( get_the_ID(), '_awsisa_speaker_sessions', true );
		?>

		<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#115E59 100%);">
			<div class="container">
				<p class="page-hero__eyebrow">Speaker Profile</p>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<?php if ( $title || $organisation ) : ?>
					<p class="page-hero__subtitle">
						<?php echo esc_html( implode( ' · ', array_filter( array( $title, $organisation, $country ) ) ) ); ?>
					</p>
				<?php endif; ?>
			</div>
		</section>

		<section class="section">
			<div class="container" style="max-width:860px;display:grid;grid-template-columns:220px 1fr;gap:3rem;align-items:start;">

				<!-- Photo & meta -->
				<div style="text-align:center;">
					<?php if ( has_post_thumbnail() ) : ?>
						<div style="width:180px;height:180px;border-radius:50%;overflow:hidden;margin:0 auto 1.25rem;border:4px solid #99F6E4;">
							<?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?>
						</div>
					<?php else : ?>
						<div style="width:180px;height:180px;border-radius:50%;background:linear-gradient(135deg,#0D9488,#115E59);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:3.5rem;">
							🎙️
						</div>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<p style="font-size:.85rem;color:#0D9488;font-weight:700;margin:0 0 .25rem;"><?php echo esc_html( $title ); ?></p>
					<?php endif; ?>
					<?php if ( $organisation ) : ?>
						<p style="font-size:.875rem;color:#475569;margin:0 0 .25rem;"><?php echo esc_html( $organisation ); ?></p>
					<?php endif; ?>
					<?php if ( $country ) : ?>
						<p style="font-size:.875rem;color:#94A3B8;margin:0 0 1rem;"><?php echo esc_html( $country ); ?></p>
					<?php endif; ?>

					<div style="display:flex;justify-content:center;gap:.5rem;flex-wrap:wrap;">
						<?php if ( $linkedin ) : ?>
							<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="btn btn--outline" style="padding:.375rem .75rem;font-size:.75rem;">LinkedIn</a>
						<?php endif; ?>
						<?php if ( $twitter ) : ?>
							<a href="https://twitter.com/<?php echo esc_attr( ltrim( $twitter, '@' ) ); ?>" target="_blank" rel="noopener" class="btn btn--outline" style="padding:.375rem .75rem;font-size:.75rem;">𝕏 Twitter</a>
						<?php endif; ?>
					</div>
				</div>

				<!-- Bio & sessions -->
				<div>
					<h2 style="font-size:1.5rem;font-weight:700;color:#0F172A;margin:0 0 1rem;">About</h2>
					<div style="color:#475569;line-height:1.8;font-size:.95rem;">
						<?php
						if ( $bio ) {
							echo wp_kses_post( wpautop( $bio ) );
						} else {
							the_content();
						}
						?>
					</div>

					<?php
					// Sessions for this speaker (from Supabase agenda).
					if ( defined( 'AWSISA_SUPABASE_URL' ) ) :
						$speaker_name    = get_the_title();
						$encoded_name    = urlencode( $speaker_name );
						$sessions_resp   = awsisa_supabase_request(
							'/rest/v1/agenda_sessions?speakers=cs.%5B"' . $encoded_name . '"%5D&is_published=eq.true&order=session_date.asc,start_time.asc',
							array( 'method' => 'GET' )
						);
						$speaker_sessions = ! is_wp_error( $sessions_resp ) ? ( json_decode( $sessions_resp, true ) ?: array() ) : array();

						if ( ! empty( $speaker_sessions ) ) : ?>
							<h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:2rem 0 1rem;">Sessions</h3>
							<div style="display:flex;flex-direction:column;gap:.75rem;">
								<?php foreach ( $speaker_sessions as $s ) : ?>
									<div style="padding:.875rem 1rem;background:#F0FDFA;border:1px solid #99F6E4;border-left:3px solid #0D9488;border-radius:8px;">
										<div style="font-weight:700;color:#0F172A;font-size:.9rem;"><?php echo esc_html( $s['title'] ); ?></div>
										<div style="font-size:.8rem;color:#64748B;margin-top:.25rem;">
											<?php echo esc_html( date( 'D j M', strtotime( $s['session_date'] ) ) ); ?>
											· <?php echo esc_html( substr( $s['start_time'] ?? '', 0, 5 ) ); ?>
											<?php if ( ! empty( $s['room'] ) ) : ?> · <?php echo esc_html( $s['room'] ); ?><?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif;
					endif; ?>
				</div>

			</div>
		</section>

	<?php else :
		// ── Default single post ──────────────────────────────────────────
		?>

		<section class="page-hero">
			<div class="container">
				<p class="page-hero__eyebrow"><?php echo esc_html( get_post_type_object( $post_type )->labels->singular_name ?? 'Update' ); ?></p>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<p class="page-hero__subtitle"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?> &nbsp;·&nbsp; <?php the_author(); ?></p>
			</div>
		</section>

		<section class="section">
			<div class="container" style="max-width:780px;">
				<div style="font-size:1rem;line-height:1.85;color:#374151;">
					<?php the_content(); ?>
				</div>
				<div style="margin-top:3rem;padding-top:2rem;border-top:1px solid #E2E8F0;display:flex;gap:1rem;">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--outline">← Home</a>
				</div>
			</div>
		</section>

	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
