<!DOCTYPE HTML>
<html>
	<head><title></title>
	<link rel="stylesheet" href="style/main.css">
	</head>
	<body>
	<div class="hcentral">
	<?php
		//Custom error message to prevent information leaking
		$code=$_GET["c"];
		echo("<h1>Error - $code</h1>");
		switch ($code){
			//Client Side Errors
			case "400": echo("Bad Request");break;
			case "401": echo("Unauthorized");break;
			case "402": echo("Payment Required");break;
			case "403": echo("Forbidden");break;
			case "404": echo("Not found");break;
			case "405": echo("Method Not Allowed");break;
			case "406": echo("Not Acceptable");break;
			case "407": echo("Proxy Authentication Required");break;
			case "408": echo("Request Timeout");break;
			case "409": echo("Conflict");break;
			case "410": echo("Gone");break;
			case "411": echo("Length Required");break;
			case "412": echo("Precondition Failed");break;
			case "413": echo("Payload Too Large");break;
			case "414": echo("Request-URI Too Long");break;
			case "415": echo("Unsupported Media Type");break;
			case "416": echo("Requested Range Not Satisfiable");break;
			case "417": echo("Expectation Failed");break;
			case "421": echo("Misdirected Request");break;
			case "422": echo("Unprocessable Entity");break;
			
			//Server-side errors
			case "500": echo("Internal Server Error");break;
			case "501": echo("Not Implemented");break;
			case "502": echo("Bad Gateway");break;
			case "503": echo("Service Unavailable");break;
			case "504": echo("Gateway Timeout");break;
			case "505": echo("HTTP Version Not Supported");break;
			case "506": echo("Variant Also Negotiates");break;
			case "507": echo("Insufficient Storage");break;
			case "508": echo("Loop Detected");break;
			case "509": echo("Bandwidth Limit Exceeded");break;
			case "510": echo("Not extended");break;
			case "511": echo("Network Authentication Required");break;
			case "520": echo("Unknown Error");
			
			default: echo("Unlisted Error");
		}
	?>
	</div>
	</body>
</html>

