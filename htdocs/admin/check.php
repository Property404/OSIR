<?php
	include("../mods/session.php");
	
	
	//Connect to database
	$link=Session::forceConnectDB();
	
	//Verify hash
	$correct_hash=mysqli_fetch_row(mysqli_query($link,"select * from admin where ID=1"))[1];
	if(Security::makeSaltedHash($_POST["password"],$correct_hash)==$correct_hash){
		
		//Correct hash - start session
		session_start();
		$_SESSION['session']="valid";
		$_SESSION['default_password']="false";
		if($_POST["password"]==Security::DEFAULT_PASSWORD){
			$_SESSION['default_password']="true";
		}
		header("Location: ./");
	}else{
		//Incorect hash, go back to login page
		header("Location: login.php?f=1");
	}
	

?>