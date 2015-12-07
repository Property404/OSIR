<!DOCTYPE html>
<html>
<head><link rel='stylesheet' href='../style/main.css'></head>
<div class='midcenter'>
<?php
	/*This module checks if login info is correct*/
	//Set error reporting
	error_reporting(E_ALL);
	
	//Import necessary modules
	include_once("../mods/session.php");
	include_once("../mods/security.php");
	include_once("../mods/eventlog.php");
	
	//Connect to database
	$link=Session::forceConnectDB();
	
	//Get attempts
	$attempts=mysqli_fetch_row(mysqli_query($link,"select ATTEMPTS from admin where ID=1"))[0];
	
	//Verify hash
	$correct_hash=mysqli_fetch_row(mysqli_query($link,"select HASH from admin where ID=1"))[0];
	$this_hash=Security::makeSaltedHash($_POST["password"],$correct_hash);

	if($this_hash==$correct_hash){
		
		//Correct hash - start session
		session_start();
		$_SESSION['admin_session']="valid";
		$_SESSION['default_password']="false";
		$_SESSION['timeout']=time();
		if($_POST["password"]==Security::DEFAULT_PASSWORD){
			$_SESSION['default_password']="true";
		}
		
		//Reset attempts
		mysqli_query($link,"UPDATE admin SET ATTEMPTS=0 WHERE ID=1");
		
		//Go to index
		header("Location: ./");
	}else if (!$this_hash ||!$correct_hash){
		header("Location: login.php?error");
	}else{
		//Increment attempts
		$attempts++;
		mysqli_query($link,"UPDATE admin SET ATTEMPTS=$attempts WHERE ID=1");
		
		if($attempts>10){
			EventLog::addEntry($link, EventLog::SECURITY_EVENT, "Failed login with $attempts attempts");
			die(":(");
		}
		
		//Incorrect hash, go back to login page
		header("Location: login.php?f");
	}
	

?>
</div>
</html>
