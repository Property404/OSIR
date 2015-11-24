<?php
	include_once("../mods/weakcrypt.php");
	include_once("../mods/security.php");
	$test_string="'sall good\x00\xff";
	$results="";
	
	//Check Weakcrypt module
	$results.="Weakcrypt::en/decrypt:\t\t".((Weakcrypt::decrypt(Weakcrypt::encrypt($test_string))===$test_string)?"OK\n":"Failed\n");
	
	//Check Security module
	$hash=Security::makeSaltedHash(Security::DEFAULT_PASSWORD);
	$results.="Security::makeSaltedHash:\t".((Security::makeSaltedHash(Security::DEFAULT_PASSWORD,$hash)===$hash)?"OK\n":"Failed\n");
	
	
	//Report results
	echo(str_replace("\n",array_key_exists("no_html",$_GET)?"\n":"<br>\n",$results));
?>