<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../style/main.css">
		<link rel="stylesheet" href="../style/doc.css">
		<link rel="stylesheet" href="../style/highlight.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.8.0/highlight.min.js"></script>

		<title>OSIR Documentation</title>
	</head>

	<body>
		<script>

	</script>

		<?php
			//Strict error checking
			error_reporting(E_ALL);

			include("../mods/docpage.php");
			//Get page objects(within $home) from pages.php
			include("pages.php");
			$current_page=$home;
			$doc=new Documentation($home,$_GET["page"]);
			$doc->listPages();
			$doc->displayPage();
			
		?>
		</div>
	</body>
</html>