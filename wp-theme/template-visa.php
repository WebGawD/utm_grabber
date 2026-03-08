<?php
/**
 * Template Name: Visa Information
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#0F172A 0%,#0D9488 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Delegates</p>
		<h1 class="page-hero__title">Visa Information</h1>
		<p class="page-hero__subtitle">Everything you need to know about entering South Africa for the Watersan Dialogue 2026.</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:960px;">

		<!-- Intro notice -->
		<div style="background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;padding:1.5rem 2rem;margin-bottom:2.5rem;display:flex;gap:1rem;align-items:flex-start;">
			<div style="font-size:1.75rem;flex-shrink:0;">🛂</div>
			<div>
				<h2 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0 0 .4rem;">Do you need a visa for South Africa?</h2>
				<p style="font-size:.9rem;color:#475569;margin:0;">Citizens of many countries can visit South Africa visa-free for up to 30–90 days. Please verify your specific requirements via the official South African Department of Home Affairs website or your nearest South African embassy/consulate.</p>
			</div>
		</div>

		<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

			<!-- Left column -->
			<div>
				<h2 style="font-size:1.4rem;font-weight:700;color:#0F172A;margin:0 0 1.25rem;">Visa-Free Countries</h2>
				<p style="font-size:.9rem;color:#475569;margin:0 0 1rem;">South Africa has bilateral visa exemption agreements with most African Union countries, EU member states, the United Kingdom, United States, Canada, Australia and many others.</p>
				<p style="font-size:.9rem;color:#475569;margin:0 0 1.5rem;">Visitors from exempt countries typically receive a visitor's visa on arrival valid for 30 days, extendable to 90 days.</p>

				<h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0 0 1rem;">Countries Requiring a Visa</h3>
				<p style="font-size:.9rem;color:#475569;margin:0 0 1rem;">If your country does not have a visa-exemption agreement, you must apply for a South African visa before travelling. The standard visitor visa is required for conference attendance.</p>

				<div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:10px;padding:1.25rem;margin-top:1.5rem;">
					<h3 style="font-size:1rem;font-weight:700;color:#92400E;margin:0 0 .5rem;">⏱ Apply Early</h3>
					<p style="font-size:.875rem;color:#78350F;margin:0;">Visa processing can take 4–8 weeks. We recommend applying at least <strong>8 weeks before</strong> the conference dates. Do not book non-refundable travel until your visa is approved.</p>
				</div>
			</div>

			<!-- Right column -->
			<div>
				<h2 style="font-size:1.4rem;font-weight:700;color:#0F172A;margin:0 0 1.25rem;">Invitation Letters</h2>
				<p style="font-size:.9rem;color:#475569;margin:0 0 1rem;">Registered delegates may request an official invitation letter from the Watersan Dialogue 2026 secretariat. This letter supports your visa application and confirms your participation in the conference.</p>

				<div style="background:#F0FDFA;border:1px solid #99F6E4;border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;">
					<h3 style="font-size:1rem;font-weight:700;color:#134E4A;margin:0 0 .75rem;">To request an invitation letter:</h3>
					<ol style="font-size:.875rem;color:#1E293B;margin:0;padding-left:1.25rem;line-height:1.8;">
						<li>Complete your delegate registration</li>
						<li>Email <a href="mailto:conference@afriwater-san.africa" style="color:#0D9488;">conference@afriwater-san.africa</a></li>
						<li>Include your full name, passport number, nationality and registration reference</li>
						<li>Allow 3–5 business days for processing</li>
					</ol>
				</div>

				<h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin:0 0 1rem;">Supporting Documents</h3>
				<ul style="font-size:.875rem;color:#475569;margin:0;padding-left:1.25rem;line-height:2;">
					<li>Valid passport (min. 6 months beyond departure)</li>
					<li>Completed visa application form (BI-84)</li>
					<li>2 passport-size photographs</li>
					<li>Proof of accommodation (hotel booking)</li>
					<li>Return flight booking</li>
					<li>Proof of sufficient funds (bank statement)</li>
					<li>Conference invitation / registration confirmation</li>
					<li>Yellow fever certificate (if applicable)</li>
				</ul>
			</div>
		</div>

		<!-- Official links -->
		<div style="margin-top:3rem;padding-top:2rem;border-top:1px solid #E2E8F0;">
			<h2 style="font-size:1.3rem;font-weight:700;color:#0F172A;margin:0 0 1.25rem;">Official Resources</h2>
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
				<?php
				$resources = array(
					array(
						'icon'  => '🏛️',
						'title' => 'Dept of Home Affairs',
						'desc'  => 'Visa requirements &amp; application forms',
						'url'   => 'https://www.dha.gov.za/index.php/immigration-services/types-of-visas',
						'label' => 'dha.gov.za',
					),
					array(
						'icon'  => '✈️',
						'title' => 'DIRCO — SA Missions Abroad',
						'desc'  => 'Find your nearest South African embassy',
						'url'   => 'https://www.dirco.gov.za/foreign-missions/',
						'label' => 'dirco.gov.za',
					),
					array(
						'icon'  => '📋',
						'title' => 'VFS Global — SA Visa',
						'desc'  => 'Online visa application centres',
						'url'   => 'https://www.vfsglobal.com/southafrica/',
						'label' => 'vfsglobal.com',
					),
					array(
						'icon'  => '🌍',
						'title' => 'eVisa Portal',
						'desc'  => 'Apply for a South African eVisa online',
						'url'   => 'https://www.evisa.gov.za/',
						'label' => 'evisa.gov.za',
					),
				);
				foreach ( $resources as $r ) : ?>
					<a href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener noreferrer" style="display:block;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;padding:1.25rem;text-decoration:none;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(13,148,136,.15)'" onmouseout="this.style.boxShadow='none'">
						<div style="font-size:1.5rem;margin-bottom:.5rem;"><?php echo $r['icon']; ?></div>
						<div style="font-weight:700;color:#0F172A;font-size:.9rem;margin-bottom:.25rem;"><?php echo esc_html( $r['title'] ); ?></div>
						<div style="font-size:.8rem;color:#64748B;margin-bottom:.5rem;"><?php echo $r['desc']; ?></div>
						<div style="font-size:.75rem;color:#0D9488;font-weight:600;"><?php echo esc_html( $r['label'] ); ?> →</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Contact -->
		<div style="margin-top:2rem;padding:1.5rem;background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;text-align:center;">
			<p style="margin:0;font-size:.9rem;color:#0F172A;">Need help with your visa application? Contact our secretariat at <a href="mailto:conference@afriwater-san.africa" style="color:#0D9488;font-weight:600;">conference@afriwater-san.africa</a></p>
		</div>

	</div>
</section>

<?php get_footer(); ?>
