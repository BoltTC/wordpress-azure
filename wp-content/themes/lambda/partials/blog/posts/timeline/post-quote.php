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
        <div class="post-grid-content">
            <?php echo oxy_shortcode_blockquote( array(
                'who'           => get_the_title(),
                'margin_top'    => 'no-top',
                'margin_bottom' => 'no-bottom'
            ), get_the_content() ); ?>
        </div>
		<span class="cd-date"><?php the_time(get_option('date_format')); ?></span>
	</div> <!-- cd-timeline-content -->
</article>
