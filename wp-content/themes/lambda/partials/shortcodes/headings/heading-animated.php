<<?php echo esc_attr($header_type); ?> class="cd-headline <?php echo esc_attr($text_animation) ?> <?php echo esc_attr(implode( ' ', $headline_classes )); ?>" data-os-animation="<?php echo esc_attr($scroll_animation); ?>" data-os-animation-delay="<?php echo esc_attr($scroll_animation_delay); ?>s">
	<span><?php echo $content; ?></span>
	<span class="cd-words-wrapper">
        <?php if (!empty($labels)): ?>
            <?php foreach ($labels as $label): ?>
                <?php if ($label == $labels[0]): ?>
                    <b class="is-visible"><?php echo esc_attr($label); ?></b>
                <?php else : ?>
                    <b><?php echo esc_attr($label); ?></b>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
	</span>
</<?php echo esc_attr($header_type); ?>>
