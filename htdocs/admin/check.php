<!DOCTYPE html>
<html>
<head><link rel='stylesheet' href='../style/main.css'></head>
<div class='midcenter'>
<?php
	/*This module checks if login info is correct*/

	//Import necessary modules
	include("../mods/session.php");
	
	//Connect to database
	$link=Session::forceConnectDB();
	
	//Get attempts
	$attempts=mysqli_fetch_row(mysqli_query($link,"select ATTEMPTS from admin where ID=1"))[0];
	
	//Verify hash
	$correct_hash=mysqli_fetch_row(mysqli_query($link,"select HASH from admin where ID=1"))[0];
	if(Security::makeSaltedHash($_POST["password"],$correct_hash)==$correct_hash){
		
		//Correct hash - start session
		session_start();
		$_SESSION['session']="valid";
		$_SESSION['default_password']="false";
		if($_POST["password"]==Security::DEFAULT_PASSWORD){
			$_SESSION['default_password']="true";
		}
		
		//Reset attempts
		mysqli_query($link,"UPDATE admin SET ATTEMPTS=0 WHERE ID=1");
		
		//Go to index
		header("Location: ./");
	}else{
		//Increment attempts
		$attempts++;
		mysqli_query($link,"UPDATE admin SET ATTEMPTS=$attempts WHERE ID=1");
		
		//Incorrect hash, go back to login page
		header("Location: login.php?f");
	}
	

?>
</div>
</html>
