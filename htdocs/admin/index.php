<!DOCTYPE HTML>
<!--This document is nearly the entire control panel.
Most OCP functionalities are implemented here-->
<html>
	<head>
		<title>OSIR Control Panel</title>
		<link rel="stylesheet" href="../style/main.css">
		<link rel="stylesheet" href="../style/menu.css">
	</head>
	<body>
		<?php
			include("menu.php");
		?>
		<div class="midcenter">
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

			
			//Check any messages
			if (array_key_exists("msg",$_GET))
				echo("<div class='alert'>".$_GET["msg"]."</div>\n");
			if (array_key_exists("note",$_GET))
				echo("<div>".$_GET["note"]."</div>\n");
			
			//See if on an option page
			if(array_key_exists("op",$_GET)){
				
				//Print help info
				if($_GET["op"]=="help"){		
					echo("Welcome to the OSIR Control Panel.<br>From here, you can update your OSIR binaries or manage your RSA keypairs.");
				}
				
				//Make manage keys form
				if($_GET["op"]=="mankey"){
					//Do the do's
					$link=Session::forceConnectDB();
				
					echo("<form method='POST' action='?op=updatekeys'>\n<select multiple='multiple' name='opt'>\n");
					//Show all RSA keys
					for($i=1;;$i++){
						$res=mysqli_fetch_row(mysqli_query($link,"SELECT * FROM RSAKEYS WHERE ID=".$i));
						if(empty($res)){break;}
						echo("<option>".$res[0].': '.$res[4]."-bit RSA</option>");
					}
					
					//Provide options as to what to do with those keys
					echo("</select><p>
					<input type='submit' value='Delete' name='rm'><br>
					<input type='submit' value='New Key' name='mk'>");
					echo("</select>\n</form>");
					
				}
				if($_GET["op"]=="updatekeys"){
					$link=Session::forceConnectDB();
					include("../mods/crypt.php");
					//Gen new key
					if(array_key_exists("mk",$_POST)){
						$keypair=Crypt::genKeyPair(2048);
						mysqli_query($link,"INSERT INTO RSAKEYS (PUBLIC,PRIVATE,SIZE) VALUES ('".$keypair["public"]."','".$keypair["private"]."',".$keypair["size"].")");
						header("Location: ?op=mankey");
					}
					
					
					//Delete old key
					if(array_key_exists("rm",$_POST)){
						echo("#");
							var_dump($_POST);
						for($i=1;;$i++){
							echo($i."<br>");
							$res=mysqli_fetch_row(mysqli_query($link,"SELECT * FROM RSAKEYS WHERE ID=".$i));
							if(empty($res)){echo("break;");break;}
echo(substr($_POST["opt"],0,strpos($_POST["opt"],':'))==$res[0]);
						if($_POST["opt"].substr(0,strpos($_POST["opt"],':'))==$res[0]){
								
								echo("!<br>");
								mysqli_query($link,"DELETE FROM RSAKEYS WHERE ID=".$res[0]);
								break;
							}
						}
						
						header("Location: ?op=mankey");
					}
				}
				//Make change Password form
				if($_GET["op"]=="changepwd"){
					echo('<form method="POST" action="index.php?op=chpwdact">
						Old Password:<br><input type="password" name="oldpwd" autofocus="autofocus"><p>
						New Password:<br><input type="password" name="newpwd"><p>
						Confirm New Password:<br><input type="password" name="newpwd2"><p>
						<input type="submit" value="Submit">
						</form>');
				}
				
				//Change password
				if($_GET["op"]=="chpwdact"){
					
					//Fetch correct hash
					$link=Session::forceConnectDB();
					$correct_hash=mysqli_fetch_row(mysqli_query($link,"select * from admin where ID=1"))[1];
					
					
					//Check if hashes match
					if(Security::makeSaltedHash($_POST["oldpwd"],$correct_hash)==$correct_hash){
						
						//Check if confirmation password matches
						if($_POST["newpwd"]==$_POST["newpwd2"]){
							
							//Check if new password is proper length
							if(strlen($_POST["newpwd"])>=Security::MIN_PASSWORD_LENGTH){
							
							//Set new hash
							mysqli_query($link,"UPDATE admin SET hash='".Security::makeSaltedHash($_POST["newpwd"]) . "' WHERE ID=1");
							$_SESSION["default_password"]="false";
							
							//Exit and inform the user on success
							header("Location: ?note=Password%20changed!");
							}else{
								
								//Password not long enough
								header("Location: ?op=changepwd&msg=Password%20too%20short");
							}
						}else{
							
							//Confirmation password doesn't match
							header("Location: ?op=changepwd&msg=Passwords%20don't%20match");
						}
					}else{
						
						//Wrong password
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
				
				//Logout
				if($_GET["op"]=="logout"){
					session_destroy();
					header("Location: login.php");
				}
				
				
				//Manage files
				if($_GET["op"]=="manfile"){
					//Valid source code extensions
					$source_extensions=array("html","htm","xml","php","js","cgi","py","perl","css","c","cpp","h","txt","bat","sh");
					
					//Write dropdown menu
					echo("<form method='POST'>\n");
					echo("<select name='file'>\n");
					
					//List options
					foreach(walkDir("..") as $file){
						if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)),$source_extensions)){
							echo("<option value='$file'");
							if(array_key_exists("file",$_POST) && !strcmp($file,$_POST["file"])){
								echo(' selected="selected"');
							}
							echo(">$file</option>\n");
						}
					}
					echo("</select>\n<input type='submit' value='Load'></form>");
					
					//Get file source code
					if(array_key_exists("file",$_POST)){
						$file_source=escapeText(file_get_contents($_POST["file"]),4);
						echo("<form method='POST' action='?op=save_source&fn=".$_POST["file"]."'>");
					}else{
						$file_source="";
						echo("<form>");
					}
					
					//Write Editor
					echo("<textarea spellcheck='false' name='file_source' style='width: 600px; height: 260px;resize: none;'>$file_source</textarea>");
					echo("<br><input type='submit' value='Save'>");
					echo("</form>");
					
					//Allow editor to indent with tab key
					echo("<script>
					document.getElementsByTagName('textarea')[0].onkeydown = function(e){
						if(e.keyCode==9 || e.which==9){
							e.preventDefault();
							var s = this.selectionStart;	this.value=this.value.substring(0,this.selectionStart) + \"\\t\" + this.value.substring(this.selectionEnd);
							this.selectionEnd = s+1; 
						}
					}
					\n</script>");
				}
				
				//Replace source with new text
				if($_GET["op"]=='save_source'){
					$fp=fopen($_GET["fn"],"w");
					$source=$_POST['file_source'];
					//Add EOF
					if($source[strlen($source)-1]!="\n"){
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
