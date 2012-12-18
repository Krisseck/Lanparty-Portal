<div class="span12">
	<form class="well form-search" id="search-seats">
		<input type="text" class="input-medium search-query">
		<button type="submit" class="btn"><?php echo lang("base_search"); ?></button>
	</form>
</div>

<div id="seats">
	<?php echo $template['partials']['seats']; ?>
</div>