<?php
/**
 * The default page template.
 *
 * @package Awsisa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php the_excerpt(); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container container--narrow">
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	</div>
</section>

<?php get_footer(); ?>
