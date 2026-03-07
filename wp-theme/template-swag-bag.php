<?php
/**
 * Template Name: Digital Swag Bag
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<!-- Page Hero -->
<section class="page-hero" style="background:linear-gradient(135deg,#1E1B4B 0%,#4F46E5 100%);">
	<div class="container">
		<p class="page-hero__eyebrow">Watersan Dialogue 2026</p>
		<h1 class="page-hero__title">Digital Swag Bag</h1>
		<p class="page-hero__subtitle">Brochures, case studies, and resources from our sponsors and exhibitors — all in one place.</p>
	</div>
</section>

<section class="section">
	<div class="container" style="max-width:960px;">

		<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
			<div>
				<p style="color:#475569;margin:0;">Browse resources shared by sponsors. Tap or click to download.</p>
			</div>
			<div style="display:flex;gap:.75rem;">
				<input type="search" id="swag-search" class="form-control" placeholder="Search resources…" style="width:240px;">
				<select id="swag-filter" class="form-control" style="width:auto;">
					<option value="">All Sponsors</option>
				</select>
			</div>
		</div>

		<?php
		// Server-side render for non-JS users and SEO.
		$items = array();
		if ( defined( 'AWSISA_SUPABASE_URL' ) ) {
			$response = awsisa_supabase_request(
				'swag_bag_items',
				'GET',
				array(),
				'is_active=eq.true&select=id,title,description,file_url,file_type,download_count,sponsor_id,sponsors(name,logo_url,tier)&order=created_at.desc'
			);
			if ( ! is_wp_error( $response ) ) {
				$items = is_array( $response ) ? $response : array();
			}
		}

		if ( empty( $items ) ) :
			?>
			<div style="text-align:center;padding:4rem 2rem;background:#F8FAFC;border-radius:16px;">
				<div style="font-size:3rem;margin-bottom:1rem;">📦</div>
				<h3 style="color:#0F172A;margin:0 0 .5rem;">Resources Coming Soon</h3>
				<p style="color:#64748B;">Sponsor resources will be available closer to the conference. Check back in October 2026.</p>
			</div>
		<?php else : ?>
			<div id="swag-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;">
				<?php foreach ( $items as $item ) :
					$sponsor     = $item['sponsors'] ?? array();
					$file_type   = strtoupper( pathinfo( $item['file_url'] ?? '', PATHINFO_EXTENSION ) ?: $item['file_type'] ?? 'FILE' );
					$icon_map    = array( 'PDF' => '📄', 'PNG' => '🖼️', 'JPG' => '🖼️', 'JPEG' => '🖼️', 'PPTX' => '📊', 'XLSX' => '📊', 'MP4' => '🎬' );
					$file_icon   = $icon_map[ $file_type ] ?? '📎';
					$tier_colors = array( 'platinum' => '#E5E7EB', 'gold' => '#FDE68A', 'silver' => '#E2E8F0', 'bronze' => '#FED7AA' );
					$tier_color  = $tier_colors[ $sponsor['tier'] ?? '' ] ?? '#F1F5F9';
					?>
					<div class="card swag-item"
						data-sponsor-id="<?php echo esc_attr( $item['sponsor_id'] ?? '' ); ?>"
						data-sponsor-name="<?php echo esc_attr( $sponsor['name'] ?? '' ); ?>"
						data-title="<?php echo esc_attr( $item['title'] ); ?>"
						style="position:relative;transition:transform .2s;">
						<?php if ( ! empty( $sponsor['name'] ) ) : ?>
							<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;">
								<?php if ( ! empty( $sponsor['logo_url'] ) ) : ?>
									<img src="<?php echo esc_url( $sponsor['logo_url'] ); ?>" alt="<?php echo esc_attr( $sponsor['name'] ); ?>" style="height:28px;object-fit:contain;">
								<?php endif; ?>
								<span class="badge" style="background:<?php echo esc_attr( $tier_color ); ?>;color:#374151;font-size:.7rem;"><?php echo esc_html( ucfirst( $sponsor['tier'] ?? '' ) ); ?></span>
							</div>
						<?php endif; ?>

						<div style="font-size:2rem;margin-bottom:.5rem;"><?php echo $file_icon; ?></div>
						<h3 style="font-size:1rem;font-weight:700;color:#0F172A;margin:0 0 .5rem;"><?php echo esc_html( $item['title'] ); ?></h3>
						<?php if ( ! empty( $item['description'] ) ) : ?>
							<p style="font-size:.8rem;color:#64748B;margin:0 0 1rem;"><?php echo esc_html( $item['description'] ); ?></p>
						<?php endif; ?>

						<div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #F1F5F9;padding-top:.75rem;margin-top:auto;">
							<span style="font-size:.75rem;color:#94A3B8;"><?php echo esc_html( $file_type ); ?> · <?php echo esc_html( number_format( $item['download_count'] ?? 0 ) ); ?> downloads</span>
							<a href="<?php echo esc_url( $item['file_url'] ); ?>"
								target="_blank"
								rel="noopener"
								class="btn btn--primary"
								style="font-size:.8rem;padding:.5rem 1rem;"
								data-item-id="<?php echo esc_attr( $item['id'] ); ?>">
								Download
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div style="margin-top:3rem;padding:1.25rem 1.5rem;background:#FFF7ED;border:1px solid #FED7AA;border-radius:12px;">
			<p style="color:#9A3412;font-size:.875rem;margin:0;">
				🔒 <strong>Delegate exclusive:</strong> Some resources are only available to registered delegates. <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" style="color:#0D9488;">Register now</a> to unlock all content.
			</p>
		</div>

	</div>
</section>

<script>
( function () {
	const grid    = document.getElementById( 'swag-grid' );
	const search  = document.getElementById( 'swag-search' );
	const filter  = document.getElementById( 'swag-filter' );
	if ( ! grid ) return;

	const items   = Array.from( grid.querySelectorAll( '.swag-item' ) );
	const seen    = new Set();
	const selOpt  = ( name, id ) => { const o = new Option( name, id ); filter.add( o ); };

	items.forEach( el => {
		const id   = el.dataset.sponsorId;
		const name = el.dataset.sponsorName;
		if ( id && name && ! seen.has( id ) ) { seen.add( id ); selOpt( name, id ); }
	} );

	function applyFilter() {
		const q  = search.value.toLowerCase();
		const sp = filter.value;
		items.forEach( el => {
			const matchQ  = ! q || el.dataset.title.toLowerCase().includes( q ) || el.dataset.sponsorName.toLowerCase().includes( q );
			const matchSp = ! sp || el.dataset.sponsorId === sp;
			el.style.display = matchQ && matchSp ? '' : 'none';
		} );
	}

	search.addEventListener( 'input', applyFilter );
	filter.addEventListener( 'change', applyFilter );

	// Log NFC tap for downloads.
	grid.addEventListener( 'click', function ( e ) {
		const btn = e.target.closest( '[data-item-id]' );
		if ( ! btn ) return;
		navigator.sendBeacon(
			'<?php echo esc_url( rest_url( "awsisa/v1/nfc/tap" ) ); ?>',
			JSON.stringify( { tap_type: 'swag_download', reference_id: btn.dataset.itemId } )
		);
	} );
} )();
</script>

<?php get_footer(); ?>
