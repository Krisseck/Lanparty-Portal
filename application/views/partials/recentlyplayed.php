<div class="song">
	<?php if($cover): ?>
		<img src="<?php echo $cover; ?>" class="cover" />
	<?php endif; ?>
	<h5><?php echo $artist." - ".$title; ?></h5>
	<p><em><?php echo $album; ?></em></p>
</div>