<?php
/**
 * Shows a posts featured image
 *
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.53.1
 */

global $post;

$image_link          = '';
$open_magnific_class = '';

if (is_single()) {
    // image should link to magnific on single post
    $image      = get_post_thumbnail_id($post->ID);
    $image_size = oxy_get_post_image_size();
    $src = wp_get_attachment_image_src($image, $image_size);
    if (false !== $src && is_array($src)) {
        $image_link = $src[0];
    }
    $icon = 'plus';
    $open_magnific_class = 'magnific';
} else {
    $blog_image_linkable = oxy_get_option('blog_image_linkable');
    // image should link to single post in lists
    $image_link = get_permalink($post->ID);
    $icon = 'link';
}

?>
<div class="figure fade-in text-center figcaption-middle">
    <?php if (!is_single() && 'off' === $blog_image_linkable): ?>
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('full', array('alt' => get_the_title($post->ID))); ?>
        <?php endif ?>
    <?php else: ?>
        <a href="<?php echo esc_url($image_link); ?>" class="figure-image <?php echo esc_attr($open_magnific_class); ?>">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', array('alt' => get_the_title($post->ID))); ?>
            <?php endif ?>
            <div class="figure-overlay">
                <div class="figure-overlay-container">
                    <div class="figure-caption">
                        <span class="figure-overlay-icons">
                            <i class="icon-<?php echo esc_attr($icon); ?>"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    <?php endif; ?>
</div>
