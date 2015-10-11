<?php
include("../mods/security.php");
	class Session{
		//Consants
		const _SQL_USERNAME="root";
		const _SQL_PASSWORD="";
		const DB_NAME="osir_admin";
		const _ADMIN_TABLE_DEFINITION="CREATE TABLE ADMIN (
										ID int(11) AUTO_INCREMENT,
										HASH varchar(512) NOT NULL,
										WIN32_BINARY_LAST_UPDATED varchar(512) NOT NULL,
										LINUX_BINARY_LAST_UPDATED varchar(512) NOT NULL,
										PRIMARY KEY (ID)
										)";
		function _ADMIN_TABLE_DEFAULT_VALUES(){
			return "INSERT INTO ADMIN (HASH,WIN32_BINARY_LAST_UPDATED,LINUX_BINARY_LAST_UPDATED) VALUES ('".Security::makeSaltedHash(Security::DEFAULT_PASSWORD) . "','Never updated','Never updated')";
		}
		
		//Create database/table if they don't exist
		function forceConnectDB(){
			
			$db_name=self::DB_NAME;
			$link="";$database="";
			
			//Connect with SQL
			$link=mysqli_connect("localhost",self::_SQL_USERNAME, self::_SQL_PASSWORD);
			if(!$link){
				die("<div class='alert'>Can't connect to database</div>" );
			}
			
			//Try to connect to database
			$database=mysqli_select_db($link,"$db_name");
			
			//Create database if it doesn't exist
			if(!$database){
				echo("<div class='alert'>Creating database<br></div>");
				mysqli_query($link,"CREATE DATABASE $db_name;");
				$database=mysqli_select_db($link,"$db_name");
				if($database){
					echo("Note: database creation successful<br>");
				}else{
					echo("<div class='alert'>Error: database creation failed</div><br>");
				}
			}
			
			//Attempt to create table if it doesn't exist
			$result=mysqli_query($link, "SELECT ID FROM ADMIN");
			if(empty($result)){
				echo("<div class='alert'>Creating table</div><br>\n");
				$result=mysqli_query($link,self::_ADMIN_TABLE_DEFINITION);
				mysqli_query($link,Session::_ADMIN_TABLE_DEFAULT_VALUES());
			}
			return $link;
		}
		
		//If not in session, redirect to login page
		function checkSession(){
			session_start();
			if(!array_key_exists('session',$_SESSION)){
				header("Location: ../admin/login.php");
			}
			
		}
	}
?>
