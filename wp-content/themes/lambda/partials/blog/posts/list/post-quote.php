<?php
/**
 * Shows a simple single post in the list view of recent posts sc
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

<a href="<?php the_permalink(); ?>" class="post-list-image">
    <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail($image_size, false, array('alt' => get_the_title($post->ID))); ?>
    <?php endif ?>
</a>
<blockquote class="post-list-quote">
	<?php the_title(); ?>
</blockquote>
<div class="post-list-date">
    <?php the_time(get_option('date_format')); ?>
</div>