<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="mini-cart-overview dropdown navbar-right">
    <a href="<?php echo WC()->cart->get_cart_url(); ?>" data-toggle="dropdown">
        <?php $animate = ( WC()->cart->cart_contents_count % 2 == 0 )  ? 'pulse-one' : 'pulse-two'; ?>

        <i class="icon icon-bag animated <?php echo $animate; ?>"></i>

        <span class="mini-cart-count"><?php echo WC()->cart->cart_contents_count; ?></span>
        <?php echo WC()->cart->get_cart_subtotal(); ?>
    </a>

    <ul role="menu" class="dropdown-menu cart_list product_list_widget <?php echo $args['list_class']; ?>">

    	<?php if ( ! WC()->cart->is_empty() ) : ?>

    		<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :

                $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

    			// Only display if allowed
    			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {


                    $product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
                    $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                    $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                }
    			?>

    			<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <div class="product-mini">
                        <div class="product-image">
                            <?php if ( ! $_product->is_visible() ) : ?>
                                <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $product_permalink ); ?>">
                                    <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h4 class="product-details-heading">
                                <a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">
        					       <?php echo apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product ); ?>

        				        </a>
                            </h4>
                            <p>
            				    <?php echo WC()->cart->get_item_data( $cart_item ); ?>
                            </p>
                            <p>
        				        <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                            </p>
                            <?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'woocommerce' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
                            ?>
                        </div>
                    </div>
    			</li>

    		<?php endforeach; ?>

    	<?php else : ?>

    		<li class="empty">
                <div class="product-mini">
                    <p><?php _e( 'No products in the cart.', 'woocommerce' ); ?></p>
                </div>
            </li>

    	<?php endif; ?>

        <?php if ( ! WC()->cart->is_empty() ) : ?>

            <li class="cart-actions">
                <p class="total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

                <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

                <div class="buttons">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>"> <?php _e( 'View cart', 'woocommerce' ); ?></a>
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
                </div>
            </li>

        <?php endif;
		do_action( 'woocommerce_mini_cart_contents' ); ?>
    </ul><!-- end product list -->
</div>
<?php do_action( 'woocommerce_after_mini_cart' ); ?>
