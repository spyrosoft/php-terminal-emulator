<?php
	
	//Me mashing my keyboard, aka uncrackable password.
	//Don't want to accidentally leave this lying around unsecure.
	$password = 'b*d34bai8(XO>UelgxiX(OEuxi9p,i9iboknX<*X>I<BShbiboetbxRLRBI.d,04d3id0X<*($#X980{I$0d';
	
	session_start();
	
	if (isset($_POST['clear']) AND $_POST['clear'] == 'clear') {
		clear_command();
	}
	
	if ( ! isset($_SESSION['persist_commands']) OR ! isset($_SESSION['commands'])) {
		$_SESSION['persist_commands'] = array();
		$_SESSION['commands'] = array();
		$_SESSION['command_responses'] = array();
	}
	
	$toggling_persist = FALSE;
	$toggling_current_persist_command = FALSE;
	
	if (isset($_POST['persist_command_id']) AND is_numeric($_POST['persist_command_id'])) {
		$toggling_persist = TRUE;
		$persist_command_id = $_POST['persist_command_id'];
		if (count($_SESSION['persist_commands']) == $persist_command_id) {
			$toggling_current_persist_command = TRUE;
		} else {
			$_SESSION['persist_commands'][$persist_command_id] =
				! $_SESSION['persist_commands'][$persist_command_id];
		}
	}
	
	$previous_commands = '';
	
	foreach ($_SESSION['persist_commands'] as $index => $persist) {
		if ($persist) {
			$current_command = $_SESSION['commands'][$index];
			if ($current_command != '') {
				$previous_commands .= $current_command . '; ';
			}
		}
	}
	
	if (isset($_POST['command'])) {
		$command = $_POST['command'];
		if ( ! isset($_SESSION['logged_in'])) {
			if ($command == $password) {
				$_SESSION['logged_in'] = TRUE;
				$response = array('Welcome, ' . str_replace("\n", '', `whoami`) . '!!');
			} else {
				$response = array('Incorrect Password');
			}
			array_push($_SESSION['persist_commands'], FALSE);
			array_push($_SESSION['commands'], 'Password: ');
			array_push($_SESSION['command_responses'], $response);
		} else {
			if ($command != '' AND ! $toggling_persist) {
				if ($command == 'logout') {
					session_unset();
					$response = array('Successfully Logged Out');
				} elseif ($command == 'clear') {
					clear_command();
				} else {
					exec($previous_commands . $command . ' 2>&1', $response, $error_code);
					if ($error_code > 0 AND $response == array()) {
						$response = array('Error');
					}
				}
			} else {
				$response = array();
			}
			if ($command != 'logout' AND $command != 'clear') {
				if ($toggling_persist) {
					if ($toggling_current_persist_command) {
						array_push($_SESSION['persist_commands'], TRUE);
						array_push($_SESSION['commands'], $command);
						array_push($_SESSION['command_responses'], $response);
						if ($command != '') {
							$previous_commands = $previous_commands . $command . '; ';
						}
					}
				} else {
					array_push($_SESSION['persist_commands'], FALSE);
					array_push($_SESSION['commands'], $command);
					array_push($_SESSION['command_responses'], $response);
				}
			}
		}
	}
	
	function clear_command()
	{
		if (isset($_SESSION['logged_in'])) {
			$logged_in = TRUE;
		} else {
			$logged_in = FALSE;
		}
		session_unset();
		if ($logged_in) {
			$_SESSION['logged_in'] = TRUE;
		}
	}
	
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
			overflow: auto;
			padding-bottom: 20px;
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
		.terminal .persist_button {
			float: right;
			border-width: 1px 0 1px 1px;
			border-style: solid;
			border-color: #00FF00;
			clear: both;
		}
	</style>
</head>
<body>
	<div class="content">
		<div class="terminal" onclick="document.getElementById('command').focus();" id="terminal">
			<div class="bar">
				<?php echo `whoami`, ' - ', exec($previous_commands . 'pwd'); ?>
			</div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="commands" id="commands">
				<input type="hidden" name="persist_command_id" id="persist_command_id" />
				<?php if ( ! empty($_SESSION['commands'])) { ?>
				<div>
					<?php foreach ($_SESSION['commands'] as $index => $command) { ?>
					<input type="button" value="<?php if ($_SESSION['persist_commands'][$index]) { ?>Un-Persist<?php } else { ?>Persist<?php } ?>" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" onclick="toggle_persist_command(<?php echo $index; ?>);" class="persist_button" />
					<pre><?php echo '$ ', $command, "\n"; ?></pre>
					<?php foreach ($_SESSION['command_responses'][$index] as $value) { ?>
					<pre><?php echo htmlentities($value), "\n"; ?></pre>
					<?php } ?>
					<?php } ?>
				</div>
				<?php } ?>
				$ <?php if ( ! isset($_SESSION['logged_in'])) { ?>Password:
				<input type="password" name="command" id="command" />
				<?php } else { ?>
				<input type="text" name="command" id="command" autocomplete="off" onkeydown="return command_keyed_down(event);" />
				<input type="button" value="Persist" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" onclick="toggle_persist_command(<?php if (isset($_SESSION['commands'])) { echo count($_SESSION['commands']); } else { echo 0; } ?>);" class="persist_button" />
				<?php } ?>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		
		<?php
			$single_quote_cancelled_commands = array();
			if ( ! empty( $_SESSION['commands'] ) ) {
				foreach ($_SESSION['commands'] as $command) {
					$cancelled_command = str_replace('\\', '\\\\', $command);
					$cancelled_command = str_replace('\'', '\\\'', $command);
					$single_quote_cancelled_commands[] = $cancelled_command;
				}
			}
		?>
		
		var previous_commands = ['', '<?php echo implode('\', \'', $single_quote_cancelled_commands) ?>', ''];
		
		var current_command_index = previous_commands.length - 1;
		
		document.getElementById('command').select();
		
		document.getElementById('terminal').scrollTop = document.getElementById('terminal').scrollHeight;
		
		function toggle_persist_command(command_id)
		{
			document.getElementById('persist_command_id').value = command_id;
			document.getElementById('commands').submit();
		}
		
		function command_keyed_down(event)
		{
			var key_code = get_key_code(event);
			if (key_code == 38) { //Up arrow
				fill_in_previous_command();
			} else if (key_code == 40) { //Down arrow
				fill_in_next_command();
			} else if (key_code == 9) { //Tab
				
			} else if (key_code == 13) { //Enter
				if (event.shiftKey) {
					toggle_persist_command(<?php
						if (isset($_SESSION['commands'])) {
							echo count($_SESSION['commands']);
						} else {
							echo 0;
						}
					?>);
					return false;
				}
			}
			return true;
		}
		
		function fill_in_previous_command()
		{
			current_command_index--;
			if (current_command_index < 0) {
				current_command_index = 0;
				return;
			}
			document.getElementById('command').value = previous_commands[current_command_index];
		}
		
		function fill_in_next_command()
		{
			current_command_index++;
			if (current_command_index >= previous_commands.length) {
				current_command_index = previous_commands.length - 1;
				return;
			}
			document.getElementById('command').value = previous_commands[current_command_index];
		}
		
		function get_key_code(event)
		{
			var event_key_code = event.keyCode;
			return event_key_code;
		}
		
	</script>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="clear" value="clear" />
		<input type="submit" value="Clear" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" />
	</form>
</body>
</html>
