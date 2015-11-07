<?php
	include("../mods/keypair.php");
	include("../mods/session.php");
	include("../mods/weakcrypt.php");
	$link=Session::forceConnectDB();
	
	//Get unencrypted symmetric key and rsa key id
	$symkey=hex2bin($_GET["symkey"]);
	$keyid=$_GET["keyid"];
	
	//Find key pair
	$rsakey=new KeyPair();
	$rsakey->sqlImport($link,$keyid);
	
	//Output encrypted asymmetric key
	echo(WeakCrypt::encrypt($rsakey->encrypt($symkey)));
?>