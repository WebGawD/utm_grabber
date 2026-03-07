<?php
/**
 * Template Name: Sponsors & Partners
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

$sponsors = array();
if ( defined( 'AWSISA_SUPABASE_URL' ) ) {
	$response = awsisa_supabase_request(
		'sponsors',
		'GET',
		array(),
		'is_active=eq.true&select=id,name,tier,logo_url,website_url,description,booth_number,booth_nfc_slug&order=tier.asc,name.asc'
	);
	if ( ! is_wp_error( $response ) ) {
		$sponsors = is_array( $response ) ? $response : array();
	}
}

$tiers = array(
	'platinum' => array( 'label' => 'Platinum Partners', 'color' => '#6B7280', 'bg' => '#F9FAFB', 'border' => '#E5E7EB', 'size' => '180px' ),
	'gold'     => array( 'label' => 'Gold Sponsors', 'color' => '#B45309', 'bg' => '#FFFBEB', 'border' => '#FDE68A', 'size' => '140px' ),
	'silver'   => array( 'label' => 'Silver Sponsors', 'color' => '#475569', 'bg' => '#F8FAFC', 'border' => '#E2E8F0', 'size' => '120px' ),
	'bronze'   => array( 'label' => 'Bronze Sponsors', 'color' => '#9A3412', 'bg' => '#FFF7ED', 'border' => '#FED7AA', 'size' => '100px' ),
	'partner'  => array( 'label' => 'Knowledge Partners', 'color' => '#1D4ED8', 'bg' => '#EFF6FF', 'border' => '#BFDBFE', 'size' => '110px' ),
	'media'    => array( 'label' => 'Media Partners', 'color' => '#374151', 'bg' => '#F9FAFB', 'border' => '#E5E7EB', 'size' => '100px' ),
);

// Group by tier.
$by_tier = array();
foreach ( $sponsors as $s ) {
	$by_tier[ $s['tier'] ][] = $s;
}
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#1E3A8A 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Watersan Dialogue 2026</p>
		<h1 class="page-hero__title">Sponsors &amp; Partners</h1>
		<p class="page-hero__subtitle">We thank the organisations making the AWSISA Watersan Dialogue possible.</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:1100px;">

		<?php if ( empty( $sponsors ) ) : ?>
			<div style="text-align:center;padding:4rem 2rem;background:#F8FAFC;border-radius:16px;">
				<div style="font-size:3rem;margin-bottom:1rem;">🤝</div>
				<h3 style="color:#0F172A;margin:0 0 .5rem;">Sponsorship Packages Coming Soon</h3>
				<p style="color:#64748B;margin:0 0 1.5rem;">Sponsorship opportunities for the 2026 dialogue will be announced shortly.</p>
				<a href="#become-a-sponsor" class="btn btn--primary">Become a Sponsor</a>
			</div>
		<?php else : ?>
			<?php foreach ( $tiers as $tier_key => $tier ) :
				$tier_sponsors = $by_tier[ $tier_key ] ?? array();
				if ( empty( $tier_sponsors ) ) continue;
				?>
				<div style="margin-bottom:3.5rem;">
					<h2 style="font-size:1.25rem;font-weight:800;color:<?php echo esc_attr( $tier['color'] ); ?>;text-transform:uppercase;letter-spacing:.1em;margin:0 0 1.5rem;padding-bottom:.75rem;border-bottom:2px solid <?php echo esc_attr( $tier['border'] ); ?>;">
						<?php echo esc_html( $tier['label'] ); ?>
					</h2>
					<div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:center;">
						<?php foreach ( $tier_sponsors as $sponsor ) : ?>
							<a href="<?php echo esc_url( ! empty( $sponsor['booth_nfc_slug'] ) ? home_url( '/booth/' . $sponsor['booth_nfc_slug'] . '/' ) : $sponsor['website_url'] ); ?>"
								<?php echo empty( $sponsor['booth_nfc_slug'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
								style="display:flex;align-items:center;justify-content:center;background:<?php echo esc_attr( $tier['bg'] ); ?>;border:1px solid <?php echo esc_attr( $tier['border'] ); ?>;border-radius:12px;padding:1.25rem;transition:transform .15s,box-shadow .15s;width:<?php echo esc_attr( $tier['size'] ); ?>;min-height:80px;">
								<?php if ( ! empty( $sponsor['logo_url'] ) ) : ?>
									<img src="<?php echo esc_url( $sponsor['logo_url'] ); ?>"
										alt="<?php echo esc_attr( $sponsor['name'] ); ?>"
										style="max-width:100%;max-height:60px;object-fit:contain;">
								<?php else : ?>
									<span style="font-size:.875rem;font-weight:600;color:#374151;text-align:center;"><?php echo esc_html( $sponsor['name'] ); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>

		<!-- Become a sponsor -->
		<div id="become-a-sponsor" style="background:linear-gradient(135deg,#0F172A,#115E59);border-radius:20px;padding:3rem;margin-top:2rem;text-align:center;">
			<h2 style="font-size:1.75rem;font-weight:800;color:#ffffff;margin:0 0 1rem;">Sponsor the 2026 Dialogue</h2>
			<p style="color:#94A3B8;font-size:1rem;max-width:560px;margin:0 auto 2rem;">Connect your brand with 5,000+ water and sanitation professionals from 54 African nations. Exhibition booths, NFC networking, speaking slots, and digital visibility included.</p>
			<div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-bottom:2.5rem;">
				<?php
				$packages = array(
					array( 'name' => 'Platinum', 'price' => 'R 250,000', 'perks' => '6 passes · Keynote slot · Booth · NFC brand tag · Digital swag' ),
					array( 'name' => 'Gold', 'price' => 'R 120,000', 'perks' => '4 passes · Panel seat · Booth · NFC brand tag · Swag bag' ),
					array( 'name' => 'Silver', 'price' => 'R 60,000', 'perks' => '2 passes · Booth · Logo placement · Digital swag' ),
					array( 'name' => 'Bronze', 'price' => 'R 25,000', 'perks' => '1 pass · Logo placement · Digital swag bag' ),
				);
				foreach ( $packages as $pkg ) : ?>
					<div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-radius:12px;padding:1.25rem 1.5rem;min-width:200px;text-align:left;">
						<div style="font-size:1rem;font-weight:800;color:#5EEAD4;"><?php echo esc_html( $pkg['name'] ); ?></div>
						<div style="font-size:1.4rem;font-weight:800;color:#ffffff;margin:.25rem 0;"><?php echo esc_html( $pkg['price'] ); ?></div>
						<div style="font-size:.8rem;color:#94A3B8;"><?php echo esc_html( $pkg['perks'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<a href="mailto:sponsorship@afriwater-san.africa?subject=Sponsorship Enquiry — Watersan 2026" class="btn btn--accent" style="font-size:1.05rem;padding:.9rem 2.5rem;">
				Get Sponsorship Prospectus
			</a>
		</div>

	</div>
</section>

<?php get_footer(); ?>
