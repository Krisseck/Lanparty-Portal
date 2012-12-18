<ul class="nav nav-tabs" id="navigation">
	<li class="<?php if(isset($dashboard_active)) echo $dashboard_active; ?>">
		<a href="<?php echo base_url('admin/dashboard');?>"><?php echo lang("base_dashboard"); ?></a>
	</li>
	<li class="<?php if(isset($seats_active)) echo $seats_active; ?>">
		<a href="<?php echo base_url('admin/seats');?>"><?php echo lang("base_seats"); ?></a>
	</li>
	</li>
</ul>
