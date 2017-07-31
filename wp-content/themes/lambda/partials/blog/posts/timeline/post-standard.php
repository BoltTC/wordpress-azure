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
?>
<article id="post-<?php the_ID(); ?>" class="post-grid cd-timeline-block element-bottom-20">
    <?php echo $post_icon; ?>
	<div class="cd-timeline-content">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('related-post-image', false, array('alt' => get_the_title(), 'class' => 'img-responsive')); ?>
            </a>
        <?php endif ?>
        <div class="post-grid-content">
            <<?php echo esc_attr($title_tag); ?> class="post-grid-content-title">
                <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                </a>
            </<?php echo esc_attr($title_tag); ?>>
            <p><?php echo get_the_excerpt(); ?></p>
        </div>
		<span class="cd-date"><?php the_time(get_option('date_format')); ?></span>
	</div> <!-- cd-timeline-content -->
</article>
