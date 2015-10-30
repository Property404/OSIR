<?php
	//Return to login screen if not in session
	include("../mods/session.php");
	Session::checkSession();
	include("ver.php");
?>
	<div class="menu">
		<ul>
			<li class="headerlink">OSIR &nbsp; &nbsp;
			<ul>
			<li><a href="?op=manbin">Manage Binaries</a></li>
			<li><a href="?op=mankey">Manage Keys</a></li>
			<li><a href="?op=wallet">Manage Wallet</a></li>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Server &nbsp; &nbsp;
			<ul>
			<li><a href="?op=manfile">File Editor</a></li>
			<li><a href="?op=changepwd">Change Password</a></li>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Info &nbsp; &nbsp;
			<ul>
			<li><a href="?op=help">Help</a></li>
			<li><a href="https://github.com/Property404/OSIR">GitHub</a></li>
			</ul>
			</li>
		</ul>
		<ul>
			<li class="headerlink">Session &nbsp; &nbsp;
			<ul>
			<li><a href="?op=logout">Logout</a></li>
			</ul>
			</li>
		</ul>
		<div style="text-align: right;left-margin: 50px;line-height:1.5em;" class="headerlink">OSIR Control Panel <code>v.<?php echo(OCP_VERSION);?></code>&nbsp;&nbsp;</div>
	</div>
<?php
			if($_SESSION['default_password']=='true'){
				echo('<div style="background-color: red;text-align: center;color:white;">You are using the default password. Please change it immediately.</div>');
			}
?>