
<?php if(isset($template['partials']['msg'])) echo $template['partials']['msg']; ?>

<div class='span12'>
	<form class="form-horizontal" method="post" action="<?php echo base_url('register');?>">
		<fieldset>
			<div class="control-group">
				<label class="control-label" for="code"><?php echo lang("base_code"); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="code" name="code" />
					<p class='help-block'><?php echo lang("base_you_need_code"); ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="seat"><?php echo lang("base_seat"); ?></label>
				<div class="controls">
					<input type="text" class="input-small" id="seat" name="seat" />
				</div>
			</div>
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
			<div class="control-group">
				<label class="control-label" for="password2"><?php echo lang("base_password_again"); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="password2" name="password2" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="email"><?php echo lang("base_email"); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="email" name="email" />
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary" name="register-button" value="<?php echo lang('base_register'); ?>"><i class='icon-key'></i> <?php echo lang("base_register"); ?></button>
			</div>
		</fieldset>
	</form>
</div>