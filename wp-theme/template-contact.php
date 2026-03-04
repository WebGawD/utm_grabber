<?php
/**
 * Template Name: Contact
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#0D9488 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Get in Touch</p>
		<h1 class="page-hero__title">Contact Us</h1>
		<p class="page-hero__subtitle">Reach our team for registration, accommodation, sponsorship, or media enquiries.</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:960px;">

		<div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;">

			<!-- Contact form -->
			<div>
				<h2 style="font-size:1.5rem;font-weight:700;color:#0F172A;margin:0 0 1.5rem;">Send Us a Message</h2>
				<form id="contact-form" novalidate>
					<?php wp_nonce_field( 'awsisa_contact', '_awsisa_contact_nonce' ); ?>

					<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
						<div class="form-group">
							<label class="form-label" for="contact-first">First Name</label>
							<input type="text" id="contact-first" name="first_name" class="form-control" required>
						</div>
						<div class="form-group">
							<label class="form-label" for="contact-last">Last Name</label>
							<input type="text" id="contact-last" name="last_name" class="form-control" required>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label" for="contact-email">Email Address</label>
						<input type="email" id="contact-email" name="email" class="form-control" required>
					</div>

					<div class="form-group">
						<label class="form-label" for="contact-org">Organisation</label>
						<input type="text" id="contact-org" name="organisation" class="form-control">
					</div>

					<div class="form-group">
						<label class="form-label" for="contact-subject">Subject</label>
						<select id="contact-subject" name="subject" class="form-control" required>
							<option value="">— Select topic —</option>
							<option value="registration">Delegate Registration</option>
							<option value="accommodation">Accommodation &amp; Bookings</option>
							<option value="sponsorship">Sponsorship &amp; Exhibiting</option>
							<option value="speaking">Speaking / Abstract Submission</option>
							<option value="media">Media &amp; Press</option>
							<option value="legacy">Legacy Initiative / Donations</option>
							<option value="general">General Enquiry</option>
						</select>
					</div>

					<div class="form-group">
						<label class="form-label" for="contact-message">Message</label>
						<textarea id="contact-message" name="message" class="form-control" rows="5" required></textarea>
					</div>

					<!-- POPIA -->
					<div class="popia-block">
						<label style="display:flex;align-items:flex-start;gap:.5rem;cursor:pointer;">
							<input type="checkbox" name="popia_consent" required style="margin-top:2px;">
							<span style="font-size:.8rem;color:#374151;">I consent to AWSISA processing my personal information to respond to my enquiry. <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" style="color:#0D9488;">Privacy Policy</a></span>
						</label>
					</div>

					<button type="submit" id="contact-submit" class="btn btn--primary" style="width:100%;margin-top:1.25rem;">
						Send Message
					</button>

					<div id="contact-success" style="display:none;margin-top:1rem;padding:1rem;background:#ECFDF5;border:1px solid #6EE7B7;border-radius:8px;color:#065F46;font-size:.9rem;">
						✅ Message sent! We'll respond within 2 business days.
					</div>
					<div id="contact-error" style="display:none;margin-top:1rem;padding:1rem;background:#FEF2F2;border:1px solid #FCA5A5;border-radius:8px;color:#991B1B;font-size:.9rem;">
						❌ Something went wrong. Please email us directly.
					</div>
				</form>
			</div>

			<!-- Contact info -->
			<div>
				<h2 style="font-size:1.5rem;font-weight:700;color:#0F172A;margin:0 0 1.5rem;">Contact Information</h2>

				<?php
				$contacts = array(
					array(
						'dept'  => 'General Enquiries',
						'email' => 'info@afriwater-san.africa',
						'icon'  => '📬',
					),
					array(
						'dept'  => 'Delegate Registration',
						'email' => 'conference@afriwater-san.africa',
						'icon'  => '🎟️',
					),
					array(
						'dept'  => 'Accommodation',
						'email' => 'accommodation@afriwater-san.africa',
						'icon'  => '🏨',
					),
					array(
						'dept'  => 'Sponsorship & Exhibitions',
						'email' => 'sponsorship@afriwater-san.africa',
						'icon'  => '🤝',
					),
					array(
						'dept'  => 'Media & Press',
						'email' => 'media@afriwater-san.africa',
						'icon'  => '📰',
					),
					array(
						'dept'  => 'Legacy Initiative',
						'email' => 'legacy@afriwater-san.africa',
						'icon'  => '🌱',
					),
				);
				foreach ( $contacts as $c ) : ?>
					<div style="display:flex;gap:1rem;margin-bottom:1.25rem;">
						<div style="font-size:1.5rem;flex-shrink:0;"><?php echo $c['icon']; ?></div>
						<div>
							<div style="font-weight:700;color:#0F172A;font-size:.9rem;"><?php echo esc_html( $c['dept'] ); ?></div>
							<a href="mailto:<?php echo esc_attr( $c['email'] ); ?>" style="color:#0D9488;font-size:.875rem;"><?php echo esc_html( $c['email'] ); ?></a>
						</div>
					</div>
				<?php endforeach; ?>

				<div style="margin-top:2rem;padding:1.25rem;background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;">
					<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .75rem;">📍 Venue</h3>
					<p style="font-size:.875rem;color:#475569;margin:0 0 .5rem;"><strong>Emperors Palace Hotel Casino Convention Resort</strong></p>
					<p style="font-size:.875rem;color:#475569;margin:0 0 .5rem;">64 Jones Road, Kempton Park, 1620<br>Gauteng, South Africa</p>
					<a href="https://maps.google.com/?q=Emperors+Palace+Kempton+Park" target="_blank" rel="noopener" style="font-size:.8rem;color:#0D9488;">Open in Google Maps →</a>
				</div>

				<div style="margin-top:1.25rem;padding:1.25rem;background:#FFF7ED;border:1px solid #FED7AA;border-radius:12px;">
					<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .5rem;">⏰ Office Hours</h3>
					<p style="font-size:.875rem;color:#475569;margin:0;">Monday – Friday: 08:00 – 17:00 SAST<br>We aim to respond within 2 business days.</p>
				</div>

				<!-- Social -->
				<div style="margin-top:2rem;">
					<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .75rem;">Follow the Dialogue</h3>
					<div style="display:flex;gap:.75rem;">
						<a href="https://twitter.com/AWSISA_Official" target="_blank" rel="noopener" class="btn btn--outline" style="padding:.5rem 1rem;font-size:.8rem;">𝕏 Twitter</a>
						<a href="https://www.linkedin.com/company/awsisa" target="_blank" rel="noopener" class="btn btn--outline" style="padding:.5rem 1rem;font-size:.8rem;">LinkedIn</a>
						<a href="https://www.facebook.com/AWAfrica" target="_blank" rel="noopener" class="btn btn--outline" style="padding:.5rem 1rem;font-size:.8rem;">Facebook</a>
					</div>
				</div>
			</div>

		</div>

	</div>
</section>

<script>
( function () {
	const form    = document.getElementById( 'contact-form' );
	const success = document.getElementById( 'contact-success' );
	const error   = document.getElementById( 'contact-error' );
	const submit  = document.getElementById( 'contact-submit' );
	if ( ! form ) return;

	form.addEventListener( 'submit', async function ( e ) {
		e.preventDefault();
		if ( ! form.checkValidity() ) { form.reportValidity(); return; }

		submit.disabled    = true;
		submit.textContent = 'Sending…';

		const data = Object.fromEntries( new FormData( form ) );

		try {
			const res = await fetch(
				'<?php echo esc_url( rest_url( "awsisa/v1/contact" ) ); ?>',
				{
					method:  'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce':   '<?php echo esc_js( wp_create_nonce( "wp_rest" ) ); ?>',
					},
					body: JSON.stringify( data ),
				}
			);
			if ( res.ok ) {
				form.reset();
				success.style.display = '';
				error.style.display   = 'none';
			} else {
				throw new Error( 'Server error' );
			}
		} catch ( err ) {
			error.style.display   = '';
			success.style.display = 'none';
		} finally {
			submit.disabled    = false;
			submit.textContent = 'Send Message';
		}
	} );
} )();
</script>

<?php get_footer(); ?>
