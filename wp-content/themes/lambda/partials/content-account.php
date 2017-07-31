<?php
/**
 * Shows a woocommerce account page
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 1.0
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license http://wiki.envato.com/support/legal-terms/licensing-terms/
 * @version 1.53.1
 */

global $woocommerce;
$template_margin = oxy_get_option('template_margin'); ?>
<section class="section section-commerce">
    <div class="container">
        <?php wc_print_notices(); ?>
        <div class="row element-top-<?php echo esc_attr($template_margin); ?> element-bottom-<?php echo esc_attr($template_margin); ?>">
            <div class="col-md-3">
                <?php
                    get_template_part('woocommerce/myaccount/navigation');
                 ?>
            </div>
            <div class="col-md-9">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</section>
