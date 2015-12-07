<?php
	/* 
		This file contains documentation
		pages for OSIR.
	*/
	
	//Make top-of-the-tree Docpage object
	$home=new Docpage("Treetop","top");
	
	//Create branches (subpages)
	$home->subpages=[
		new Docpage("Introduction","OSIR is an open source project to allow cybersecurity students to study how ransomware and other malware is built.
		<br>OSIR is a cross-platform ransomware, divided into a client and a backend<br><br>
		<div style='text-align: left;'>
		<b>Goal:</b> Teaching cybersecurity students ransomware design and application through studying source code and contributing to the project, or deriving a new project, in a lab or classroom setting. The program will be available for public use on a git repository. The students will identify elements of the program, alter them in some way, and launch their own attacks to explore the effects on the host computer.<br><br>
		<b>Target Users:</b> Cybersecurity students studying malware that have prior coursework in programming (C and server-side programming)<br><br>
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
		</div>",[new Docpage("Dev Requirements","<div style='text-align: left;'>
	<ul style='text-align: left'>
		<li>
		<b>General Requirements:</b>
			<ul style='text-align: left'>
				<li>
					<blue>Essential Requirements</blue>
					<ul style='text-align: left'>
						<li>Open Source (under GPL)</li>
						<li>Well written, well documented, and understandable code</li>
						<li>No black box components (apart from dependencies)</li>
					</ul>
				</li>
				<li>
					<blue>Desirable Requirements</blue>
					<ul style='text-align: left'>
						<li>Portable code (C99) compilable by GCC/G++ and the Tiny C Compiler</li>
						<li>Easy to build</li>
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
						<li>Infects other files on, or connected to, the host system</li>
						<li>Carries a payload</li>
						<li>Can infect non-native executable(e.g ELF on Windows, or win32 PE on GNU/Linux) using an internet connection to download encrypted binaries</li>
					</ul>
				</li>
			</ul>
		</li><br>
		<li>
		<b>Payload Requirements</b>
			<ul style='text-align: left;'>
				<li><blue>Essential Requirements</blue>
					<ul style='text-align: left'>
						<li>Encrypts user’s files with a cryptographically secure pseudo-random 128-bit block cipher in CBC mode (algorithm TBD), to be encrypted by an RSA public key, such that files cannot be decrypted without the server’s (holding the RSA private key) permission</li>
						<li>When finished, redirects user to ransom note on server, requesting Bitcoin (BTC)</li>
						<li>When finished, creates a “return hostage” program on user’s computer, that when used with the right key (from the server) decrypts the user’s file</li>
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
</div>")]),
		new Docpage("Building","Building OSIR involves uploading the backend and compiling the client.",[
			new Docpage("Requirements","<div style='text-align: left'><b>Client</b><ul>
			<li><a href='https://github.com/Property404/OSIR'>OSIR source code</a></li>
			<li>TCC(Tiny C Compiler) <strong>or</strong> GCC(GNU Compiler Collection)</li>
			<li>GNU/Linux <strong>and</strong> 64-bit Microsoft Windows (or a cross compiler)</li>
			</ul>
			<b>Server</b>
			<ul>
			<li>OSIR source code</li>
			<li>Apache</li>
			<li>MySQL</li>
			<li>PHP (>=5.6)</li>
			<li>Python (>=3.4)</li>
			</ul>
			</div>"),
			new DocPage("Deploying Backend","Deploying the backend requires an *<a href='https://en.wikipedia.org/wiki/Solution_stack'>AMP</a> server. For testing purposes, you can use <a href='https://en.wikipedia.org/wiki/Xampp'>XAMPP</a> on <a href='https://en.wikipedia.org/wiki/Localhost'>localhost</a>.<br><br><div style='text-align: left;'
			><b>Steps:</b><ol>
			<li>Copy the <strong>contents</strong> of the osir/htdocs directory to the desired OSIR folder on the server.<br><br></li>
			<li>Navigate, on a browser, to your_ip/osir_folder/admin(<em>e.g. 127.0.0.1/osir/admin</em>)<br><br></li>
			<li>If you were using HTTP, you will be redirected to the HTTPS version of the site. If you do not have a valid certificate, your browser will warn you. For testing purposes, you can ignore this warning.<br><br></li>
			<li>Login using the default password: <em>hunter2</em><br><br></li>
			<li>Immediately change your password (<em>server</em>&gt;<em>change password</em>)<br><br></li>
			</ol>
			</div>"),
			new DocPage("Building Client","Building with GNU Compiler Collection is the easiest way to compile OSIR on Windows. In addition, GCC's code output is highly optimized when compared with TCC.<br><br>TCC's output code is generally a lot smaller than GCC's. For example, TCC will compile:<pre><code class=\"c\">#include &lt;stdio.h&gt;
int main(){printf(\"Hello World\");return 0;}</code></pre>into a 2KB file, while GCC will compile it into a 16KB file.
			<div style='text-align: left;'>
			<ol>
				<li>Edit osir/src/settings.h
					<ul>
						<li>Change SERVER_HOSTNAME to the remote ip address of the server</li>
						<li>Change any other literals as desired</li><br>
					</ul>
				</li>
				<li>Install GCC</li>
					<ul>
						<li>Debian/Ubuntu: run <code>sudo apt-get install gcc</code></li>
						<li>Fedora/RedHat: run <code>sudo dnf install gcc</code></li>
						<li>Windows: <a href='http://mingw.org/'>MinGW</a> and <a href='http://sourceforge.net/projects/mingw-w64/'>MinGW-w64</a> both have ports for GCC</li>
					</ul><br>
				</li>
				<li>Install TCC (Optional)
					<ol>
						<li>Download the <a href='bellard.org/tcc/'>TCC binary</a></li>
						<li>Windows:
							<ol>
								<li>Complicated things</li>
							</ol>
						</li>
					</ol><br>
				</li>
				<li>Run makefile</li>
				<ul>
					<li>Linux: null</li>
					<li>Windows: In osir/src, run <code>make.bat gcc</code> <strong>or</strong> <code>make.bat tcc</code></li>
				</ul>
			</ol>
				
			")
			]
		),
		new DocPage("Using"),
		new DocPage("Understanding")
	];
?>