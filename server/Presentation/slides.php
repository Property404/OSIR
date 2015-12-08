<?php
	$home=new Docpage("Treetop","top");
	$home->subpages=[
		new Docpage("Recap", "<div style='text-align: left'>
		<ul>
			<li>Ransomware</li><br>
			<li>You know, for kids</li><br>
			<li>Cryptography</li><br>
			<li>Infection</li><br>
		</ul>
		</div>",[
			new DocPage("Overview","<div style='text-align: left'>
		<b>Acronym:</b>
		<b>O</b>pen
		<b>S</b>ource
		<b>I</b>nfectious
		<b>R</b>ansomware<br><br>
		<b>Language(client):</b>
		C<br><br>
		<b>Language(backend):</b>
		PHP(>=5.6), Python(>=3.4)<br><br>
		<b>Paradigm:</b> Procedural client-side coding, with server-side OOP<br><br>
		<b>Compiler:</b>
		<a href='http://bellard.org/tcc/'>Tiny C Compiler</a>, <a href='https://gcc.gnu.org/'>GCC</a><br><br>
		<b>Dependencies:</b> tiny aes, winapi<br><br>
		<b>License:</b> <a href='https://www.gnu.org/licenses/gpl-3.0.html'>GPLv3</a>
		</div>"),
			new DocPage("Ransomware","<div style='text-align: left;'>
			<ul>
			<li><strong>Definition:</strong> malware that restricts access to a system and demands that the user pay a ransom to the operators to remove the restriction.</li><br>
			<li><strong>Cryptoviral Extortion: </strong>encrypt files on system harddrive</li><br>
			<li><strong>Public Key Cryptography: </strong>
			Use of two asymmetric keys for encryption and decryption</li>
			</ul>
		</div>")]
		),
		new DocPage("Design", "<b>Client-Server</b><br><br>
		<strong>Design of the software architecture:</strong><br>
Our software follows a Client-Server design that permits mostly modular communication between the client and backend. The Client-Server architecture logically followed from our ransomware project concept which entails a host download of the client, with server communication for secure information sending/reception.<br><br>

<strong>Dependencies view:</strong><br>
Dependencies include third party C libraries (client side), MinGW headers (for TCC), and OpenSSL"),
		new Docpage("Requirements","<div style='text-align: left;'>
	<ul style='text-align: left'>
		<li>
		<b>General Requirements:</b>
			<ul style='text-align: left'>
				<li>
					<blue>Essential Requirements</blue>
					<ul style='text-align: left'>
						<li>Open Source (under GPL)<b>Check</b></li>
						<li>Well written, well documented, and understandable code<b>Check (up for debate)</b></li>
						<li>No black box components (apart from dependencies)<b>Check</b></li>
					</ul>
				</li>
				<li>
					<blue>Desirable Requirements</blue>
					<ul style='text-align: left'>
						<li>Portable code (C99) compilable by GCC/G++ and the Tiny C Compiler<b>Partial</b></li>
						<li>Easy to build<b>Check</b></li>
						<li>Low detection rate by antivirus</li>
					</ul>
				</li>
			</ul>
		</li>
		
		<br>
		<li>
		<b>Infection Component Requirements</b>
			<ul style='text-align: left'>
				<li><blue>Essential Requirements</blue>
					<ul style='text-align: left'>
						<li>Infects other files on, or connected to, the host system<b>Check</b></li>
						<li>Carries a payload<b>Check</b></li>
						<li>Can infect non-native executable(e.g ELF on Windows, or win32 PE on GNU/Linux) using an internet connection to download encrypted binaries<b>Check</b></li>
					</ul>
				</li>
			</ul>
		</li><br>
		<li>
		<b>Payload Requirements</b>
			<ul style='text-align: left;'>
				<li><blue>Essential Requirements</blue>
					<ul style='text-align: left'>
						<li>Encrypts user’s files with a cryptographically secure pseudo-random 128-bit block cipher in CBC mode (algorithm TBD), to be encrypted by an RSA public key, such that files cannot be decrypted without the server’s (holding the RSA private key) permission<b>Check</b></li>
						<li>When finished, redirects user to ransom note on server, requesting Bitcoin (BTC)</li>
						<li>When finished, creates a “return hostage” program on user’s computer, that when used with the right key (from the server) decrypts the user’s file<b>Check</b></li>
					</ul>
				</li>
				<li><blue>Optional Requirements</blue>
					<ul style='text-align: left'>
						<li>Steals password from browsers</li>
						<li>Uses social media as infection medium</li>
					</ul>
			</ul>
		</li>
	</ul>
</div>"),
		new Docpage("Diagrams","Big Diagrams<br>Small Diagrams<br>Vegan Diagrams",[
			new Docpage("Class Diagram","<canvas id='cimg' width='1000' height='1000' style=''></canvas><script>imageObj.src ='images/class_diagram.png';</script>"),
			new Docpage("Execution View","<canvas id='cimg' width='1000' height='1000' style=''></canvas><script>imageObj.src ='images/execution_view_client.png';</script>",[
				new DocPage("Server Side","<canvas id='cimg' width='1000' height='1000' style=''></canvas><script>imageObj.src ='images/execution_view_server.png';</script>")
			]
			),
			new DocPage("Event Trace","<canvas id='cimg' width='1500' height='1000' style=''></canvas><script>imageObj.src ='images/event_trace_diagram.png';</script>")
		]),
		new DocPage("Testing Results", "<text style='text-align: left'><ul><li>Client</li><br><li>Server</li><br></text>", [
			new DocPage("Unit","<pre style='text-align: left'>
Testing clonelib.h
getOwnBytes:	OK(35328 bytes)
Writing bytes
Setting modified date
infectTarget:	OK

Testing b64.h
b64decode:	OK(OK)
b16en/decode:	Failed(We dont need no education!=We dont need no educationE)

Testing crypt.h
symEncrypt:	OK
symDecrypt:	OK

Testing ransomlib.h
partialEncryptFile:	OK
partialEncryptFile-d:	OK

Testings paths.h
getExternalPaths: OK
Connection Status(wikipedia):	OK
Connection Status(OSIR):	OK

Server Unit Test Results:
Weakcrypt::en/decrypt:		OK
Security::makeSaltedHash:	OK
			</pre>")
		]),
		new DocPage("Stats","<b>Stats from GitHub</b><br>
		 PHP 40.9%<br>C 39.5%<br>CSS 10.1%<br>Batchfile 2.7%<br>C++ 2.4%<br>HTML 2.2%<br>Other 2.2%<br><br><b>Size</b><br>Client Source Code: 75KB\n<br>Server Source Code: 60KB<br><br><b>Tree</b><br><pre style='text-align:left;'>OSIR-MASTER
|   .gitignore
|   LICENSE
|   
+---client
|   |   make.bat
|   |   osir.c
|   |   
|   \---common
|       |   clonelib.c
|       |   clonelib.h
|       |   common.h
|       |   crypt.c
|       |   crypt.h
|       |   os.c
|       |   os.h
|       |   paths.c
|       |   paths.h
|       |   ransomlib.c
|       |   ransomlib.h
|       |   settings.h
|       |   unit_backup.c
|       |   weakcrypt.c
|       |   weakcrypt.h
|       |   web.c
|       |   web.h
|       |   
|       \---thirdparty
|               aes.c
|               aes.h
|               b64.c
|               b64.h
|               dirent.h
|               
+---server
|   |   decrypt.php
|   |   error.php
|   |   index.html
|   |   note.php
|   |   pay.php
|   |   
|   +---admin
|   |       check.php
|   |       index.php
|   |       login.php
|   |       menu.php
|   |       update_encrypted_binaries.cgi
|   |       ver.php
|   |       xcrypt.py
|   |       
|   +---binaries
|   |       index.php
|   |       
|   +---client-interface
|   |       echo.php
|   |       encrypt.php
|   |       test.php
|   |       
|   +---doc
|   |       index.php
|   |       pages.php
|   |       
|   +---images
|   |       lock.png
|   |       
|   +---licenses
|   |       highlightjs.txt
|   |       lock.txt
|   |       
|   +---mods
|   |   |   docpage.php
|   |   |   eventlog.php
|   |   |   index.php
|   |   |   keypair.php
|   |   |   python bitcoin
|   |   |   security.php
|   |   |   session.php
|   |   |   weakcrypt.php
|   |   |   
|   |   \---licenses
|   |           highlightjs.txt
|   |           lock.txt
|   |           
|   +---style
|   |       highlight.css
|   |       index.php
|   |       main.css
|   |       menu.css
|   |       
|   \---xbinaries
|           index.php
|           
\---tests
    |   build.bat
    |   test.bat
    |   
    +---playground
    |       dummy.txt
    |       
    \---source
            client_unit.c
            hello.c
            infection_system.c
            server_unit.c
            </pre>"),
		new DocPage("Guide", "<text style='text-align: left'><ul><li><a href='../admin'>OSIR Control Panel</a></li><br><li>Documentation</li><br><li>Demo</li></text>")
	];
	function slide($title,$body){
		return "<h1>$title</h1>\n<p>$body</p>";
	}
	function escapeText($text)
	{
				$text=str_replace("&","&amp;",$text);
				$text=str_replace("<","&lt;",$text);
				return $text;
	}
	
		
		
	
?>