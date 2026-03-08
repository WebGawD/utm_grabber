<?php
/**
 * Template Name: FAQ
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#0D9488 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Delegates</p>
		<h1 class="page-hero__title">Frequently Asked Questions</h1>
		<p class="page-hero__subtitle">Answers to the most common questions about the Watersan Dialogue 2026.</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:800px;">

		<?php
		$faq_sections = array(
			array(
				'section' => 'Registration & Tickets',
				'icon'    => '🎟️',
				'items'   => array(
					array(
						'q' => 'How do I register as a delegate?',
						'a' => 'Visit our <a href="' . esc_url( home_url( '/register/' ) ) . '" style="color:#0D9488;">Registration page</a> and complete the online form. You will receive a confirmation email with your delegate reference number and QR code ticket.',
					),
					array(
						'q' => 'What delegate categories are available?',
						'a' => 'We offer: <strong>In-Person Delegate</strong> (full access to ICC venue and all sessions), <strong>Virtual Delegate</strong> (live-streamed sessions and digital resources), <strong>Exhibitor/Sponsor</strong> (booth access + delegate pass), and <strong>Press/Media</strong> (journalist accreditation). Each category has different pricing — see the registration page for details.',
					),
					array(
						'q' => 'Can I attend virtually?',
						'a' => 'Yes. A virtual attendance option is available providing access to live-streamed plenary sessions and keynotes via our delegate app. Select "Virtual Delegate" during registration.',
					),
					array(
						'q' => 'Is there a group discount?',
						'a' => 'Yes — groups of 5 or more from the same organisation receive a 10% discount. Please contact <a href="mailto:conference@afriwater-san.africa" style="color:#0D9488;">conference@afriwater-san.africa</a> to arrange group registration.',
					),
					array(
						'q' => 'What is the cancellation / refund policy?',
						'a' => 'Cancellations made more than 30 days before the event receive a full refund less an administration fee. Cancellations within 30 days are non-refundable but a substitute delegate may attend in your place. Please notify us in writing.',
					),
				),
			),
			array(
				'section' => 'Venue & Logistics',
				'icon'    => '🏛️',
				'items'   => array(
					array(
						'q' => 'Where is the conference being held?',
						'a' => 'The Watersan Dialogue 2026 takes place at the <strong>Inkosi Albert Luthuli International Convention Centre (ICC)</strong>, 45 Bram Fischer Road, Durban, 4001, KwaZulu-Natal, South Africa.',
					),
					array(
						'q' => 'What are the conference dates and times?',
						'a' => 'Please refer to the homepage or contact the secretariat for the finalised dates. Sessions typically run from 08:00 to 18:00 SAST each day, with the gala dinner on the final evening.',
					),
					array(
						'q' => 'Is there parking at the ICC?',
						'a' => 'Yes, underground parking is available at the ICC and at the adjacent parking garage on Bram Fischer Road. Parking costs approximately R20–40 per hour. We recommend using Uber or e-hailing services to avoid parking.',
					),
					array(
						'q' => 'Are meals provided?',
						'a' => 'Morning refreshments, lunch and afternoon tea are provided for in-person delegates each conference day. Evening networking events may have their own catering arrangements. Dietary requirements can be noted during registration.',
					),
					array(
						'q' => 'Is the ICC accessible for delegates with disabilities?',
						'a' => 'Yes. The ICC Durban is fully accessible with ramps, lifts, accessible parking bays, and accessible ablution facilities throughout the venue. Please indicate any accessibility requirements during registration so we can make appropriate arrangements.',
					),
				),
			),
			array(
				'section' => 'Accommodation & Travel',
				'icon'    => '✈️',
				'items'   => array(
					array(
						'q' => 'Has the conference arranged hotel rates?',
						'a' => 'Yes. The secretariat has negotiated preferential rates at selected hotels near the ICC. Visit our <a href="' . esc_url( home_url( '/accommodation/' ) ) . '" style="color:#0D9488;">Accommodation page</a> to book at the conference rates.',
					),
					array(
						'q' => 'Do I need a visa to attend?',
						'a' => 'Citizens of many countries can enter South Africa visa-free. If you require a visa, the secretariat can provide an official invitation letter to support your application. See our <a href="' . esc_url( home_url( '/visa-information/' ) ) . '" style="color:#0D9488;">Visa Information page</a> for details.',
					),
					array(
						'q' => 'Which airport should I fly into?',
						'a' => 'Fly into <strong>King Shaka International Airport (DUR)</strong>, approximately 35 km from the ICC. Most international delegates connect through OR Tambo International (JNB) in Johannesburg. See our <a href="' . esc_url( home_url( '/travel/' ) ) . '" style="color:#0D9488;">Getting There</a> page.',
					),
				),
			),
			array(
				'section' => 'Programme & Speakers',
				'icon'    => '📋',
				'items'   => array(
					array(
						'q' => 'Where can I view the conference programme?',
						'a' => 'The full programme is available on the <a href="' . esc_url( home_url( '/agenda/' ) ) . '" style="color:#0D9488;">Programme page</a> and in the delegate app (available closer to the event).',
					),
					array(
						'q' => 'Can I submit a paper or presentation?',
						'a' => 'Abstract submissions are reviewed by the programme committee. Contact <a href="mailto:conference@afriwater-san.africa" style="color:#0D9488;">conference@afriwater-san.africa</a> with your proposed title and a 250-word abstract for consideration.',
					),
					array(
						'q' => 'Will sessions be recorded?',
						'a' => 'Selected plenary sessions and keynotes will be recorded. Recordings will be made available to registered delegates after the event. Individual speakers may opt out of recording.',
					),
					array(
						'q' => 'Is there CPD accreditation?',
						'a' => 'We are exploring CPD accreditation with relevant professional bodies including WISA and ECSA. Updates will be communicated to registered delegates.',
					),
				),
			),
			array(
				'section' => 'Networking & Digital',
				'icon'    => '📱',
				'items'   => array(
					array(
						'q' => 'Is there a conference app?',
						'a' => 'Yes. The Watersan Dialogue 2026 delegate app will be available prior to the event. It includes the programme, venue map, networking features, real-time announcements, and your digital ticket. You will receive access instructions by email after registration.',
					),
					array(
						'q' => 'How does the QR networking work?',
						'a' => 'Each delegate receives a unique QR code on their badge and in the app. Scan another delegate\'s QR code to instantly exchange contact details and save them to your networking contacts list within the app.',
					),
					array(
						'q' => 'Is there Wi-Fi at the ICC?',
						'a' => 'Yes. Complimentary high-speed Wi-Fi is available throughout the ICC venue. Access credentials will be provided at registration check-in.',
					),
					array(
						'q' => 'Will there be a digital swag bag?',
						'a' => 'Yes! Sponsor materials, brochures, presentations and resources are available via the Digital Swag Bag in the delegate app — reducing physical waste while ensuring you don\'t miss any valuable content.',
					),
				),
			),
			array(
				'section' => 'Media & Press',
				'icon'    => '📰',
				'items'   => array(
					array(
						'q' => 'How do journalists register?',
						'a' => 'Accredited media representatives can apply for press credentials by emailing <a href="mailto:media@afriwater-san.africa" style="color:#0D9488;">media@afriwater-san.africa</a> with a letter of assignment from your editor or publication.',
					),
					array(
						'q' => 'Is there a media centre at the venue?',
						'a' => 'Yes. A dedicated media centre with workstations, power, high-speed internet and a press officer is available at the ICC throughout the conference.',
					),
				),
			),
		);
		?>

		<?php foreach ( $faq_sections as $idx => $section ) : ?>
			<div style="margin-bottom:3rem;">
				<h2 style="font-size:1.2rem;font-weight:700;color:#0D9488;margin:0 0 1.25rem;display:flex;align-items:center;gap:.5rem;padding-bottom:.75rem;border-bottom:2px solid #E2E8F0;">
					<span><?php echo $section['icon']; ?></span>
					<?php echo esc_html( $section['section'] ); ?>
				</h2>

				<div style="display:flex;flex-direction:column;gap:.75rem;">
					<?php foreach ( $section['items'] as $i => $item ) :
						$faq_id = 'faq-' . $idx . '-' . $i;
					?>
						<div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;overflow:hidden;">
							<button
								onclick="(function(btn){var panel=btn.nextElementSibling;var icon=btn.querySelector('.faq-icon');var open=panel.style.display!=='none';panel.style.display=open?'none':'block';icon.textContent=open?'+':'−';btn.style.background=open?'':'#F0FDFA';})(this)"
								style="width:100%;text-align:left;background:none;border:none;padding:1.1rem 1.25rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:1rem;font-size:.9rem;font-weight:700;color:#0F172A;font-family:inherit;"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $faq_id ); ?>"
							>
								<span><?php echo esc_html( $item['q'] ); ?></span>
								<span class="faq-icon" style="flex-shrink:0;width:1.5rem;height:1.5rem;background:#0D9488;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:400;line-height:1;">+</span>
							</button>
							<div id="<?php echo esc_attr( $faq_id ); ?>" style="display:none;padding:.25rem 1.25rem 1.1rem;font-size:.875rem;color:#475569;line-height:1.7;border-top:1px solid #E2E8F0;">
								<?php echo wp_kses_post( $item['a'] ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<!-- Still have questions -->
		<div style="margin-top:2rem;padding:2rem;background:linear-gradient(135deg,#0F172A,#134E4A);border-radius:16px;color:white;text-align:center;">
			<h2 style="font-size:1.25rem;font-weight:700;margin:0 0 .5rem;">Still have a question?</h2>
			<p style="color:rgba(255,255,255,.75);font-size:.9rem;margin:0 0 1.25rem;">Our secretariat team is happy to help. We aim to respond within 2 business days.</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary" style="display:inline-block;text-decoration:none;background:#0D9488;color:white;padding:.75rem 2rem;border-radius:8px;font-weight:600;">Contact Us →</a>
		</div>

	</div>
</section>

<?php get_footer(); ?>
