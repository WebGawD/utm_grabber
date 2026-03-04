<?php
/**
 * 404 — Page Not Found
 *
 * @package Awsisa_Watersan_2026
 */

get_header();
?>

<section style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:4rem 1rem;">
	<div style="text-align:center;max-width:560px;">
		<div style="font-size:6rem;line-height:1;margin-bottom:1rem;">💧</div>
		<h1 style="font-size:2.5rem;font-weight:800;color:#0F172A;margin:0 0 .75rem;">Page Not Found</h1>
		<p style="font-size:1.1rem;color:#64748B;margin:0 0 2rem;">
			This page must have evaporated. Let's get you back to the conference.
		</p>
		<div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">Back to Home</a>
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="btn btn--outline">Register</a>
			<a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>" class="btn btn--outline">Programme</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
