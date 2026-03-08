<?php
/**
 * Email notifications for Awsisa Events.
 *
 * Sends transactional emails via wp_mail().
 * In production, configure an SMTP plugin (e.g. WP Mail SMTP) with
 * SendGrid, Resend, or Amazon SES for reliable delivery.
 *
 * @package Awsisa_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends the delegate registration confirmation email with their QR code.
 *
 * @param array $delegate Delegate row from Supabase.
 * @return bool Whether the email was sent.
 */
function awsisa_send_registration_email( $delegate ) {
	if ( empty( $delegate['email'] ) ) {
		return false;
	}

	$to      = $delegate['email'];
	$name    = $delegate['first_name'] . ' ' . $delegate['last_name'];
	$subject = sprintf(
		/* translators: %s: attendee name */
		__( 'Your AWSISA Watersan 2026 Registration — %s', 'awsisa-events' ),
		$name
	);

	$qr_token   = $delegate['qr_code_token'] ?? '';
	$qr_url     = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . rawurlencode( $qr_token );
	$event_date = '9 – 12 November 2026';
	$venue      = 'Inkosi Albert Luthuli ICC, 45 Bram Fischer Road, Durban';

	$message = awsisa_email_registration_html( array(
		'name'       => $name,
		'first_name' => $delegate['first_name'],
		'email'      => $to,
		'org'        => $delegate['organisation'] ?? '',
		'type'       => $delegate['delegate_type'] ?? '',
		'qr_token'   => $qr_token,
		'qr_url'     => $qr_url,
		'event_date' => $event_date,
		'venue'      => $venue,
	) );

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: AWSISA Events <events@awsisa-watersan-dialogue.org>',
		'Bcc: info@lubabalo.co.za',
	);

	return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Builds the HTML for the registration confirmation email.
 *
 * @param array $data Template variables.
 * @return string HTML email body.
 */
function awsisa_email_registration_html( $data ) {
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
	$type_label = $type_labels[ $data['type'] ] ?? ucfirst( $data['type'] );

	ob_start();
	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php esc_html_e( 'AWSISA Registration Confirmation', 'awsisa-events' ); ?></title>
</head>
<body style="margin:0;padding:0;background:#F8FAFC;font-family:'Inter',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#115E59,#0D9488);padding:40px 40px 32px;text-align:center;">
          <div style="font-family:'Outfit',Arial,sans-serif;font-size:24px;font-weight:800;color:#ffffff;margin-bottom:4px;">AWSISA Africa</div>
          <div style="color:#5EEAD4;font-size:14px;font-weight:600;">Water & Sanitation Dialogue 2026</div>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:40px;">
          <h1 style="font-family:'Outfit',Arial,sans-serif;font-size:28px;color:#0F172A;margin:0 0 8px;">You're Registered! 🎉</h1>
          <p style="color:#475569;font-size:16px;margin:0 0 32px;">Dear <?php echo esc_html( $data['first_name'] ); ?>, your registration for AWSISA Watersan Dialogue 2026 is confirmed.</p>

          <!-- Event details -->
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;margin-bottom:32px;">
            <tr>
              <td style="padding:24px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">📅 Date:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $data['event_date'] ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">📍 Venue:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $data['venue'] ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">👤 Name:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $data['name'] ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">🏢 Organisation:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $data['org'] ?: '—' ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">🎫 Type:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $type_label ); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- QR Code -->
          <div style="text-align:center;margin-bottom:32px;">
            <h2 style="font-family:'Outfit',Arial,sans-serif;font-size:20px;color:#0F172A;margin-bottom:12px;">Your Conference QR Code</h2>
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">Present this QR code at registration to check in instantly and receive your badge.</p>
            <img src="<?php echo esc_url( $data['qr_url'] ); ?>" alt="Your QR code" width="200" height="200" style="border:8px solid #F0FDFA;border-radius:12px;">
            <p style="font-size:12px;color:#94A3B8;margin-top:12px;font-family:monospace;"><?php echo esc_html( substr( $data['qr_token'], 0, 16 ) . '…' ); ?></p>
          </div>

          <!-- What to bring -->
          <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;padding:20px;margin-bottom:32px;">
            <h3 style="font-family:'Outfit',Arial,sans-serif;color:#92400E;font-size:16px;margin:0 0 12px;">📋 What to Bring</h3>
            <ul style="color:#78350F;font-size:14px;margin:0;padding-left:20px;line-height:1.8;">
              <li>This QR code (digital or printed)</li>
              <li>Government-issued photo ID</li>
              <li>Proof of payment (if applicable)</li>
            </ul>
          </div>

          <!-- CTA -->
          <div style="text-align:center;margin-bottom:32px;">
            <a href="<?php echo esc_url( home_url( '/accommodation/' ) ); ?>" style="display:inline-block;background:#0D9488;color:#ffffff;font-weight:700;font-size:15px;padding:14px 32px;border-radius:8px;text-decoration:none;">Book Accommodation</a>
          </div>

          <p style="color:#475569;font-size:14px;">Questions? Email us at <a href="mailto:info@lubabalo.co.za" style="color:#0D9488;">info@lubabalo.co.za</a></p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#0F172A;padding:24px 40px;text-align:center;">
          <p style="color:#64748B;font-size:12px;margin:0;">#AWSISA2026 | #Afriwatersan26 | www.awsisa-watersan-dialogue.org</p>
          <p style="color:#475569;font-size:11px;margin:8px 0 0;">Your personal information is processed in accordance with South Africa's Protection of Personal Information Act (POPIA).</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
	<?php
	return ob_get_clean();
}

