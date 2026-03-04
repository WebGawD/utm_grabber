<?php
/**
 * Template Name: Legacy Initiative Donation
 *
 * @package Awsisa_Watersan_2026
 */

get_header();

wp_enqueue_script(
	'awsisa-donate-app',
	get_template_directory_uri() . '/assets/js/donate-app.js',
	array(),
	'1.0.0',
	true
);
wp_localize_script(
	'awsisa-donate-app',
	'awsisaDonate',
	array(
		'supabaseUrl'      => defined( 'AWSISA_SUPABASE_URL' ) ? AWSISA_SUPABASE_URL : '',
		'supabaseKey'      => defined( 'AWSISA_SUPABASE_ANON_KEY' ) ? AWSISA_SUPABASE_ANON_KEY : '',
		'restUrl'          => rest_url( 'awsisa/v1' ),
		'nonce'            => wp_create_nonce( 'wp_rest' ),
		'payfastMerchant'  => defined( 'AWSISA_PAYFAST_MERCHANT_ID' ) ? AWSISA_PAYFAST_MERCHANT_ID : '',
		'payfastKey'       => defined( 'AWSISA_PAYFAST_MERCHANT_KEY' ) ? AWSISA_PAYFAST_MERCHANT_KEY : '',
		'payfastSandbox'   => defined( 'AWSISA_PAYFAST_SANDBOX' ) && AWSISA_PAYFAST_SANDBOX,
		'privacyUrl'       => get_privacy_policy_url(),
		'successUrl'       => home_url( '/donate/thank-you/' ),
		'cancelUrl'        => home_url( '/donate/' ),
	)
);
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#14532D 0%,#16A34A 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">AWSISA Legacy Initiative</p>
		<h1 class="page-hero__title">Leave a Legacy in Water &amp; Sanitation</h1>
		<p class="page-hero__subtitle">Your donation funds scholarships, rural infrastructure, and WASH advocacy across Africa.</p>
	</div>
</section>

