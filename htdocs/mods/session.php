<?php
include("../mods/security.php");
include("../mods/defaultkeys.php");
	class Session{
		//Public Constants
		const DB_NAME="osir_admin";
		
		//Private Constants
		const _SQL_USERNAME="root";
		const _SQL_PASSWORD="";
		const _ADMIN_TABLE_DEFINITION="CREATE TABLE ADMIN (
										ID int(11) AUTO_INCREMENT,
										HASH varchar(512) NOT NULL,
										ATTEMPTS int(11),
										WIN32_BINARY_LAST_UPDATED varchar(512) NOT NULL,
										LINUX_BINARY_LAST_UPDATED varchar(512) NOT NULL,
										PRIMARY KEY (ID)
										)";
		private static function _ADMIN_TABLE_DEFAULT_VALUES(){
			return "INSERT INTO ADMIN (HASH,ATTEMPTS,WIN32_BINARY_LAST_UPDATED,LINUX_BINARY_LAST_UPDATED) VALUES ('".Security::makeSaltedHash(Security::DEFAULT_PASSWORD) . "',0,'Never updated','Never updated')";
		}
		const _KEYS_TABLE_DEFINITION="CREATE TABLE RSAKEYS (
										ID int(11) AUTO_INCREMENT,
										KEYNUMBER int(11),
										PUBLIC varchar(4096) NOT NULL,
										PRIVATE varchar(4096) NOT NULL,
										SIZE int(11),
										PRIMARY KEY (ID)
										)";
		const _KEYS_TABLE_DEFAULT_VALUES="INSERT INTO RSAKEYS (PUBLIC,PRIVATE,SIZE) VALUES ('".DEFAULT_PUBLIC_KEY."','".DEFAULT_PRIVATE_KEY."',1024)";
		
		//Create database/table if they don't exist
		public static function forceConnectDB(){
			$db_name=self::DB_NAME;
			
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
				mysqli_query($link,self::_KEYS_TABLE_DEFINITION);
				mysqli_query($link,Session::_KEYS_TABLE_DEFAULT_VALUES);
				mysqli_query($link,self::_ADMIN_TABLE_DEFINITION);
				mysqli_query($link,Session::_ADMIN_TABLE_DEFAULT_VALUES());
			}
			return $link;
		}
		
		
		//If not in session, redirect to login page
		public static function checkSession(){
			//Make sure using TLS
			if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
				header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
			}
			
			//Check session
			session_start();
			if(!array_key_exists('session',$_SESSION)){
				header("Location: ../admin/login.php");
			}
			
		}
	}
?>
