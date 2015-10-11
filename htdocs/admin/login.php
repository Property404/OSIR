<!DOCTYPE html>
<html>
	<head>
	<title>login</title>
	<link rel="stylesheet" href="../style/main.css" type="text/css">
	</head>
	<body>
		<div class="central">
			<?php
				if(array_key_exists('f',$_GET)){
					echo("<div class='alert'>Wrong Password</div>");
				}else{
					echo("Password</br>");
				}
			?>
			<form method="POST" action="check.php">
				<input type="password" name="password" autofocus="autofocus"><br>
				
			</form>
		</div>
	</body>
</html>
