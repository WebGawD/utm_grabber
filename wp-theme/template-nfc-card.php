<?php
/**
 * Template Name: NFC Delegate Card
 *
 * Renders a mobile-optimised contact card for the delegate whose UUID is
 * passed as the `d` query parameter (e.g. /delegate-card/?d=UUID).
 *
 * The template fetches only publicly-safe fields from Supabase
 * (profile_public must be true) and logs an anonymous NFC tap.
 *
 * No WordPress header/footer chrome — pure card view for phone screens.
 *
 * @package Awsisa_Watersan_2026
 */

// Disable theme header / footer chrome — output document manually.
$delegate_id = isset( $_GET['d'] ) ? sanitize_text_field( wp_unslash( $_GET['d'] ) ) : '';

// Validate UUID format (allow alphanumeric + hyphens, 10–40 chars).
if ( ! preg_match( '/^[a-zA-Z0-9\-]{10,40}$/', $delegate_id ) ) {
	$delegate_id = '';
}

$delegate = null;
$error    = '';

if ( $delegate_id && defined( 'AWSISA_SUPABASE_URL' ) && AWSISA_SUPABASE_URL ) {

	// Log the tap (fire-and-forget, best effort).
	$tap_url  = rtrim( AWSISA_SUPABASE_URL, '/' ) . '/rest/v1/nfc_taps';
	$tap_data = array(
		'tap_type'    => 'delegate_view',
		'delegate_id' => $delegate_id,
	);
	wp_remote_post( $tap_url, array(
		'headers' => array(
			'apikey'        => AWSISA_SUPABASE_ANON_KEY,
			'Authorization' => 'Bearer ' . AWSISA_SUPABASE_ANON_KEY,
			'Content-Type'  => 'application/json',
		),
		'body'    => wp_json_encode( $tap_data ),
		'timeout' => 3,
		'blocking' => false,
	) );

	// Fetch public-safe fields only (direct wp_remote_get — finer query control).
	$url  = rtrim( AWSISA_SUPABASE_URL, '/' )
		. '/rest/v1/delegates'
		. '?id=eq.' . rawurlencode( $delegate_id )
		. '&profile_public=eq.true'
		. '&select=first_name,last_name,organisation,job_title,country,delegate_type'
		. '&limit=1';

	$resp = wp_remote_get( $url, array(
		'headers' => array(
			'apikey'        => AWSISA_SUPABASE_ANON_KEY,
			'Authorization' => 'Bearer ' . AWSISA_SUPABASE_ANON_KEY,
			'Accept'        => 'application/json',
		),
		'timeout' => 8,
	) );

	if ( is_wp_error( $resp ) ) {
		$error = esc_html__( 'Unable to reach server. Please try again.', 'awsisa' );
	} else {
		$code    = wp_remote_retrieve_response_code( $resp );
		$decoded = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( $code === 200 && ! empty( $decoded ) ) {
			$delegate = $decoded[0];
		} else {
			$error = esc_html__( 'This delegate profile is not publicly available.', 'awsisa' );
		}
	}
} elseif ( $delegate_id && ! defined( 'AWSISA_SUPABASE_URL' ) ) {
	$error = esc_html__( 'Conference database not yet configured.', 'awsisa' );
} elseif ( ! $delegate_id ) {
	$error = esc_html__( 'No delegate ID provided. Scan a valid NFC badge QR code.', 'awsisa' );
}

// Delegate type display labels.
$type_labels = array(
	'government'     => 'Government',
	'utility'        => 'Utility',
	'private_sector' => 'Private Sector',
	'ngo'            => 'NGO / NPO',
	'academic'       => 'Academic',
	'media'          => 'Media',
	'exhibitor'      => 'Exhibitor',
	'sponsor'        => 'Sponsor',
);

// Compute initials from delegate name.
$initials = '??';
if ( $delegate ) {
	$parts    = array_filter( array( $delegate['first_name'] ?? '', $delegate['last_name'] ?? '' ) );
	$initials = implode( '', array_map( fn( $p ) => mb_strtoupper( mb_substr( $p, 0, 1 ) ), $parts ) );
}

// Card accent colour per delegate type.
$type_colours = array(
	'government'     => array( 'bg' => '#0F766E', 'light' => '#CCFBF1', 'text' => '#F0FDFA' ),
	'utility'        => array( 'bg' => '#0369A1', 'light' => '#E0F2FE', 'text' => '#F0F9FF' ),
	'private_sector' => array( 'bg' => '#7E22CE', 'light' => '#F3E8FF', 'text' => '#FAF5FF' ),
	'ngo'            => array( 'bg' => '#15803D', 'light' => '#DCFCE7', 'text' => '#F0FDF4' ),
	'academic'       => array( 'bg' => '#B45309', 'light' => '#FEF3C7', 'text' => '#FFFBEB' ),
	'media'          => array( 'bg' => '#BE123C', 'light' => '#FFE4E6', 'text' => '#FFF1F2' ),
	'exhibitor'      => array( 'bg' => '#0F172A', 'light' => '#E2E8F0', 'text' => '#F8FAFC' ),
	'sponsor'        => array( 'bg' => '#B45309', 'light' => '#FEF3C7', 'text' => '#FFFBEB' ),
);
$dtype  = $delegate['delegate_type'] ?? 'government';
$colour = $type_colours[ $dtype ] ?? $type_colours['government'];

