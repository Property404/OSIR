<?php
	include_once("../mods/keypair.php");
	include_once("../mods/session.php");
	include_once("../mods/weakcrypt.php");
	include_once("../mods/eventlog.php");
	$link=Session::forceConnectDB();
	
	//Get unencrypted symmetric key and rsa key id
	$symkey=hex2bin($_GET["symkey"]);
	$keyid=$_GET["keyid"];
	
	//Find key pair
	$rsakey=new KeyPair(true);
	$rsakey->sqlImport($link,$keyid);
	
	//Encrypt symmetric key
	$encrypted_key=$rsakey->encrypt($symkey);
	
	//Check if things are valid
	if($symkey==$rsakey->decrypt($encrypted_key)){
		//Things are valid
		echo(WeakCrypt::encrypt($encrypted_key));
		EventLog::addEntry($link, "Client", "Encrypted symmetric key");
	}else{
		//Things aren't valid
		echo("Encryption Failed");
		EventLog::addEntry($link, "Client", "Encrypted symmetric key(failed)");
	}
?>
