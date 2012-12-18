<ul class="nav nav-tabs" id="navigation">
	<li class="<?php if(isset($dashboard_active)) echo $dashboard_active; ?>">
		<a href="<?php echo base_url('dashboard');?>"><?php echo lang("base_dashboard"); ?></a>
	</li>
	<li class="<?php if(isset($events_active)) echo $events_active; ?>">
		<a href="<?php echo base_url('events');?>"><?php echo lang("base_schedule"); ?></a>
	</li>
	<li class="<?php if(isset($compos_active)) echo $compos_active; ?>">
		<a href="<?php echo base_url('compos');?>"><?php echo lang("base_tournaments"); ?></a>
	</li>
	<li class="<?php if(isset($pizza_active)) echo $pizza_active; ?>">
		<a href="<?php echo base_url('pizza');?>"><?php echo lang("base_pizza"); ?></a>
	</li>
	<li class="<?php if(isset($map_active)) echo $map_active; ?>">
		<a href="<?php echo base_url('map');?>"><?php echo lang("base_map"); ?></a>
	</li>
	<li class="<?php if(isset($songs_active)) echo $songs_active; ?>">
		<a href="<?php echo base_url('songs');?>"><?php echo lang("base_song_requests"); ?></a>
	</li>
</ul>
