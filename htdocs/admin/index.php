<!DOCTYPE HTML>
<html>
	<head>
		<title>OSIR Control Panel</title>
		<link rel="stylesheet" href="../style/main.css">
		<?php
		//Add menu
		include("./menu_bar.php");
		?>
	</head>
	<body>
		<div class="central">
		<?php
			//Check any messages
			if (array_key_exists("msg",$_GET))
				echo("<div class='alert'>".$_GET["msg"]."</div>\n");
			if (array_key_exists("note",$_GET))
				echo("<div>".$_GET["note"]."</div>\n");
			
			//See if on an option page
			if(array_key_exists("op",$_GET)){
				
				//Help the user out with working the server
				if($_GET["op"]=="help"){
					
					echo("Welcome to the OSIR Control Panel.<br>From here, you can update your OSIR binaries or manage your RSA keypairs.");
				}
				
				//Make change Password form
				if($_GET["op"]=="changepwd"){
					echo('<form method="POST" action="index.php?op=chpwdact">
						Old Password:<br><input type="password" name="oldpwd" autofocus="autofocus"><p>
						New Password:<br><input type="password" name="newpwd"><p>
						Confirm New Password:<br><input type="password" name="newpwd2"><p>
						<input type="submit" text="Submit">');
				}
				
				//Chage password
				if($_GET["op"]=="chpwdact"){
					//Fetch correct hash
					$link=Session::forceConnectDB();
					$correct_hash=mysqli_fetch_row(mysqli_query($link,"select * from admin where ID=1"))[1];
					
					if(Security::makeSaltedHash($_POST["oldpwd"],$correct_hash)==$correct_hash){
						if($_POST["newpwd"]==$_POST["newpwd2"]){
							if(strlen($_POST["newpwd"])>3){
							//Set new hash
							mysqli_query($link,"UPDATE admin SET hash='".Security::makeSaltedHash($_POST["newpwd"]) . "' WHERE ID=1");
							$_SESSION["default_password"]="false";
							header("Location: ?note=Password%20changed!");
							}else{
								header("Location: ?op=changepwd&msg=Password%20too%20short");
							}
						}else{
							header("Location: ?op=changepwd&msg=Passwords%20don't%20match");
						}
					}else{
						header("Location: ?op=changepwd&msg=Wrong%20password");
					}
				}
				
				//Make binary update form
				if($_GET["op"]=="manbin"){
					//Fetch last-updated times
					$link=Session::forceConnectDB();
					$win_last_updated=mysqli_fetch_row(mysqli_query($link,"SELECT WIN32_BINARY_LAST_UPDATED FROM admin WHERE ID=1"))[0];
					$lin_last_updated=mysqli_fetch_row(mysqli_query($link,"SELECT LINUX_BINARY_LAST_UPDATED FROM admin WHERE ID=1"))[0];
					
					//Create form
					echo("<form method='post' action='?op=uploadbins' enctype='multipart/form-data'>");
					
					//Windows
					echo("<strong>Windows</strong><br><time>$win_last_updated</time><br>");
					for($i=0;$i<13;$i++)echo("&nbsp;");
					echo("<input type='file' name='winbin'><p>");
					
					//Linux
					echo("<strong>Linux (ELF)</strong><br><time>$lin_last_updated</time><br>");
					for($i=0;$i<13;$i++)echo("&nbsp;");
					echo("<input type='file' name='linbin'><p>");
					echo("<input type='submit' value='Update'>\n</form>");
				}
				
				//Update binaries
				if($_GET['op']=="uploadbins"){
					//Connect to database
					$link=Session::forceConnectDB();
						
					echo("Updating binaries...<br>");
					$target_file_linux="../binaries/elf64";
					$target_file_windows="../binaries/win64.exe";
					
					//Move temp files to proper directory
					$moved_win=move_uploaded_file($_FILES["winbin"]["tmp_name"], $target_file_windows);
					$moved_lin=move_uploaded_file($_FILES["linbin"]["tmp_name"], $target_file_linux);		

					//Update last-updated values
					if($moved_win!==false){
						mysqli_query($link,"UPDATE admin SET  WIN32_BINARY_LAST_UPDATED='" . date("Y-m-d H:i:s") . "' WHERE ID=1");
					}
					if($moved_lin!==false){
						mysqli_query($link,"UPDATE admin SET  LINUX_BINARY_LAST_UPDATED='" . date("Y-m-d H:i:s") . "' WHERE ID=1");
					}

					//Update encrypted binaries
					header("Location: update_encrypted_binaries.cgi");
					
				}
				
			}
		?>
		</div>
	</body>
</html>