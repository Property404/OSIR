<?php
	include("../mods/session.php");
	Session::checkSession();
?>
<link rel="stylesheet" href="../style/menu.css">
	<div class="menu">
		<ul>
			<li class="headerlink">OSIR &nbsp; &nbsp;
			<ul>
			<li><a href="?op=manbin">Manage Binaries</a></li><br>
			<li><a href="?op=mankey">Manage Keys</a></li><br>
			<li><a href="?op=wallet">Manage Wallet</a></li><br>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Account &nbsp; &nbsp;
			<ul>
			<li><a href="?op=changepwd">Change Password</a></li><br>
			<li><a href="logout.php">Logout</a></li><br>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Help &nbsp; &nbsp;
			<ul>
			<li><a href="https://github.com/Property404/OSIR">GitHub</a></li><br>
			<li><a href="?op=help">Help Page</a></li><br>
			</ul>
			</li>
		</ul>
	</div>
<?php
			if($_SESSION['default_password']=='true'){
				echo('<div class="hcentral"><div class="alert">You are using the default password. Please change it immediately.</div></div>');
			}else
			if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"]=='off'){
				echo('<div class="hcentral"><div class="alert">You are using HTTP. Please switch to HTTPS.</div></div>');
			}
?>