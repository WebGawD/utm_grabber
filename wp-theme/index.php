<?php
/**
 * The main template file (fallback for all content types).
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
		<?php the_archive_title( '<h1 class="page-hero__title">', '</h1>' ); ?>
	</div>
</section>

<section class="section">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
					<div class="card__body">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div><?php the_excerpt(); ?></div>
					</div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No content found.', 'awsisa' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
