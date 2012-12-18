<div class='span12'>
	<form class="form-horizontal" method="post" action="<?php echo base_url('login');?>">
		<fieldset>
			<div class="control-group">
				<label class="control-label" for="username"><?php echo lang("base_username"); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="username" name="username" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password"><?php echo lang("base_password"); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="password" name="password" />
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary" name="login-button" value="<?php echo lang('base_login'); ?>"><i class='icon-key'></i> <?php echo lang("base_login"); ?></button>
			</div>
		</fieldset>
	</form>
</div>