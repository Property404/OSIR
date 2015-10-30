<!DOCTYPE html>
<html>
	<head>
		<title>login</title>
		<link rel="stylesheet" href="../style/main.css" type="text/css">
	</head>
	<body>
		<div class="midcenter">
			<?php
				//Redirect to HTTPS
				if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
					header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
				}
				
				//Check if already logged in
				session_start();
				if(array_key_exists('session',$_SESSION)){
					header("Location: ./");
				}
				
				//Display message if wrong password
				if(array_key_exists('f',$_GET)){
					echo("<div class='alert'>Wrong Password</div>\n");
				}else{
					echo("Password<br>\n");
				}
			?>
			<form method="POST" action="check.php">
				<input type="password" name="password" autofocus="autofocus"><br>
			</form>
		</div>
	</body>
</html>
