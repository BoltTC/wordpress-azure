<div id="cd-timeline" class="cd-container <?php echo esc_attr(implode(' ', $classes)); ?>" data-os-animation="<?php echo esc_attr($scroll_animation); ?>" data-os-animation-delay="<?php echo esc_attr($scroll_animation_delay); ?>s">
   <?php
   foreach($posts as $post):
       setup_postdata($post);
       // get related post image
       $image = '';
       if (has_post_thumbnail()) {
           $attachment = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'related-post-image');
           if(!empty($attachment)) {
               $image = $attachment[0];
           }
       }

       $format = get_post_format();
       if($format !== 'quote' && $format !== 'link') :
           $format = 'standard';
       endif;
       switch ($format) {
           case 'quote':
               $post_icon = '<div class="cd-timeline-img cd-quote"><i class="fa fa-quote-right"></i></div>';
               break;
           case 'link':
               $post_icon = '<div class="cd-timeline-img cd-link"><i class="fa fa-link"></i></div>';
               break;
           default:
               $post_icon = '<div class="cd-timeline-img cd-picture"><i class="fa fa-picture-o"></i></div>';
               break;
       } ?>

       <?php include(locate_template('partials/blog/posts/timeline/post-' . $format . '.php')); ?>
   <?php endforeach; ?>
</div>
