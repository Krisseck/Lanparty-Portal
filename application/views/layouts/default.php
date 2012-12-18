<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $template['title']; ?> | <?php echo $this->config->item("site_name"); ?></title>
		<?php echo script_tag("http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.1.min.js"); ?>
		<?php echo script_tag("js/bootstrap.min.js"); ?>
		<?php echo link_tag("css/bootstrap.min.css"); ?>
		<?php echo link_tag("css/font-awesome.css"); ?>
		<?php echo link_tag("css/style.css"); ?>
		<meta charset="utf-8">
		<?php echo $template['metadata']; ?>
	</head>
	<body>
		<div id="wrapper">
			<?php echo $template['partials']['adminlogo']; ?>
			<div class="row" id="header">
				<div id="logo" class="span10">
					<a href='/'><img src='/img/logo.png' alt='Urbanlan Party Portal' /></a>
				</div>
				<div id="login-container" class="span2"><?php echo $template['partials']['login']; ?></div>
			</div>
			<?php echo $template['partials']['navigation']; ?>
			<div id="content" class='row'>
				<h1 class='span12'><?php echo $template['title']; ?></h1>
				<?php echo $template['partials']['flashmsg']; ?>
				<?php echo $template['body']; ?>
			</div>
		</div>
		<div id="civars" class="hidden"><?php if(isset($template['partials']['civars'])) echo $template['partials']['civars']; ?></div>
	</body>
</html>
