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
			<li class="headerlink">Server &nbsp; &nbsp;
			<ul>
			<li><a href="?op=manfile">File Editor</a></li>
			<li><a href="?op=changepwd">Change Password</a></li><br>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Info &nbsp; &nbsp;
			<ul>
			<li><a href="?op=help">Help</a></li><br>
			<li><a href="https://github.com/Property404/OSIR">GitHub</a></li><br>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Session &nbsp; &nbsp;
			<ul>
			<li><a href="?op=logout">Logout</a></li><br>
			</ul>
		</ul>
		<div style="text-align: right;left-margin: 50px;line-height:1.5em;" class="headerlink">OSIR Control Panel <code>v.p.0.1</code>&nbsp;&nbsp;</div>
	</div>
<?php
			if($_SESSION['default_password']=='true'){
				echo('<div style="background-color: red;text-align: center;color:white;">You are using the default password. Please change it immediately.</div>');
			}
?>