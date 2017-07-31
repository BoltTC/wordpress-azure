<?php
/**
 * Creates a slide with an image
 *
 * @package Lambda
 * @subpackage Admin
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.53.1
 * @author Oxygenna.com
 */
?>
<figure>
	<?php echo wp_get_attachment_image($item->ID, $image_size, false, array('alt' => get_the_title())); ?>
<?php if ($captions == 'show') : ?>
	<figcaption>
		<h3><?php echo $item->post_title; ?></h3>
		<p><?php echo $item->post_excerpt; ?></p>
	</figcaption>
<?php endif; ?>
</figure>