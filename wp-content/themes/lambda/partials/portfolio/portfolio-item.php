<?php
/**
 * Shows a simple single post
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 1.0
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license http://wiki.envato.com/support/legal-terms/licensing-terms/
 * @version 1.53.1
 */
global $post;
?>
<div class="figure fade-in text-center vertical-middle" >
    <a class="figure-image" href="<?php the_permalink(); ?>">
        <?php echo wp_get_attachment_image(get_post_thumbnail_id($post->ID), 'large', false, array('alt' => get_the_title())); ?>
        <div class="figure-overlay" >
            <div class="figure-overlay-container">
                <div class="figure-caption">
                    <h3><?php the_title(); ?></h3>
                </div>
            </div>
        </div>
    </a>
</div>