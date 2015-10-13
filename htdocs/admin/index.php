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
			//Define functions here
			function escapeText($text, $tabWidth)
			{
				$text=str_replace("&","&amp;",$text);
				$text=str_replace("<","&lt;",$text);
				return $text;
			}

			//Recursively walk directories
			function walkDir($dir){
				$files=scandir($dir);
				$flist=array();
			
				//Go through directory
				foreach($files as $fn){
					$path=$dir."/".$fn;
					
					//Recursively add
					if(is_dir($path)){
						if($fn!="." && $fn!=".."){
							$flist=array_merge($flist,walkDir($path));
						}
					}else{
						//Add file
						$flist[]=$path;
					}
				}
				return $flist;
			}
		?>
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
					$correct_hash=mysqli_fetch_row(mysqli_query($link,"select * from admin where ID=1"));
					$correct_hash=$correct_hash[1];#stupid PHP versions<5.4 require this kind of bullshittery
					
					
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
					$win_last_updated=mysqli_fetch_row(mysqli_query($link,"SELECT WIN32_BINARY_LAST_UPDATED FROM admin WHERE ID=1"));
					$lin_last_updated=mysqli_fetch_row(mysqli_query($link,"SELECT LINUX_BINARY_LAST_UPDATED FROM admin WHERE ID=1"));
					$win_last_updated=$win_last_updated[0];#More ass-backwards compatibility
					$lin_last_updated=$lin_last_updated[0];
					
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
				
				//Logout
				if($_GET["op"]=="logout"){
					session_destroy();
					header("Location: login.php");
				}
				
				
				//Manage files
				if($_GET["op"]=="manfile"){
					//Valid source code extensions
					$source_extensions=array("php","html","htm","cgi","py","css","c","cpp","perl");
					
					//Write dropdown menu
					echo("<form method='POST'>\n");
					echo("<select name='file'>\n");
					if(array_key_exists("file",$_POST)){
						echo($_POST["file"]);
					}
					foreach(walkDir("..") as $file){
						if(in_array(pathinfo($file, PATHINFO_EXTENSION),$source_extensions)){
							echo("<option value='$file'");
							if(array_key_exists("file",$_POST) && !strcmp($file,$_POST["file"])){
								echo(' selected="selected"');
							}
							echo(">$file</option>\n");
						}
					}
					echo("</select>\n<input type='submit' value='Load'></form>");
					
					//Write Editor
					if(array_key_exists("file",$_POST)){
						$file_source=escapeText(file_get_contents($_POST["file"]),4);
						echo("<form method='POST' action='?op=save_source&fn=".$_POST["file"]."'>");
					}else{
						$file_source="";
					}
					echo("<textarea spellcheck='false' name='file_source' style='width: 600px; height: 260px;resize: none;'>$file_source</textarea>");
					echo("<input type='submit' value='Save'>");
					
					
					echo("</form>");
				}
				
				//Replace source with new text
				if($_GET["op"]=='save_source'){
					$fp=fopen($_GET["fn"],"w");
					$source=$_POST['file_source'];
					if($source[strlen($source)-1]!='\n'){
						$source=$source."\n";
					}
					fwrite($fp,$source);
					fclose($fp);
					header("Location: ?op=manfile");
				}
				
			}
		?>
		</div>
	</body>
</html>


