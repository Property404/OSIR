<?php
	include("../mods/keypair.php");
	include("../mods/session.php");
	include("../mods/weakcrypt.php");
	$link=Session::forceConnectDB();
	
	//Get unencrypted symmetric key and rsa key id
	$symkey=hex2bin($_GET["symkey"]);
	$keyid=$_GET["keyid"];
	
	//Find key pair
	$rsakey=new KeyPair(true);
	$rsakey->sqlImport($link,$keyid);
	
	//Encrypt symmetric key
	$encrypted_key=$rsakey->encrypt($symkey);
	
	//Make assertion
	assert($symkey!==$rsakey->decrypt($encrypted_key));
	
	echo(WeakCrypt::encrypt($encrypted_key));
?>
