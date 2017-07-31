<?php
/**
 * Default 404 template
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 0.1
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license http://wiki.envato.com/support/legal-terms/licensing-terms/
 * @version 1.53.1
 */

global $post;
$id_404 = oxy_get_option( '404_page' );
$id_404 = function_exists('icl_object_id') ? icl_object_id($id_404, 'page', true) : $id_404;
if( $id_404 ) {
    get_header();
    $post = get_post( $id_404 );
    setup_postdata( $post );
    oxy_page_header( $post->ID, array( 'heading_type' => 'page' ) );

    get_template_part('partials/content', 'page');

    $allow_comments = oxy_get_option( 'site_comments' );
    ?>
    <?php if( $allow_comments == 'pages' || $allow_comments == 'all' ) : ?>
    <section class="section">
        <div class="container">
            <div class="row">
                <?php comments_template( '', true ); ?>
            </div>
        </div>
    </section>
    <?php
    endif;
}
get_footer();