/**
 * Sends an accommodation booking confirmation email to the delegate.
 *
 * @param array $delegate  Delegate row: first_name, last_name, email.
 * @param array $pkg       Accommodation package row: hotel_name, price_zar, price_usd, description.
 * @param array $booking   Accommodation booking row: id.
 * @param array $body      Raw request body: check_in_date, check_out_date, special_requests.
 * @return bool Whether the email was sent.
 */
function awsisa_send_accomm_booking_email( $delegate, $pkg, $booking, $body ) {
	if ( empty( $delegate['email'] ) ) {
		return false;
	}

	$to         = $delegate['email'];
	$first_name = $delegate['first_name'] ?? '';
	$last_name  = $delegate['last_name']  ?? '';
	$name       = trim( $first_name . ' ' . $last_name );

	$subject = sprintf(
		/* translators: %s: hotel name */
		__( 'Accommodation Booking Confirmed — %s | Watersan Dialogue 2026', 'awsisa-events' ),
		$pkg['hotel_name'] ?? 'Your Hotel'
	);

	$message = awsisa_email_accomm_html( array(
		'first_name'    => $first_name,
		'name'          => $name ?: $to,
		'email'         => $to,
		'hotel_name'    => $pkg['hotel_name']   ?? '',
		'description'   => $pkg['description']  ?? '',
		'price_zar'     => $pkg['price_zar']    ?? 0,
		'price_usd'     => $pkg['price_usd']    ?? 0,
		'check_in'      => $body['check_in_date']  ?? '',
		'check_out'     => $body['check_out_date'] ?? '',
		'booking_ref'   => $booking['id']          ?? '',
		'special_reqs'  => $body['special_requests'] ?? $body['notes'] ?? '',
	) );

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: AWSISA Events <events@awsisa-watersan-dialogue.org>',
		'Bcc: info@lubabalo.co.za',
	);

	return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Builds the HTML for the accommodation booking confirmation email.
 *
 * @param array $d Template variables.
 * @return string HTML email body.
 */
