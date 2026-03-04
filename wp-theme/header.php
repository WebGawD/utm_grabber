<?php
/**
 * The header template for the Awsisa theme.
 *
 * @package Awsisa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#0D9488">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link sr-only"><?php esc_html_e( 'Skip to content', 'awsisa' ); ?></a>

<header class="site-header" id="site-header" role="banner">
	<div class="container">
		<nav class="nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'awsisa' ); ?>">

			<!-- Logo -->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav__logo" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php
				$logo_id = get_theme_mod( 'custom_logo' );
				if ( $logo_id ) {
					$logo_html = wp_get_attachment_image( $logo_id, array( 160, 60 ), false, array( 'class' => 'nav__logo-img', 'alt' => get_bloginfo( 'name' ) ) );
					echo $logo_html; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					?>
					<svg class="nav__logo-img" width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<circle cx="22" cy="22" r="22" fill="#0D9488"/>
						<path d="M11 28 C11 20 22 14 22 14 C22 14 33 20 33 28" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
						<ellipse cx="22" cy="30" rx="8" ry="4" fill="white" opacity="0.3"/>
						<circle cx="22" cy="29" r="3" fill="white"/>
					</svg>
					<span class="nav__logo-text">
						AWSISA<br>
						<span class="nav__logo-sub"><?php esc_html_e( 'Watersan Dialogue 2026', 'awsisa' ); ?></span>
					</span>
					<?php
				}
				?>
			</a>

			<!-- Mobile toggle -->
			<button
				class="nav__toggle"
				id="nav-toggle"
				aria-controls="nav-menu"
				aria-expanded="false"
				aria-label="<?php esc_attr_e( 'Toggle navigation', 'awsisa' ); ?>"
			>
				<span></span>
				<span></span>
				<span></span>
			</button>

			<!-- Navigation Menu -->
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'nav-menu',
				'container'      => false,
				'menu_class'     => 'nav__menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
				'fallback_cb'    => 'awsisa_fallback_nav',
			) );
			?>

			<!-- CTA buttons -->
			<div class="nav__actions">
				<a href="<?php echo esc_url( awsisa_register_url() ); ?>" class="btn btn--primary btn--sm">
					<?php esc_html_e( 'Register Now', 'awsisa' ); ?>
				</a>
			</div>
		</nav>
	</div>
</header>

<main id="main-content" class="site-main" role="main">
<?php

/**
 * Fallback navigation if no menu is assigned.
 */
function awsisa_fallback_nav() {
	?>
	<ul class="nav__menu" id="nav-menu" role="list">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>"><?php esc_html_e( 'Agenda', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/speakers/' ) ); ?>"><?php esc_html_e( 'Speakers', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/accommodation/' ) ); ?>"><?php esc_html_e( 'Accommodation', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/sponsors/' ) ); ?>"><?php esc_html_e( 'Sponsors', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><?php esc_html_e( 'Legacy', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/swag-bag/' ) ); ?>"><?php esc_html_e( 'Swag Bag', 'awsisa' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'awsisa' ); ?></a></li>
	</ul>
	<?php
}
