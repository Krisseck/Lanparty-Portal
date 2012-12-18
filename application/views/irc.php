<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>IRC</title>
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			height: 100%;
			width: 100%;
		}

		#chat {
			width: 100%;
			height: 100%;
			border: 0;
		}
	</style>
</head>

<body>
	<iframe src="http://webchat.quakenet.org/?nick=<?php echo $nick; ?>&channels=<?php echo $channel; ?>&prompt=1" id="chat" class="span9"></iframe>
</body>
</html>
