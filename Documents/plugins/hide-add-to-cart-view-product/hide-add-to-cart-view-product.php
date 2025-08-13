<?php
/**
 * Plugin Name: WooCommerce Hide Add to Cart, Change to View Product, Hide Price (Elementor Compatible)
 * Description: Hides the 'Add to Cart' button, changes it to 'View Product', and hides the product price on WooCommerce product listings and single product pages. Compatible with Elementor.
 * Version: 1.0.0
 * Author: Lwazi Ndlebe
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Remove the default 'Add to Cart' button on product listings.
 */
function custom_remove_add_to_cart_loop() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action( 'init', 'custom_remove_add_to_cart_loop' );

/**
 * Add a 'View Product' button on product listings.
 */
function custom_add_view_product_loop() {
	global $product;
	echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="button">' . esc_html__( 'View Product', 'woocommerce' ) . '</a>';
}
add_action( 'woocommerce_after_shop_loop_item', 'custom_add_view_product_loop', 10 );

/**
 * Remove the default 'Add to Cart' form on single product pages.
 */
function custom_remove_add_to_cart_single() {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
add_action( 'init', 'custom_remove_add_to_cart_single' );

/**
 * Add a 'View Product' button on single product pages.
 */
function custom_add_view_product_single() {
	global $product;
	echo '<p><a href="' . esc_url( $product->get_permalink() ) . '" class="button alt">' . esc_html__( 'View Product', 'woocommerce' ) . '</a></p>';
}
add_action( 'woocommerce_single_product_summary', 'custom_add_view_product_single', 30 );

/**
 * Remove the product price on product listings.
 */
function custom_remove_product_price_loop() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
}
add_action( 'init', 'custom_remove_product_price_loop' );

/**
 * Remove the product price on single product pages.
 */
function custom_remove_product_price_single() {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
}
add_action( 'init', 'custom_remove_product_price_single' );

/**
 * Remove the product price in Elementor Product Grid Widget.
 */
function custom_remove_elementor_product_grid_price() {
	remove_action( 'elementor/widgets/woocommerce-grid/render', 'WC_Widget_Products::products_loop', 30 );
	remove_action( 'elementor/widgets/woocommerce-carousel/render', 'WC_Widget_Products::products_loop', 30 );

	// Re-add the loop with modified price display
	add_action( 'elementor/widgets/woocommerce-grid/render', 'custom_elementor_products_loop', 30 );
	add_action( 'elementor/widgets/woocommerce-carousel/render', 'custom_elementor_products_loop', 30 );
}
add_action( 'elementor/widgets/widgets_registered', 'custom_remove_elementor_product_grid_price' );

/**
 * Custom products loop for Elementor to hide price.
 *
 * @param \Elementor\Widget_Base $widget The widget instance.
 */
function custom_elementor_products_loop( $widget ) {
	global $product;

	if ( $widget->get_name() === 'woocommerce-grid' || $widget->get_name() === 'woocommerce-carousel' ) {
		$settings = $widget->get_settings_for_display();
		$products = wc_get_products( $widget->query_args );

		if ( $products ) {
			echo '<ul class="wc-products elementor-grid elementor-has-item-ratio">';
			foreach ( $products as $product ) {
				setup_postdata( $product->get_id() );
				?>
				<li <?php wc_product_class( 'product elementor-grid-item', $product ); ?>>
					<div class="elementor-product-wrapper">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="elementor-product-image">
							<?php echo $product->get_image(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<div class="elementor-product-details">
							<?php
							/**
							 * Hook: woocommerce_shop_loop_item_title.
							 *
							 * @hooked woocommerce_template_loop_product_title - 10
							 */
							do_action( 'woocommerce_shop_loop_item_title' );

							/**
							 * Hook: woocommerce_after_shop_loop_item_title.
							 *
							 * @hooked woocommerce_template_loop_rating - 5
							 * @hooked woocommerce_template_loop_price - 10 (REMOVED BY custom_remove_product_price_loop)
							 */
							do_action( 'woocommerce_after_shop_loop_item_title' );

							/**
							 * Hook: woocommerce_after_shop_loop_item.
							 *
							 * @hooked woocommerce_template_loop_product_link_close - 5
							 * @hooked woocommerce_template_loop_add_to_cart - 10 (REMOVED BY custom_remove_add_to_cart_loop)
							 */
							do_action( 'woocommerce_after_shop_loop_item' );

							// Add custom "View Product" button for Elementor
							echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="button elementor-button elementor-size-sm">' . esc_html__( 'View Product', 'woocommerce' ) . '</a>';
							?>
						</div>
					</div>
				</li>
				<?php
			}
			echo '</ul>';
		} else {
			// No products found
		}
	}
}