function awsisa_email_accomm_html( $d ) {
	$nights = 0;
	if ( ! empty( $d['check_in'] ) && ! empty( $d['check_out'] ) ) {
		$ci     = new DateTime( $d['check_in'] );
		$co     = new DateTime( $d['check_out'] );
		$nights = max( 1, (int) $ci->diff( $co )->days );
	}
	$total_zar = (int) $d['price_zar'] * $nights;
	$total_usd = (float) $d['price_usd'] * $nights;

	ob_start();
	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accommodation Booking Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#F8FAFC;font-family:'Inter',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#115E59,#0D9488);padding:40px 40px 32px;text-align:center;">
          <div style="font-family:'Outfit',Arial,sans-serif;font-size:24px;font-weight:800;color:#ffffff;margin-bottom:4px;">AWSISA Africa</div>
          <div style="color:#5EEAD4;font-size:14px;font-weight:600;">Water & Sanitation Dialogue 2026</div>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:40px;">
          <h1 style="font-family:'Outfit',Arial,sans-serif;font-size:28px;color:#0F172A;margin:0 0 8px;">Accommodation Confirmed! 🏨</h1>
          <p style="color:#475569;font-size:16px;margin:0 0 28px;">Dear <?php echo esc_html( $d['first_name'] ?: $d['name'] ); ?>, your room at <strong><?php echo esc_html( $d['hotel_name'] ); ?></strong> has been successfully reserved.</p>

          <!-- Booking summary -->
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#F0FDFA;border:1px solid #99F6E4;border-radius:12px;margin-bottom:28px;">
            <tr>
              <td style="padding:24px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="padding:6px 0;width:40%;"><strong style="color:#0F172A;">🏨 Hotel:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $d['hotel_name'] ); ?></td>
                  </tr>
                  <?php if ( $d['description'] ) : ?>
                  <tr>
                    <td style="padding:6px 0;vertical-align:top;"><strong style="color:#0F172A;">ℹ️ About:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $d['description'] ); ?></td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">📅 Check-in:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $d['check_in'] ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">📅 Check-out:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $d['check_out'] ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">🌙 Nights:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $nights ); ?></td>
                  </tr>
                  <tr>
                    <td style="padding:6px 0;"><strong style="color:#0F172A;">💰 Total:</strong></td>
                    <td style="padding:6px 0;">
                      <span style="font-weight:800;color:#0D9488;font-size:1.1em;">R <?php echo esc_html( number_format( $total_zar ) ); ?></span>
                      <?php if ( $total_usd > 0 ) : ?>
                        <span style="color:#94A3B8;font-size:.85em;"> (~$<?php echo esc_html( number_format( $total_usd, 0 ) ); ?> USD)</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php if ( ! empty( $d['special_reqs'] ) ) : ?>
                  <tr>
                    <td style="padding:6px 0;vertical-align:top;"><strong style="color:#0F172A;">📝 Requests:</strong></td>
                    <td style="color:#475569;padding:6px 0;"><?php echo esc_html( $d['special_reqs'] ); ?></td>
                  </tr>
                  <?php endif; ?>
                </table>
              </td>
            </tr>
          </table>

          <!-- Booking reference -->
          <?php if ( ! empty( $d['booking_ref'] ) ) : ?>
          <div style="text-align:center;margin-bottom:28px;">
            <div style="display:inline-block;background:#F0FDFA;border:2px solid #99F6E4;border-radius:12px;padding:16px 28px;">
              <div style="font-size:12px;color:#94A3B8;margin-bottom:4px;letter-spacing:.05em;text-transform:uppercase;">Booking Reference</div>
              <div style="font-weight:800;font-size:1.25rem;color:#0D9488;letter-spacing:.08em;font-family:monospace;"><?php echo esc_html( strtoupper( substr( $d['booking_ref'], 0, 8 ) ) ); ?></div>
              <div style="font-size:11px;color:#CBD5E1;margin-top:4px;"><?php echo esc_html( $d['booking_ref'] ); ?></div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Payment notice -->
          <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;padding:20px;margin-bottom:28px;">
            <h3 style="font-family:'Outfit',Arial,sans-serif;color:#92400E;font-size:15px;margin:0 0 8px;">💳 Payment at Check-in</h3>
            <p style="color:#78350F;font-size:14px;margin:0;line-height:1.7;">No payment is required today. Your rate is guaranteed — please settle directly with the hotel at check-in. Present this email or your booking reference.</p>
          </div>

          <!-- What to know -->
          <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:20px;margin-bottom:28px;">
            <h3 style="font-family:'Outfit',Arial,sans-serif;color:#1D4ED8;font-size:15px;margin:0 0 10px;">📌 Good to Know</h3>
            <ul style="color:#1E40AF;font-size:14px;margin:0;padding-left:20px;line-height:1.9;">
              <li>Cancellations accepted up to <strong>31 October 2026</strong></li>
              <li>All partner hotels are within walking distance of the ICC Durban</li>
              <li>For group bookings (10+ rooms) contact <a href="mailto:info@lubabalo.co.za" style="color:#0D9488;">info@lubabalo.co.za</a></li>
              <li>Conference venue: Inkosi Albert Luthuli ICC, 45 Bram Fischer Road, Durban</li>
            </ul>
          </div>

          <p style="color:#475569;font-size:14px;">Questions? Email us at <a href="mailto:info@lubabalo.co.za" style="color:#0D9488;">info@lubabalo.co.za</a></p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#0F172A;padding:24px 40px;text-align:center;">
          <p style="color:#64748B;font-size:12px;margin:0;">#AWSISA2026 | #Afriwatersan26 | www.awsisa-watersan-dialogue.org</p>
          <p style="color:#475569;font-size:11px;margin:8px 0 0;">Your personal information is processed in accordance with South Africa's Protection of Personal Information Act (POPIA).</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
	<?php
	return ob_get_clean();
}
