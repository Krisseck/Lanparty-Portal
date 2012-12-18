<div class='hero-unit'>
	
	<h2><?php echo $title; ?></h2>
	
	<div class='description'>
		<?php echo $description; ?>
	</div>

	<?php if($max_teams != 0): ?>
		<div class='teams'>
			<strong><?php echo lang("base_participants"); ?>: <?php echo $c_teams."/".$max_teams; ?></strong>
		</div>
	<?php endif; ?>

	<?php if($binarybeast!="" && $c_teams<$max_teams): ?>
		<a href="http://www.binarybeast.com/<?php echo $binarybeast; ?>" class="btn btn-primary participate" target="_blank"><?php echo lang("base_participate"); ?></a>
	<?php endif; ?>

	<?php if($binarybeast!=""): ?>
		<iframe src="http://binarybeast.com/<?php echo $binarybeast; ?>/full" class="binarybeast-iframe" width="820" height="<?php echo $binarybeast_height; ?>" scrolling="auto" frameborder="0">
    	</iframe>
    <?php endif; ?>

</div>