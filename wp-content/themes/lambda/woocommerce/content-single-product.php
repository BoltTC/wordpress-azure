<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>
<div class="product-nav pull-right">
    <?php previous_post_link('%link', '<i class="fa fa-angle-left" data-toggle="tooltip" title="%title" ></i>'); ?>
    <?php next_post_link('%link', '<i class="fa fa-angle-right" data-toggle="tooltip" title="%title"></i>'); ?>
</div>
<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="row product-summary">
        <div class="col-md-6">
            <div class="product-images">
				<?php
					/**
					 * woocommerce_before_single_product_summary hook
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>
		</div>
		<div class="col-md-6">
			<div class="summary entry-summary">
				<?php
					/**
					 * woocommerce_single_product_summary hook
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */
					do_action( 'woocommerce_single_product_summary' );
					if ( !in_array('none', oxy_get_option( 'woo_social_networks' )) ) {
			            echo oxy_shortcode_sharing( array(
	                        'social_networks'   	=> implode( ',', oxy_get_option( 'woo_social_networks' ) ),
	                        'background_shape'  	=> 'rect',
	                        'background_colour'  	=> '',
	                        'margin_top'            => 'no-top',
	                        'margin_bottom'         => 'no-bottom',
	        				'background_show_hover' => 'off'
	                    ));
			        }
				?>
			</div>
		</div>
	</div><!-- .summary -->

	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
