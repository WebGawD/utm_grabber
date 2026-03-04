<?php
/**
 * Template Name: Delegate Registration
 * Template Post Type: page
 *
 * Multi-step delegate registration page.
 * Embeds the registration React app and falls back to a server-rendered
 * form if the React bundle has not been built yet.
 *
 * @package Awsisa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue the registration React app (built via npm run build in /registration-app).
$reg_app_js = get_template_directory_uri() . '/assets/js/registration-app.js';
wp_enqueue_script( 'awsisa-reg-app', $reg_app_js, array(), '1.0.0', true );
wp_localize_script( 'awsisa-reg-app', 'awsisaReg', array(
	'supabaseUrl' => AWSISA_SUPABASE_URL,
	'supabaseKey' => AWSISA_SUPABASE_ANON_KEY,
	'restUrl'     => esc_url_raw( rest_url( 'awsisa/v1/' ) ),
	'nonce'       => wp_create_nonce( 'awsisa_rest' ),
	'successUrl'  => esc_url( home_url( '/register/success/' ) ),
	'privacyUrl'  => esc_url( home_url( '/privacy-policy/' ) ),
	'pricing'     => array(
		'government'     => array( 'zar' => 4500,  'usd' => 250 ),
		'utility'        => array( 'zar' => 5500,  'usd' => 305 ),
		'private_sector' => array( 'zar' => 8500,  'usd' => 472 ),
		'ngo'            => array( 'zar' => 3500,  'usd' => 194 ),
		'academic'       => array( 'zar' => 2800,  'usd' => 155 ),
		'media'          => array( 'zar' => 0,     'usd' => 0 ),
		'exhibitor'      => array( 'zar' => 12000, 'usd' => 667 ),
	),
) );

get_header();
?>

<section class="page-hero">
	<div class="container">
		<span class="badge" style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 1rem;"><?php esc_html_e( 'Delegate Registration', 'awsisa' ); ?></span>
		<h1 class="page-hero__title"><?php esc_html_e( 'Register for AWSISA Watersan Dialogue 2026', 'awsisa' ); ?></h1>
		<p class="page-hero__subtitle">
			<?php esc_html_e( 'Secure your place at the most important water and sanitation gathering in the Global South.', 'awsisa' ); ?>
			<strong style="color: #5EEAD4;"><?php echo esc_html( awsisa_event_dates() ); ?></strong>
		</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width: 860px;">

		<!-- React Registration App mounts here -->
		<div id="awsisa-registration-app" data-loading="true">

			<!-- Fallback / skeleton while React loads -->
			<div class="steps" aria-label="<?php esc_attr_e( 'Registration steps', 'awsisa' ); ?>">
				<?php
				$steps = array(
					esc_html__( 'Personal Info', 'awsisa' ),
					esc_html__( 'Delegate Type', 'awsisa' ),
					esc_html__( 'Accommodation', 'awsisa' ),
					esc_html__( 'Payment', 'awsisa' ),
					esc_html__( 'Review & Submit', 'awsisa' ),
				);
				foreach ( $steps as $i => $label ) :
					$active    = ( 0 === $i ) ? 'is-active' : '';
					$connector = ( $i < count( $steps ) - 1 ) ? '<div class="step__connector"></div>' : '';
					?>
					<div class="step <?php echo esc_attr( $active ); ?>">
						<div class="step__indicator"><?php echo esc_html( $i + 1 ); ?></div>
						<span class="step__label"><?php echo esc_html( $label ); ?></span>
					</div>
					<?php echo $connector; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>

			<!-- Step 1: Personal Information (server-rendered fallback) -->
			<div class="card" style="margin-top: 2rem;">
				<div class="card__body">
					<h2 style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Personal Information', 'awsisa' ); ?></h2>
					<p style="color: #64748B; margin-bottom: 2rem;"><?php esc_html_e( 'Please enter your details as they should appear on your conference badge.', 'awsisa' ); ?></p>

					<form
						id="awsisa-reg-form-step1"
						class="awsisa-form"
						method="post"
						action="<?php echo esc_url( rest_url( 'awsisa/v1/register' ) ); ?>"
					>
						<?php wp_nonce_field( 'awsisa_register', 'awsisa_nonce' ); ?>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
							<div class="form-group">
								<label class="form-label" for="first_name">
									<?php esc_html_e( 'First Name', 'awsisa' ); ?><span class="required">*</span>
								</label>
								<input
									type="text"
									id="first_name"
									name="first_name"
									class="form-control"
									placeholder="<?php esc_attr_e( 'e.g. Thandiwe', 'awsisa' ); ?>"
									required
									autocomplete="given-name"
								>
							</div>
							<div class="form-group">
								<label class="form-label" for="last_name">
									<?php esc_html_e( 'Last Name', 'awsisa' ); ?><span class="required">*</span>
								</label>
								<input
									type="text"
									id="last_name"
									name="last_name"
									class="form-control"
									placeholder="<?php esc_attr_e( 'e.g. Nkosi', 'awsisa' ); ?>"
									required
									autocomplete="family-name"
								>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="email">
								<?php esc_html_e( 'Email Address', 'awsisa' ); ?><span class="required">*</span>
							</label>
							<input
								type="email"
								id="email"
								name="email"
								class="form-control"
								placeholder="<?php esc_attr_e( 'you@organisation.com', 'awsisa' ); ?>"
								required
								autocomplete="email"
							>
							<span class="form-help"><?php esc_html_e( 'Your QR code and confirmation will be sent to this address.', 'awsisa' ); ?></span>
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
							<div class="form-group">
								<label class="form-label" for="organisation"><?php esc_html_e( 'Organisation', 'awsisa' ); ?></label>
								<input type="text" id="organisation" name="organisation" class="form-control" autocomplete="organization">
							</div>
							<div class="form-group">
								<label class="form-label" for="job_title"><?php esc_html_e( 'Job Title', 'awsisa' ); ?></label>
								<input type="text" id="job_title" name="job_title" class="form-control" autocomplete="organization-title">
							</div>
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
							<div class="form-group">
								<label class="form-label" for="country">
									<?php esc_html_e( 'Country', 'awsisa' ); ?><span class="required">*</span>
								</label>
								<select id="country" name="country" class="form-control" required autocomplete="country-name">
									<option value=""><?php esc_html_e( 'Select country…', 'awsisa' ); ?></option>
									<?php
									$countries = array(
										'ZA' => 'South Africa', 'NG' => 'Nigeria', 'KE' => 'Kenya',
										'ET' => 'Ethiopia', 'EG' => 'Egypt', 'GH' => 'Ghana',
										'TZ' => 'Tanzania', 'UG' => 'Uganda', 'MZ' => 'Mozambique',
										'ZW' => 'Zimbabwe', 'ZM' => 'Zambia', 'MW' => 'Malawi',
										'AO' => 'Angola', 'NA' => 'Namibia', 'BW' => 'Botswana',
										'RW' => 'Rwanda', 'SN' => 'Senegal', 'CM' => 'Cameroon',
										'CI' => "Côte d'Ivoire", 'ML' => 'Mali', 'BF' => 'Burkina Faso',
										'MG' => 'Madagascar', 'SD' => 'Sudan', 'DZ' => 'Algeria',
										'MA' => 'Morocco', 'TN' => 'Tunisia',
										'--' => '──────────────',
										'IN' => 'India', 'BD' => 'Bangladesh', 'PK' => 'Pakistan',
										'BR' => 'Brazil', 'CO' => 'Colombia', 'PE' => 'Peru',
										'GB' => 'United Kingdom', 'US' => 'United States',
										'DE' => 'Germany', 'FR' => 'France', 'NL' => 'Netherlands',
									);
									foreach ( $countries as $code => $name ) :
										if ( '--' === $code ) :
											?>
											<option disabled>──────────────</option>
											<?php
										else :
											?>
											<option value="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></option>
											<?php
										endif;
									endforeach;
									?>
								</select>
							</div>
							<div class="form-group">
								<label class="form-label" for="phone"><?php esc_html_e( 'Phone / WhatsApp', 'awsisa' ); ?></label>
								<input type="tel" id="phone" name="phone" class="form-control" placeholder="+27 xx xxx xxxx" autocomplete="tel">
								<span class="form-help"><?php esc_html_e( 'For Flash Alerts and urgent communication during the event.', 'awsisa' ); ?></span>
							</div>
						</div>

						<div class="popia-block">
							<div class="popia-block__title">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
								<?php esc_html_e( 'POPIA Consent (Protection of Personal Information Act)', 'awsisa' ); ?>
							</div>
							<div class="popia-block__items">
								<label class="form-check">
									<input type="checkbox" name="popia_consent" value="1" class="form-check__input" required>
									<span class="form-check__label">
										<?php
										printf(
											/* translators: %s: privacy policy link */
											esc_html__( 'I consent to AWSISA Africa processing my personal information for event management and communication purposes, as described in the %s.', 'awsisa' ),
											'<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Privacy Policy', 'awsisa' ) . '</a>'
										);
										?>
										<strong style="color: #DC2626;"> *</strong>
									</span>
								</label>
								<label class="form-check">
									<input type="checkbox" name="marketing_consent" value="1" class="form-check__input">
									<span class="form-check__label">
										<?php esc_html_e( 'I consent to receive future AWSISA event news and water sector updates by email. (Optional — you may unsubscribe at any time.)', 'awsisa' ); ?>
									</span>
								</label>
								<label class="form-check">
									<input type="checkbox" name="profile_public" value="1" class="form-check__input" checked>
									<span class="form-check__label">
										<?php esc_html_e( 'I consent to my name, organisation and job title being visible to other delegates for networking purposes.', 'awsisa' ); ?>
									</span>
								</label>
							</div>
						</div>

						<div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
							<button type="submit" class="btn btn--primary btn--lg">
								<?php esc_html_e( 'Continue to Delegate Type', 'awsisa' ); ?>
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- #awsisa-registration-app -->

	</div>
</section>

<?php get_footer(); ?>
