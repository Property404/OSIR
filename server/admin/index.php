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
			error_reporting(E_ALL);
			include_once("menu.php");
			include_once("../mods/keypair.php");
			include_once("../mods/security.php");
			include_once("../mods/eventlog.php");
		?>
		
		<div class="midcenter">
		<?php
			//Define functions here
			function escapeText($text)
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
					header("location: ../doc/index.php?page=0");
				}
				
				else if($_GET["op"]=="clearlog"){
					$link=Session::forceConnectDB();
					EventLog::clear($link);
					header("location: ./?op=log");
				}
				//Show event log
				else if($_GET["op"]=="log"){
					$link=Session::forceConnectDB();
					
					//Options
					echo("<table border='0'><tr><td style='text-align:left'><a href='./?op=log'>Refresh</a></td><td style='text-align:right;'><a href='./?op=clearlog'>Clear</a></td></tr></table>");
					
					//Print out table header
					echo("<table border='1' style='text-align: center'>");
					echo("<tr><td><strong>ID</strong></td><td><strong>Timestamp</strong></td><td><strong>IP</strong></td><td><strong>Type</strong></td><td><strong>Details</strong></td>"
					);
					
					//Loop through event entries
					for($i=1;;$i++){
						$k=EventLog::getEntry($link, $i);
						if($k==null)break;
						echo("<tr>");
						for($j=0; $j<5; $j++)
							echo("<td>".$k[$j]."</td>");
						echo("</tr>");
					}
					echo("</table>");
				}
				
				//Make manage keys form
				else if($_GET["op"]=="mankey"){
					//Connect to database
					$link=Session::forceConnectDB();
				
					//Show public key
					$current_id=0;
					if(array_key_exists("show",$_GET)){
						$current_id=$_GET["show"];
						$shownkey=(new KeyPair())->sqlImport($link,$current_id);
						echo($shownkey->toString()."<br><code>".str_replace("\n","<br>",$shownkey->getPublic())."</code>");
					}
				
					//Create manage key form
					echo("<form method='POST' action='?op=updatekeys'>\n<select name='opt'>\n");
					
					//Show all RSA keys
					for($i=1;$i<=KeyPair::getHighestID($link);$i++){
						$res=(new KeyPair())->sqlImport($link,$i);
						if($res->isValid())
							echo("<option".(($current_id==$i)?" selected=true":"").">".$res->toString()."</option>");
					}
					
					//Provide options as to what to do with those keys
					echo("</select><p>
					<input type='submit' value='Show' name='show_key'><br>
					<input type='submit' value='Delete' name='rm_key'><br>
					<input type='submit' value='New Key' name='mk_key'>");
					echo("</select>\n</form>");
					
				}
				else if($_GET["op"]=="updatekeys"){
					$link=Session::forceConnectDB();
					//Gen new key
					if(array_key_exists("mk_key",$_POST)){
						(new KeyPair())->generate()->sqlExport($link);
						header("Location: ?op=mankey");
					}
					
					//Show public key
					if(array_key_exists("show_key",$_POST)){
						echo($_POST["opt"]);
						for($i=1;$i<=KeyPair::getHighestID($link);$i++)
							if((new KeyPair())->sqlImport($link,$i)->toString()==$_POST["opt"])
								header("Location: ?op=mankey&show=".$i);
						
					}
					
					//Delete old key
					if(array_key_exists("rm_key",$_POST)){
						for($i=1;$i<KeyPair::getHighestID($link);$i++){
							$res=(new KeyPair())->sqlImport($link,$i);
							if($res->toString()==$_POST["opt"]){
								$res->destroy($link);
								header("Location: ?op=mankey");
							}
						}
						//This catches when the user tries to
						//Delete keys too fast
						header("Location: ?op=mankey");
					}
					
					die("Failed");
						
				}
				
				//Make change Password form
				else if($_GET["op"]=="changepwd"){
					echo('<form method="POST" action="index.php?op=chpwdact">
						Old Password:<br><input type="password" name="oldpwd" autofocus="autofocus"><p>
						New Password:<br><input type="password" name="newpwd"><p>
						Confirm New Password:<br><input type="password" name="newpwd2"><p>
						<input type="submit" value="Submit">
						</form>');
				}
				
				//Change password
				else if($_GET["op"]=="chpwdact"){
					
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
						if(!rand(0,10)){header("Location: ?op=logout");die("");}
						header("Location: ?op=changepwd&msg=Wrong%20password");
					}
				}
				
				//Make binary update form
				else if($_GET["op"]=="manbin"){
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
				else if($_GET['op']=="uploadbins"){
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
				else if($_GET["op"]=="logout"){
					unset($_SESSION);
					session_destroy();
					header("Location: login.php");
				}
				
				
				//Manage files
				else if($_GET["op"]=="manfile"){
					//Valid source code extensions
					$source_extensions=array("html","htm","xml","js","txt","css");
					
					//Write dropdown menu
					echo("<form method='POST'>\n");
					echo("<select name='file'>\n");
					
					//List all files as options
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
						$file_source=escapeText(file_get_contents($_POST["file"]));
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
				else if($_GET["op"]=='save_source'){
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