<div id="donate-app">

	<section class="section">
		<div class="container" style="max-width:860px;">

			<!-- Impact table -->
			<div class="section__header">
				<h2 class="section__title">Your Impact</h2>
				<p class="section__subtitle">Every Rand goes directly to programmes — no admin overhead charged to Legacy donations.</p>
			</div>

			<table style="width:100%;border-collapse:collapse;margin-bottom:3rem;font-size:.9rem;">
				<thead>
					<tr style="background:#F0FDF4;border-bottom:2px solid #BBF7D0;">
						<th style="text-align:left;padding:.75rem 1rem;color:#166534;">Donation</th>
						<th style="text-align:left;padding:.75rem 1rem;color:#166534;">Impact</th>
					</tr>
				</thead>
				<tbody>
					<tr style="border-bottom:1px solid #DCFCE7;">
						<td style="padding:.75rem 1rem;font-weight:700;color:#16A34A;">R 100</td>
						<td style="padding:.75rem 1rem;color:#475569;">Provides 500 litres of clean water to a rural household for one month</td>
					</tr>
					<tr style="border-bottom:1px solid #DCFCE7;background:#FAFAF9;">
						<td style="padding:.75rem 1rem;font-weight:700;color:#16A34A;">R 500</td>
						<td style="padding:.75rem 1rem;color:#475569;">Supplies 2 hand-washing stations for a rural school</td>
					</tr>
					<tr style="border-bottom:1px solid #DCFCE7;">
						<td style="padding:.75rem 1rem;font-weight:700;color:#16A34A;">R 1,000</td>
						<td style="padding:.75rem 1rem;color:#475569;">Funds one month's WASH training for a community health worker</td>
					</tr>
					<tr style="background:#FAFAF9;">
						<td style="padding:.75rem 1rem;font-weight:700;color:#16A34A;">R 5,000</td>
						<td style="padding:.75rem 1rem;color:#475569;">Co-sponsors a full university scholarship for an environmental engineering student</td>
					</tr>
				</tbody>
			</table>

			<!-- Donation form -->
			<div class="card" style="max-width:560px;margin:0 auto;border:2px solid #BBF7D0;">
				<h3 style="font-size:1.25rem;font-weight:700;color:#0F172A;margin:0 0 1.5rem;">Make a Donation</h3>

				<!-- Tier buttons -->
				<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:1.25rem;" id="donate-tiers">
					<?php
					$tiers = array(
						array( 'amount' => 100, 'label' => 'R 100' ),
						array( 'amount' => 500, 'label' => 'R 500' ),
						array( 'amount' => 1000, 'label' => 'R 1 000' ),
						array( 'amount' => 5000, 'label' => 'R 5 000' ),
					);
					foreach ( $tiers as $tier ) : ?>
						<button type="button"
							class="donate-tier btn btn--outline"
							data-amount="<?php echo esc_attr( $tier['amount'] ); ?>"
							style="font-weight:700;">
							<?php echo esc_html( $tier['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<form id="donate-form" novalidate>
					<div class="form-group">
						<label class="form-label" for="donate-amount">Amount (ZAR)</label>
						<div style="position:relative;">
							<span style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#64748B;font-weight:600;">R</span>
							<input type="number" id="donate-amount" name="amount_zar" class="form-control" style="padding-left:2.5rem;" placeholder="Enter amount" min="10" required>
						</div>
					</div>

					<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
						<div class="form-group">
							<label class="form-label" for="donate-first-name">First Name</label>
							<input type="text" id="donate-first-name" name="first_name" class="form-control" required>
						</div>
						<div class="form-group">
							<label class="form-label" for="donate-last-name">Last Name</label>
							<input type="text" id="donate-last-name" name="last_name" class="form-control" required>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label" for="donate-email">Email Address</label>
						<input type="email" id="donate-email" name="email" class="form-control" required>
					</div>

					<div class="form-group">
						<label class="form-label" for="donate-org">Organisation <span style="color:#94A3B8;font-weight:400;">(optional)</span></label>
						<input type="text" id="donate-org" name="organisation" class="form-control">
					</div>

					<div class="form-group">
						<label style="display:flex;align-items:flex-start;gap:.5rem;cursor:pointer;">
							<input type="checkbox" name="is_anonymous" id="donate-anon" style="margin-top:4px;">
							<span style="font-size:.875rem;color:#475569;">Make my donation anonymous (your name will not appear in the acknowledgements)</span>
						</label>
					</div>

					<!-- POPIA notice -->
					<div class="popia-block">
						<p style="font-size:.8rem;font-weight:600;color:#0F172A;margin:0 0 .5rem;">Privacy Notice (POPIA)</p>
						<p style="font-size:.8rem;color:#475569;margin:0 0 .75rem;">Your personal information is collected solely to process your donation and issue a tax certificate where applicable. It will not be sold or used for unrelated purposes.</p>
						<label style="display:flex;align-items:flex-start;gap:.5rem;cursor:pointer;">
							<input type="checkbox" name="popia_consent" required style="margin-top:2px;">
							<span style="font-size:.8rem;color:#374151;">I consent to AWSISA processing my personal information for the purposes described above. <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" style="color:#0D9488;">Privacy Policy</a></span>
						</label>
					</div>

					<button type="submit" id="donate-submit" class="btn btn--legacy" style="width:100%;margin-top:1.5rem;background:#16A34A;border-color:#16A34A;">
						Donate via PayFast
					</button>

					<p style="text-align:center;font-size:.75rem;color:#94A3B8;margin-top:.75rem;">
						Secure payment by PayFast · Your card details never touch our servers
					</p>
				</form>
			</div>

			<!-- Section 18A placeholder -->
			<div style="text-align:center;margin-top:2.5rem;padding:1.5rem;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:12px;">
				<p style="color:#166534;font-size:.875rem;margin:0;">
					🏛️ <strong>Section 18A Tax Certificates</strong> will be issued for donations of <strong>R 100 or more</strong> within 30 days. Certificates are sent to the email address provided.
				</p>
			</div>

		</div>
	</section>

</div><!-- #donate-app -->

<?php get_footer(); ?>
