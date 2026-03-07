<?php
/**
 * The footer template for the Awsisa theme.
 *
 * @package Awsisa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- #main-content -->

<footer class="site-footer" role="contentinfo" aria-label="<?php esc_attr_e( 'Site Footer', 'awsisa' ); ?>">
	<div class="container">
		<div class="footer__grid">

			<!-- Brand Column -->
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<svg class="footer__brand-logo" width="120" height="40" viewBox="0 0 200 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<circle cx="28" cy="28" r="28" fill="#0D9488"/>
						<path d="M14 36 C14 25 28 18 28 18 C28 18 42 25 42 36" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
						<ellipse cx="28" cy="39" rx="10" ry="5" fill="white" opacity="0.3"/>
						<circle cx="28" cy="38" r="4" fill="white"/>
						<text x="64" y="20" font-family="Outfit, sans-serif" font-size="14" font-weight="700" fill="white">AWSISA Africa</text>
						<text x="64" y="38" font-family="Outfit, sans-serif" font-size="11" font-weight="600" fill="#5EEAD4">Watersan Dialogue 2026</text>
					</svg>
				</a>

				<p class="footer__description">
					<?php esc_html_e( 'The AWSISA Africa &amp; Global South Water &amp; Sanitation Dialogue 2026 — an unparalleled gathering of water and sanitation stakeholders from across the value chain.', 'awsisa' ); ?>
				</p>

				<div class="footer__values">
					<span class="footer__value footer__value--solidarity"><?php esc_html_e( 'Solidarity', 'awsisa' ); ?></span>
					<span class="footer__value footer__value--equality"><?php esc_html_e( 'Equality', 'awsisa' ); ?></span>
					<span class="footer__value footer__value--sustainability"><?php esc_html_e( 'Sustainability', 'awsisa' ); ?></span>
				</div>

				<div class="footer__social" style="margin-top: 1.25rem;">
					<a href="https://twitter.com/AWSISA_Africa" class="footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Follow us on X (Twitter)', 'awsisa' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.261 5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
					</a>
					<a href="https://www.linkedin.com/company/awsisa" class="footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Connect on LinkedIn', 'awsisa' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
					</a>
					<a href="https://www.facebook.com/AWsisaAfrica" class="footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Like us on Facebook', 'awsisa' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
					</a>
				</div>
			</div>

			<!-- Quick Links -->
			<div>
				<h3 class="footer__heading"><?php esc_html_e( 'Quick Links', 'awsisa' ); ?></h3>
				<ul class="footer__links">
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About the Dialogue', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>"><?php esc_html_e( 'Programme', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/speakers/' ) ); ?>"><?php esc_html_e( 'Speakers', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/sponsors/' ) ); ?>"><?php esc_html_e( 'Sponsors & Partners', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/swag-bag/' ) ); ?>"><?php esc_html_e( 'Digital Swag Bag', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><?php esc_html_e( 'Legacy Initiative', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'awsisa' ); ?></a></li>
				</ul>
			</div>

			<!-- Delegates -->
			<div>
				<h3 class="footer__heading"><?php esc_html_e( 'Delegates', 'awsisa' ); ?></h3>
				<ul class="footer__links">
					<li><a href="<?php echo esc_url( awsisa_register_url() ); ?>"><?php esc_html_e( 'Register Now', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/accommodation/' ) ); ?>"><?php esc_html_e( 'Accommodation', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/visa-information/' ) ); ?>"><?php esc_html_e( 'Visa Information', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/travel/' ) ); ?>"><?php esc_html_e( 'Getting There', 'awsisa' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'awsisa' ); ?></a></li>
				</ul>
			</div>

			<!-- Contact -->
			<div>
				<h3 class="footer__heading"><?php esc_html_e( 'Contact', 'awsisa' ); ?></h3>
				<p class="footer__contact">
					<strong style="color: rgba(255,255,255,0.9);"><?php esc_html_e( 'Enquiries', 'awsisa' ); ?></strong><br>
					<a href="mailto:conference@afriwater-san.africa" style="color: #5EEAD4;">conference@afriwater-san.africa</a>
				</p>
				<p class="footer__contact">
					<strong style="color: rgba(255,255,255,0.9);"><?php esc_html_e( 'Venue', 'awsisa' ); ?></strong><br>
					Inkosi Albert Luthuli ICC<br>
					45 Bram Fischer Road, Durban<br>
					KwaZulu-Natal, South Africa
				</p>
				<p class="footer__contact" style="margin-top: 1rem;">
					<strong style="color: rgba(255,255,255,0.9);"><?php esc_html_e( 'Event Dates', 'awsisa' ); ?></strong><br>
					<?php echo awsisa_event_dates(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
				<p class="footer__contact" style="margin-top: 1rem;">
					<a href="https://www.awsisa-watersan-dialogue.org" target="_blank" rel="noopener noreferrer" style="color: #5EEAD4; font-size: 0.875rem;">
						www.awsisa-watersan-dialogue.org
					</a>
				</p>
			</div>
		</div><!-- .footer__grid -->
	</div><!-- .container -->

	<!-- Footer Bottom Bar -->
	<div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 0;">
		<div class="container">
			<div class="footer__bottom">
				<p>
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
					<?php
					printf(
						/* translators: %s: site name */
						esc_html__( 'AWSISA Africa. All rights reserved. #AWSISA2026 | #Afriwatersan26', 'awsisa' )
					);
					?>
				</p>
				<nav aria-label="<?php esc_attr_e( 'Legal navigation', 'awsisa' ); ?>">
					<ul style="display: flex; gap: 1.5rem; list-style: none; padding: 0; margin: 0; font-size: 0.8125rem; color: rgba(255,255,255,0.5);">
						<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color: rgba(255,255,255,0.5);"><?php esc_html_e( 'Privacy Policy (POPIA)', 'awsisa' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" style="color: rgba(255,255,255,0.5);"><?php esc_html_e( 'Terms', 'awsisa' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/paia-manual/' ) ); ?>" style="color: rgba(255,255,255,0.5);"><?php esc_html_e( 'PAIA Manual', 'awsisa' ); ?></a></li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
