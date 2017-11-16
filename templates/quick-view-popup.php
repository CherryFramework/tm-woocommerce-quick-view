<?php
/**
 * Quick View popup template.
 * Note: please do not remove 'tm-quick-view-popup' class from popup wrapper and 'tm-quick-view-popup__content' class from content holder.
 */
?>
<div class="tm-quick-view-popup">
	<div class="tm-quick-view-popup__overlay"></div>
	<div class="tm-quick-view-popup__content"><?php
		$this->popup_loader();
	?></div>
	<?php $this->prev_next_buttons(); ?>
	<?php $this->close_button(); ?>
</div>