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
<article id="post-<?php the_ID(); ?>" <?php post_class( $extra_article_class ); ?>>
<?php if( has_post_thumbnail() && !is_search()) : ?>
    <div class="post-media">
        <div class="figure fade-in text-center figcaption-middle">
            <a href="<?php echo esc_url( strip_tags( $post->post_content ) ); ?>" class="figure-image">
                <?php the_post_thumbnail('full', false, array('alt' => get_the_title($post->ID))); ?>
                <div class="figure-overlay">
                    <div class="figure-overlay-container">
                        <div class="figure-caption">
                            <span class="figure-overlay-icons">
                                <i class="icon-link"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
<?php endif; ?>
    <header class="post-head">
        <h2 class="post-title">
            <a href="<?php echo esc_url( strip_tags( $post->post_content ) ); ?>" target="_blank">
                <?php the_title(); ?>
            </a>
        </h2>
    </header>
</article>