// vCard data (for JS).
$vcard_json = $delegate ? wp_json_encode( array(
	'first_name'   => $delegate['first_name']   ?? '',
	'last_name'    => $delegate['last_name']     ?? '',
	'organisation' => $delegate['organisation']  ?? '',
	'job_title'    => $delegate['job_title']     ?? '',
	'country'      => $delegate['country']       ?? '',
) ) : '{}';

// Canonical URL for this card.
$card_url = home_url( '/delegate-card/?d=' . rawurlencode( $delegate_id ) );

// Page title.
$page_title = $delegate
	? esc_html( ( $delegate['first_name'] ?? '' ) . ' ' . ( $delegate['last_name'] ?? '' ) . ' — AWSISA 2026' )
	: esc_html__( 'Delegate Card — AWSISA Watersan 2026', 'awsisa' );
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $page_title; // Already escaped above ?></title>
	<meta name="robots" content="noindex,nofollow">
	<?php if ( $delegate ) : ?>
		<meta property="og:title" content="<?php echo esc_attr( ( $delegate['first_name'] ?? '' ) . ' ' . ( $delegate['last_name'] ?? '' ) ); ?>">
		<meta property="og:description" content="<?php echo esc_attr( implode( ', ', array_filter( array( $delegate['job_title'] ?? '', $delegate['organisation'] ?? '' ) ) ) ); ?> · AWSISA Watersan 2026">
		<meta property="og:type" content="profile">
	<?php endif; ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap">
	<style>
		*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
		html { height: 100%; }
		body {
			font-family: 'Inter', system-ui, sans-serif;
			background: <?php echo esc_attr( $colour['light'] ); ?>;
			min-height: 100%;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 1.5rem 1rem 3rem;
			-webkit-font-smoothing: antialiased;
		}

		/* Card ---------------------------------------------------------------- */
		.nfc-card {
			background: #fff;
			border-radius: 1.5rem;
			width: 100%;
			max-width: 380px;
			box-shadow: 0 20px 48px -8px rgba(0,0,0,.18), 0 0 0 1px rgba(0,0,0,.04);
			overflow: hidden;
		}
		.nfc-card__header {
			background: <?php echo esc_attr( $colour['bg'] ); ?>;
			padding: 2.25rem 1.5rem 3.5rem;
			text-align: center;
			position: relative;
		}
		.nfc-card__avatar {
			width: 88px;
			height: 88px;
			border-radius: 50%;
			background: rgba(255,255,255,.15);
			border: 3px solid rgba(255,255,255,.35);
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 1.25rem;
			font-family: 'Outfit', sans-serif;
			font-size: 2rem;
			font-weight: 800;
			color: #fff;
			letter-spacing: -.02em;
		}
		.nfc-card__name {
			font-family: 'Outfit', sans-serif;
			font-size: 1.4rem;
			font-weight: 800;
			color: #fff;
			line-height: 1.2;
		}
		.nfc-card__role {
			font-size: .825rem;
			color: rgba(255,255,255,.8);
			margin-top: .35rem;
			font-weight: 500;
		}
		.nfc-card__badge {
			display: inline-block;
			background: rgba(255,255,255,.2);
			color: #fff;
			font-size: .65rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .08em;
			padding: .2rem .6rem;
			border-radius: 99px;
			margin-top: .75rem;
		}
		.nfc-card__wave {
			position: absolute;
			bottom: 0; left: 0; right: 0;
			height: 30px;
			background: #fff;
			clip-path: ellipse(55% 100% at 50% 100%);
		}

		/* Body ---------------------------------------------------------------- */
		.nfc-card__body {
			padding: 1.5rem 1.5rem 1.75rem;
		}
		.nfc-card__detail {
			display: flex;
			align-items: flex-start;
			gap: .75rem;
			padding: .625rem 0;
			border-bottom: 1px solid #F1F5F9;
		}
		.nfc-card__detail:last-child { border-bottom: none; }
		.nfc-card__detail-icon {
			width: 32px;
			height: 32px;
			border-radius: 8px;
			background: <?php echo esc_attr( $colour['light'] ); ?>;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
			margin-top: .05rem;
		}
		.nfc-card__detail-label {
			font-size: .7rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: .06em;
			color: #94A3B8;
			line-height: 1;
			margin-bottom: .2rem;
		}
		.nfc-card__detail-value {
			font-size: .9rem;
			font-weight: 600;
			color: #0F172A;
			line-height: 1.3;
		}

		/* Actions ------------------------------------------------------------- */
		.nfc-card__actions {
			padding: 0 1.5rem 1.75rem;
			display: flex;
			flex-direction: column;
			gap: .625rem;
		}
		.btn-card {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: .5rem;
			padding: .75rem 1.25rem;
			border-radius: .75rem;
			font-weight: 700;
			font-size: .875rem;
			cursor: pointer;
			border: none;
			width: 100%;
			transition: opacity .15s, transform .1s;
			text-decoration: none;
		}
		.btn-card:active { transform: scale(.97); }
		.btn-card--primary {
			background: <?php echo esc_attr( $colour['bg'] ); ?>;
			color: #fff;
		}
		.btn-card--outline {
			background: <?php echo esc_attr( $colour['light'] ); ?>;
			color: <?php echo esc_attr( $colour['bg'] ); ?>;
		}

		/* Event branding strip ------------------------------------------------ */
		.nfc-card__footer {
			padding: 1rem 1.5rem;
			border-top: 1px solid #F1F5F9;
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 1rem;
		}
		.nfc-card__event-name {
			font-size: .7rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .07em;
			color: #94A3B8;
			line-height: 1.4;
		}
		.nfc-card__event-logo {
			width: 32px;
			height: 32px;
			border-radius: 8px;
			background: <?php echo esc_attr( $colour['bg'] ); ?>;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
		}

		/* Error state --------------------------------------------------------- */
		.nfc-error {
			background: #fff;
			border-radius: 1.5rem;
			width: 100%;
			max-width: 380px;
			padding: 3rem 2rem;
			text-align: center;
			box-shadow: 0 20px 48px -8px rgba(0,0,0,.18);
		}
		.nfc-error__icon { font-size: 3rem; margin-bottom: 1rem; }
		.nfc-error__title { font-family: 'Outfit', sans-serif; font-size: 1.3rem; font-weight: 800; color: #0F172A; margin-bottom: .75rem; }
		.nfc-error__msg { color: #64748B; font-size: .9rem; line-height: 1.6; margin-bottom: 2rem; }

		/* Toast --------------------------------------------------------------- */
		#nfc-toast {
			position: fixed;
			bottom: 1.5rem;
			left: 50%;
			transform: translateX(-50%) translateY(4rem);
			background: #0F172A;
			color: #F1F5F9;
			padding: .65rem 1.25rem;
			border-radius: .75rem;
			font-size: .85rem;
			font-weight: 600;
			white-space: nowrap;
			transition: transform .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
			opacity: 0;
			pointer-events: none;
			z-index: 100;
		}
		#nfc-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
	</style>
</head>
<body>

<?php if ( $delegate ) : ?>

	<div class="nfc-card" role="main" aria-label="<?php echo esc_attr( ( $delegate['first_name'] ?? '' ) . ' ' . ( $delegate['last_name'] ?? '' ) ); ?>'s delegate card">

		<!-- Header with gradient + initials avatar -->
		<div class="nfc-card__header">
			<div class="nfc-card__avatar" aria-hidden="true">
				<?php echo esc_html( $initials ); ?>
			</div>
			<h1 class="nfc-card__name">
				<?php echo esc_html( ( $delegate['first_name'] ?? '' ) . ' ' . ( $delegate['last_name'] ?? '' ) ); ?>
			</h1>
			<?php if ( ! empty( $delegate['job_title'] ) ) : ?>
				<p class="nfc-card__role"><?php echo esc_html( $delegate['job_title'] ); ?></p>
			<?php endif; ?>
			<span class="nfc-card__badge">
				<?php echo esc_html( $type_labels[ $dtype ] ?? ucfirst( $dtype ) ); ?> · AWSISA 2026
			</span>
			<div class="nfc-card__wave" aria-hidden="true"></div>
		</div>

		<!-- Detail rows -->
		<div class="nfc-card__body">

			<?php if ( ! empty( $delegate['organisation'] ) ) : ?>
				<div class="nfc-card__detail">
					<div class="nfc-card__detail-icon" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $colour['bg'] ); ?>" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
					</div>
					<div>
						<div class="nfc-card__detail-label"><?php esc_html_e( 'Organisation', 'awsisa' ); ?></div>
						<div class="nfc-card__detail-value"><?php echo esc_html( $delegate['organisation'] ); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $delegate['country'] ) ) : ?>
				<div class="nfc-card__detail">
					<div class="nfc-card__detail-icon" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $colour['bg'] ); ?>" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
					</div>
					<div>
						<div class="nfc-card__detail-label"><?php esc_html_e( 'Country', 'awsisa' ); ?></div>
						<div class="nfc-card__detail-value"><?php echo esc_html( $delegate['country'] ); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<div class="nfc-card__detail">
				<div class="nfc-card__detail-icon" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $colour['bg'] ); ?>" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
				</div>
				<div>
					<div class="nfc-card__detail-label"><?php esc_html_e( 'Event', 'awsisa' ); ?></div>
					<div class="nfc-card__detail-value">AWSISA Africa &amp; Global South<br>Water &amp; Sanitation Dialogue 2026</div>
				</div>
			</div>

		</div><!-- .nfc-card__body -->

		<!-- Action buttons -->
		<div class="nfc-card__actions">
			<button class="btn-card btn-card--primary" id="btn-save-contact">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
				<?php esc_html_e( 'Save Contact', 'awsisa' ); ?>
			</button>
			<button class="btn-card btn-card--outline" id="btn-share">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
				<?php esc_html_e( 'Share Card', 'awsisa' ); ?>
			</button>
		</div>

		<!-- Event branding footer -->
		<div class="nfc-card__footer">
			<div class="nfc-card__event-name">
				Watersan Dialogue 2026<br>
				Emperors Palace · Johannesburg
			</div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nfc-card__event-logo" aria-label="AWSISA homepage">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" aria-hidden="true"><path d="M12 2C8 2 4 8 4 14s4 8 8 8 8-2 8-8S16 2 12 2z"/><path d="M12 8v8M8 12h8"/></svg>
			</a>
		</div>

	</div><!-- .nfc-card -->

	<!-- Toast notification -->
	<div id="nfc-toast" role="status" aria-live="polite"></div>

	<script>
	(function () {
		'use strict';

		var vData = <?php echo $vcard_json; // Already json_encoded — safe to output raw ?>;
		var cardUrl = <?php echo wp_json_encode( $card_url ); ?>;
		var toast   = document.getElementById('nfc-toast');

		function showToast(msg) {
			toast.textContent = msg;
			toast.classList.add('show');
			setTimeout(function () { toast.classList.remove('show'); }, 2500);
		}

		// Save Contact → download .vcf
		document.getElementById('btn-save-contact').addEventListener('click', function () {
			var lines = [
				'BEGIN:VCARD',
				'VERSION:3.0',
				'FN:' + (vData.first_name + ' ' + vData.last_name).trim(),
				'N:' + vData.last_name + ';' + vData.first_name + ';;;',
			];
			if (vData.organisation) lines.push('ORG:' + vData.organisation);
			if (vData.job_title)    lines.push('TITLE:' + vData.job_title);
			if (vData.country)      lines.push('ADR:;;;;;;' + vData.country);
			lines.push('NOTE:AWSISA Africa & Global South Water & Sanitation Dialogue 2026');
			lines.push('END:VCARD');

			var blob = new Blob([lines.join('\r\n')], { type: 'text/vcard;charset=utf-8' });
			var a    = document.createElement('a');
			a.href     = URL.createObjectURL(blob);
			a.download = (vData.first_name + '_' + vData.last_name).replace(/\s+/g, '_') + '.vcf';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(a.href);
			showToast('✓ Contact saved');
		});

		// Share → Web Share API with URL copy fallback
		document.getElementById('btn-share').addEventListener('click', function () {
			var shareData = {
				title: (vData.first_name + ' ' + vData.last_name).trim() + ' — AWSISA 2026',
				text:  'Connect with ' + (vData.first_name || 'a delegate') + ' at the AWSISA Watersan Dialogue 2026.',
				url:   cardUrl,
			};

			if (navigator.share) {
				navigator.share(shareData).catch(function () {});
			} else {
				// Fallback: copy URL to clipboard.
				if (navigator.clipboard) {
					navigator.clipboard.writeText(cardUrl).then(function () {
						showToast('🔗 Link copied to clipboard');
					});
				} else {
					showToast('Card URL: ' + cardUrl);
				}
			}
		});
	}());
	</script>

<?php else : ?>

	<!-- Error state -->
	<div class="nfc-error" role="main">
		<div class="nfc-error__icon">💧</div>
		<h1 class="nfc-error__title"><?php esc_html_e( 'Card Not Found', 'awsisa' ); ?></h1>
		<p class="nfc-error__msg"><?php echo $error; // Already escaped above ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
		   style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:#0D9488;color:#fff;border-radius:.75rem;font-weight:700;font-size:.875rem;text-decoration:none;">
			← <?php esc_html_e( 'Back to AWSISA 2026', 'awsisa' ); ?>
		</a>
	</div>

<?php endif; ?>

</body>
</html>
