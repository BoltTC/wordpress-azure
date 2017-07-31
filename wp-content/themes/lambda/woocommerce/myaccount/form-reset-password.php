<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
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
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices(); ?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">
    <p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce' ) ); ?></p>

    <div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first form-group">
        <label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="password_1" id="password_1" />
    </div>
    <div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last form-group">
		<label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="password_2" id="password_2" />
    </div>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

	<div class="clear"></div>

	<?php do_action( 'woocommerce_resetpassword_form' ); ?>

    <p class="woocommerce-form-row form-row">
        <input type="hidden" name="wc_reset_password" value="true" />
        <input type="submit" class="woocommerce-Button btn btn-success" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>" />
    </p>

	<?php wp_nonce_field( 'reset_password' ); ?>

</form>