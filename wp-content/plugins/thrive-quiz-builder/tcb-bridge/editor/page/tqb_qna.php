<?php
global $variation;

include tqb()->plugin_path( 'tcb-bridge/editor/page/head.php' ); ?>
<div style="display: none" class="bSe"></div>
<div id="tqb-editor-replace">
	<div class="tqb-triggered">
		<div id="tve_flt" class="tve_flt">
			<div class="tl-style tqb-template-style-<?php echo $variation['style'] ?>">
				<?php echo TCB_Hooks::tqb_editor_custom_content( $variation ); ?>
			</div>
		</div>
	</div>
	<div style="opacity: .6; padding-top: 240px; text-align: center; position: relative; z-index: -1;">
		<h4><?php echo __( 'This is a Variation type called "Q&A". It is displayed on posts that have its code in the content.', Thrive_Quiz_Builder::T ) ?></h4>
	</div>
</div>

<?php include tqb()->plugin_path( 'tcb-bridge/editor/page/footer.php' ); ?>