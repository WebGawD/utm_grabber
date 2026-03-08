<?php
/**
 * Template Name: Legacy
 *
 * The AWSISA Legacy Initiative — CSI donation programme funding rural
 * sanitation infrastructure in underserved communities.
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="page-hero" style="background:linear-gradient(135deg,#14532D 0%,#166534 60%,#15803D 100%);">
	<div class="container">
		<p class="page-hero__eyebrow"><?php esc_html_e( 'Corporate Social Investment', 'awsisa' ); ?></p>
		<h1 class="page-hero__title"><?php esc_html_e( 'The Legacy Initiative', 'awsisa' ); ?></h1>
		<p class="page-hero__subtitle">
			<?php esc_html_e( 'Turning conversations into concrete change — funding rural sanitation infrastructure in underserved communities across Southern Africa.', 'awsisa' ); ?>
		</p>
		<div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
			<a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="btn btn--legacy btn--lg">
				<?php esc_html_e( 'Contribute Now', 'awsisa' ); ?>
			</a>
			<a href="#how-it-works" class="btn btn--outline btn--lg" style="border-color:rgba(255,255,255,.4);color:#fff;">
				<?php esc_html_e( 'How It Works', 'awsisa' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ── Impact stats ───────────────────────────────────────────────────────── -->
<section style="background:#166534;padding:2.5rem 0;border-bottom:1px solid rgba(255,255,255,.1);">
	<div class="container">
		<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;text-align:center;">
			<div>
				<p style="font-size:2.25rem;font-weight:800;color:#BBF7D0;margin:0;font-family:'Outfit',sans-serif;">12</p>
				<p style="color:#86EFAC;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Communities Reached', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.25rem;font-weight:800;color:#BBF7D0;margin:0;font-family:'Outfit',sans-serif;">4,800</p>
				<p style="color:#86EFAC;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'People Served', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.25rem;font-weight:800;color:#BBF7D0;margin:0;font-family:'Outfit',sans-serif;">R 3.2M</p>
				<p style="color:#86EFAC;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Invested to Date', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.25rem;font-weight:800;color:#BBF7D0;margin:0;font-family:'Outfit',sans-serif;">5</p>
				<p style="color:#86EFAC;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Countries', 'awsisa' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- ── What is the Legacy Initiative ─────────────────────────────────────── -->
<section class="section" id="how-it-works">
	<div class="container container--narrow">

		<div style="text-align:center;margin-bottom:2.5rem;">
			<span class="badge badge--success" style="margin-bottom:.75rem;"><?php esc_html_e( 'Our Commitment', 'awsisa' ); ?></span>
			<h2 style="color:#14532D;margin:0 0 1rem;"><?php esc_html_e( 'What Is the Legacy Initiative?', 'awsisa' ); ?></h2>
		</div>

		<p style="font-size:1.05rem;line-height:1.85;color:#334155;margin-bottom:1.25rem;">
			<?php esc_html_e( 'The AWSISA Legacy Initiative is our commitment that the Watersan Dialogue creates tangible, lasting impact beyond the conference venue. Every rand contributed by delegates, sponsors, and partners is ring-fenced for on-the-ground sanitation and water access projects in rural and peri-urban communities.', 'awsisa' ); ?>
		</p>
		<p style="font-size:1.05rem;line-height:1.85;color:#334155;margin-bottom:0;">
			<?php esc_html_e( 'Projects are selected in partnership with local municipalities, NGOs, and community leaders — ensuring interventions are community-owned, technically sound, and built to last.', 'awsisa' ); ?>
		</p>

	</div>
</section>

<!-- ── How your contribution is used ─────────────────────────────────────── -->
<section class="section" style="background:#F0FDF4;">
	<div class="container">

		<div style="text-align:center;margin-bottom:2.5rem;">
			<h2 style="color:#14532D;margin:0 0 .5rem;"><?php esc_html_e( 'How Your Contribution Is Used', 'awsisa' ); ?></h2>
			<p style="color:#16A34A;max-width:480px;margin:0 auto;">
				<?php esc_html_e( '100% of Legacy donations fund direct project costs. Admin and oversight are covered separately by AWSISA.', 'awsisa' ); ?>
			</p>
		</div>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.5rem;">

			<?php
			$uses = array(
				array(
					'icon'    => '🚽',
					'title'   => __( 'Sanitation Infrastructure', 'awsisa' ),
					'pct'     => '45%',
					'body'    => __( 'Construction and rehabilitation of communal ablution blocks, VIP latrines, and school WASH facilities.', 'awsisa' ),
				),
				array(
					'icon'    => '💧',
					'title'   => __( 'Water Access Points', 'awsisa' ),
					'pct'     => '30%',
					'body'    => __( 'Borehole drilling, pump installation, piped water reticulation, and rainwater harvesting systems.', 'awsisa' ),
				),
				array(
					'icon'    => '📚',
					'title'   => __( 'WASH Education', 'awsisa' ),
					'pct'     => '15%',
					'body'    => __( 'Hygiene promotion, school curricula, and community health worker training programmes.', 'awsisa' ),
				),
				array(
					'icon'    => '🔧',
					'title'   => __( 'Operations & Maintenance', 'awsisa' ),
					'pct'     => '10%',
					'body'    => __( 'Seed funding for community-managed O&M funds to ensure infrastructure remains functional long-term.', 'awsisa' ),
				),
			);
			foreach ( $uses as $u ) :
			?>
				<div class="card" style="padding:1.75rem;">
					<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
						<span style="font-size:1.75rem;"><?php echo $u['icon']; ?></span>
						<span style="background:#DCFCE7;color:#15803D;font-weight:700;font-size:.8rem;padding:.2rem .625rem;border-radius:20px;">
							<?php echo esc_html( $u['pct'] ); ?>
						</span>
					</div>
					<h3 style="font-size:1rem;font-weight:700;color:#14532D;margin:0 0 .5rem;"><?php echo esc_html( $u['title'] ); ?></h3>
					<p style="font-size:.875rem;color:#475569;line-height:1.7;margin:0;"><?php echo esc_html( $u['body'] ); ?></p>
				</div>
			<?php endforeach; ?>

		</div>

	</div>
</section>

<!-- ── 2026 Project Goal ───────────────────────────────────────────────────── -->
<section class="section">
	<div class="container container--narrow">

		<div style="background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border:1.5px solid #86EFAC;border-radius:1rem;padding:2.5rem;">

			<div style="text-align:center;margin-bottom:2rem;">
				<h2 style="color:#14532D;margin:0 0 .5rem;"><?php esc_html_e( '2026 Legacy Goal', 'awsisa' ); ?></h2>
				<p style="color:#166534;margin:0;">
					<?php esc_html_e( 'Funding a community sanitation hub in rural KwaZulu-Natal — serving 600 households.', 'awsisa' ); ?>
				</p>
			</div>

			<!-- Progress bar -->
			<div style="margin-bottom:.625rem;display:flex;justify-content:space-between;font-size:.875rem;font-weight:600;">
				<span style="color:#15803D;"><?php esc_html_e( 'R 0 raised', 'awsisa' ); ?></span>
				<span style="color:#14532D;"><?php esc_html_e( 'Goal: R 85,000', 'awsisa' ); ?></span>
			</div>
			<div style="background:#BBF7D0;border-radius:99px;height:12px;overflow:hidden;margin-bottom:1.5rem;">
				<div style="height:100%;width:0%;background:linear-gradient(90deg,#16A34A,#15803D);border-radius:99px;transition:width .6s ease;"></div>
			</div>

			<div style="text-align:center;">
				<a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="btn btn--legacy btn--lg">
					<?php esc_html_e( 'Be the First to Contribute', 'awsisa' ); ?>
				</a>
			</div>

		</div>

	</div>
</section>

<!-- ── Past Projects ──────────────────────────────────────────────────────── -->
<section class="section" style="background:#F8FAFC;">
	<div class="container">

		<div style="text-align:center;margin-bottom:2.5rem;">
			<h2 style="color:#0F172A;margin:0 0 .5rem;"><?php esc_html_e( 'Previous Legacy Projects', 'awsisa' ); ?></h2>
			<p style="color:#64748B;max-width:480px;margin:0 auto;">
				<?php esc_html_e( 'A record of the communities we have served through past Watersan Dialogues.', 'awsisa' ); ?>
			</p>
		</div>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;">

			<?php
			$projects = array(
				array(
					'year'      => '2025',
					'title'     => __( 'Limpopo Rural WASH Programme', 'awsisa' ),
					'location'  => __( 'Tzaneen District, Limpopo', 'awsisa' ),
					'impact'    => __( '320 households · 1,600 people', 'awsisa' ),
					'raised'    => 'R 420,000',
				),
				array(
					'year'      => '2024',
					'title'     => __( 'Eastern Cape Sanitation Upgrade', 'awsisa' ),
					'location'  => __( 'OR Tambo District, Eastern Cape', 'awsisa' ),
					'impact'    => __( '280 households · 1,400 people', 'awsisa' ),
					'raised'    => 'R 385,000',
				),
				array(
					'year'      => '2023',
					'title'     => __( 'Zimbabwe Community Boreholes', 'awsisa' ),
					'location'  => __( 'Masvingo Province, Zimbabwe', 'awsisa' ),
					'impact'    => __( '360 households · 1,800 people', 'awsisa' ),
					'raised'    => 'R 310,000',
				),
			);
			foreach ( $projects as $p ) :
			?>
				<div class="card" style="padding:1.75rem;">
					<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
						<span style="background:#14532D;color:#BBF7D0;font-weight:800;font-size:.8rem;padding:.25rem .75rem;border-radius:20px;font-family:'Outfit',sans-serif;">
							<?php echo esc_html( $p['year'] ); ?>
						</span>
					</div>
					<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .375rem;"><?php echo esc_html( $p['title'] ); ?></h3>
					<p style="font-size:.8rem;color:#0D9488;font-weight:600;margin:0 0 .375rem;">📍 <?php echo esc_html( $p['location'] ); ?></p>
					<p style="font-size:.8rem;color:#475569;margin:0 0 .375rem;">👥 <?php echo esc_html( $p['impact'] ); ?></p>
					<p style="font-size:.8rem;color:#16A34A;font-weight:600;margin:0;">💚 <?php echo esc_html( $p['raised'] ); ?> <?php esc_html_e( 'raised', 'awsisa' ); ?></p>
				</div>
			<?php endforeach; ?>

		</div>

	</div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────────────────── -->
<section class="section">
	<div class="container">
		<div style="text-align:center;padding:3rem;background:linear-gradient(135deg,#14532D,#166534);border-radius:1rem;">
			<h2 style="color:#FFFFFF;margin:0 0 .75rem;"><?php esc_html_e( 'Leave a Legacy Beyond the Dialogue', 'awsisa' ); ?></h2>
			<p style="color:#BBF7D0;margin:0 0 1.75rem;max-width:520px;margin-left:auto;margin-right:auto;">
				<?php esc_html_e( 'Every contribution — large or small — helps us build something permanent in a community that needs it most.', 'awsisa' ); ?>
			</p>
			<div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
				<a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="btn btn--legacy btn--lg">
					<?php esc_html_e( 'Donate to the Legacy Fund', 'awsisa' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline btn--lg" style="border-color:rgba(255,255,255,.4);color:#fff;">
					<?php esc_html_e( 'Partner With Us', 'awsisa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
