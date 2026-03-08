<?php
/**
 * Template Name: About
 *
 * About AWSISA and the Watersan Dialogue 2026 conference.
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#134E4A 100%);">
	<div class="container">
		<p class="page-hero__eyebrow"><?php esc_html_e( 'Who We Are', 'awsisa' ); ?></p>
		<h1 class="page-hero__title"><?php esc_html_e( 'About AWSISA', 'awsisa' ); ?></h1>
		<p class="page-hero__subtitle">
			<?php esc_html_e( 'Advancing water security and sanitation across Africa through dialogue, research, and action.', 'awsisa' ); ?>
		</p>
	</div>
</section>

<!-- ── Mission & Vision ───────────────────────────────────────────────────── -->
<section class="section">
	<div class="container container--narrow">

		<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:3rem;">

			<div style="background:#F0FDFA;border-left:4px solid #0D9488;border-radius:0 10px 10px 0;padding:1.75rem 2rem;">
				<h2 style="color:#134E4A;font-size:1.1rem;text-transform:uppercase;letter-spacing:.08em;margin:0 0 .75rem;">
					<?php esc_html_e( 'Our Mission', 'awsisa' ); ?>
				</h2>
				<p style="color:#0F172A;line-height:1.75;margin:0;">
					<?php esc_html_e( 'To convene Africa\'s leading water and sanitation practitioners, policymakers, and innovators in an annual dialogue that accelerates progress toward universal access to safe water and dignified sanitation.', 'awsisa' ); ?>
				</p>
			</div>

			<div style="background:#F0FDF4;border-left:4px solid #16A34A;border-radius:0 10px 10px 0;padding:1.75rem 2rem;">
				<h2 style="color:#14532D;font-size:1.1rem;text-transform:uppercase;letter-spacing:.08em;margin:0 0 .75rem;">
					<?php esc_html_e( 'Our Vision', 'awsisa' ); ?>
				</h2>
				<p style="color:#0F172A;line-height:1.75;margin:0;">
					<?php esc_html_e( 'An Africa where every person has reliable access to clean water and safe sanitation — a continent where water security underpins health, economic growth, and human dignity.', 'awsisa' ); ?>
				</p>
			</div>

		</div>

		<!-- About the organisation -->
		<p style="font-size:1.05rem;line-height:1.85;color:#334155;margin-bottom:1.25rem;">
			<?php esc_html_e( 'The African Water and Sanitation Initiative for Southern Africa (AWSISA) was founded to bridge the gap between policy ambition and implementation reality. We bring together government bodies, utilities, NGOs, development finance institutions, and private sector partners to share knowledge, challenge assumptions, and commit to measurable outcomes.', 'awsisa' ); ?>
		</p>
		<p style="font-size:1.05rem;line-height:1.85;color:#334155;margin-bottom:0;">
			<?php esc_html_e( 'The annual Watersan Dialogue is our flagship event — a four-day immersive conference featuring keynote addresses, technical workshops, field excursions, and networking that has become the premier gathering for water and sanitation professionals on the continent.', 'awsisa' ); ?>
		</p>

	</div>
</section>

<!-- ── Stats bar ─────────────────────────────────────────────────────────── -->
<section class="section" style="background:#0F172A;padding:3rem 0;">
	<div class="container">
		<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;text-align:center;">

			<div>
				<p style="font-size:2.5rem;font-weight:800;color:#5EEAD4;margin:0;font-family:'Outfit',sans-serif;">2026</p>
				<p style="color:#94A3B8;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Conference Year', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.5rem;font-weight:800;color:#5EEAD4;margin:0;font-family:'Outfit',sans-serif;">500+</p>
				<p style="color:#94A3B8;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Expected Delegates', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.5rem;font-weight:800;color:#5EEAD4;margin:0;font-family:'Outfit',sans-serif;">30+</p>
				<p style="color:#94A3B8;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Countries Represented', 'awsisa' ); ?></p>
			</div>
			<div>
				<p style="font-size:2.5rem;font-weight:800;color:#5EEAD4;margin:0;font-family:'Outfit',sans-serif;">40+</p>
				<p style="color:#94A3B8;margin:.25rem 0 0;font-size:.875rem;"><?php esc_html_e( 'Sessions & Workshops', 'awsisa' ); ?></p>
			</div>

		</div>
	</div>
</section>

<!-- ── Conference Themes ──────────────────────────────────────────────────── -->
<section class="section">
	<div class="container">

		<div style="text-align:center;margin-bottom:2.5rem;">
			<span class="badge badge--primary" style="margin-bottom:.75rem;"><?php esc_html_e( 'Watersan Dialogue 2026', 'awsisa' ); ?></span>
			<h2 style="color:#0F172A;margin:0 0 .75rem;"><?php esc_html_e( 'Conference Themes', 'awsisa' ); ?></h2>
			<p style="color:#64748B;max-width:540px;margin:0 auto;">
				<?php esc_html_e( '9 – 12 November 2026 · ICC Durban, KwaZulu-Natal', 'awsisa' ); ?>
			</p>
		</div>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;">

			<?php
			$themes = array(
				array(
					'icon'  => '💧',
					'title' => __( 'Water Security & Governance', 'awsisa' ),
					'body'  => __( 'Strengthening institutional frameworks, transboundary water management, and national water resource strategies.', 'awsisa' ),
				),
				array(
					'icon'  => '🚿',
					'title' => __( 'Sanitation & Hygiene at Scale', 'awsisa' ),
					'body'  => __( 'Scaling WASH services in urban informal settlements, peri-urban areas, and rural communities across Southern Africa.', 'awsisa' ),
				),
				array(
					'icon'  => '🌍',
					'title' => __( 'Climate Resilience & Water', 'awsisa' ),
					'body'  => __( 'Adapting water infrastructure and management practices to increasing climate variability, droughts, and flooding.', 'awsisa' ),
				),
				array(
					'icon'  => '💰',
					'title' => __( 'Financing & Investment', 'awsisa' ),
					'body'  => __( 'Mobilising development finance, blended finance, and private capital to close the infrastructure funding gap.', 'awsisa' ),
				),
				array(
					'icon'  => '📡',
					'title' => __( 'Digital Utilities & Innovation', 'awsisa' ),
					'body'  => __( 'Leveraging smart metering, remote sensing, AI, and data analytics to improve utility performance and accountability.', 'awsisa' ),
				),
				array(
					'icon'  => '🤝',
					'title' => __( 'Partnerships & South-South Learning', 'awsisa' ),
					'body'  => __( 'Facilitating peer exchange between African utilities, governments, and practitioners to transfer proven approaches.', 'awsisa' ),
				),
			);
			foreach ( $themes as $theme ) :
			?>
				<div class="card" style="padding:1.75rem;">
					<div style="font-size:2rem;margin-bottom:.875rem;"><?php echo $theme['icon']; ?></div>
					<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .5rem;"><?php echo esc_html( $theme['title'] ); ?></h3>
					<p style="font-size:.875rem;color:#475569;line-height:1.7;margin:0;"><?php echo esc_html( $theme['body'] ); ?></p>
				</div>
			<?php endforeach; ?>

		</div>

	</div>
</section>

<!-- ── Organising Committee ───────────────────────────────────────────────── -->
<section class="section" style="background:#F8FAFC;">
	<div class="container">

		<div style="text-align:center;margin-bottom:2.5rem;">
			<h2 style="color:#0F172A;margin:0 0 .5rem;"><?php esc_html_e( 'Organising Committee', 'awsisa' ); ?></h2>
			<p style="color:#64748B;max-width:480px;margin:0 auto;">
				<?php esc_html_e( 'Details of the 2026 organising committee will be published here shortly.', 'awsisa' ); ?>
			</p>
		</div>

		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1.25rem;">
			<?php for ( $i = 0; $i < 6; $i++ ) : ?>
				<div class="card" style="padding:1.5rem;text-align:center;opacity:.45;">
					<div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#CBD5E1,#E2E8F0);margin:0 auto 1rem;"></div>
					<div style="height:.875rem;background:#E2E8F0;border-radius:4px;width:70%;margin:0 auto .5rem;"></div>
					<div style="height:.75rem;background:#F1F5F9;border-radius:4px;width:55%;margin:0 auto;"></div>
				</div>
			<?php endfor; ?>
		</div>

	</div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────────────────── -->
<section class="section">
	<div class="container">
		<div style="text-align:center;padding:3rem;background:linear-gradient(135deg,#F0FDFA,#CCFBF1);border-radius:1rem;">
			<h2 style="color:#134E4A;margin:0 0 .75rem;"><?php esc_html_e( 'Join Us in Durban', 'awsisa' ); ?></h2>
			<p style="color:#0F766E;margin:0 0 1.75rem;max-width:500px;margin-left:auto;margin-right:auto;">
				<?php esc_html_e( 'Secure your place at Africa\'s premier water and sanitation conference — 9 to 12 November 2026 at the ICC Durban.', 'awsisa' ); ?>
			</p>
			<div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
				<a href="<?php echo esc_url( awsisa_register_url() ); ?>" class="btn btn--primary btn--lg">
					<?php esc_html_e( 'Register Now', 'awsisa' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline btn--lg">
					<?php esc_html_e( 'Get in Touch', 'awsisa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
