<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../style/main.css">
		<link rel="stylesheet" href="../style/doc.css">
		<link rel="stylesheet" href="../style/highlight.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.8.0/highlight.min.js"></script>

		<title>OSIR Presentation</title>
	</head>

	<body>
		<script>
			//Image drawer function
			function drawImage(imageObj){
				var canvas = document.getElementById('cimg');
				var context = canvas.getContext('2d');
				var x = 69;
				var y = 50;
				context.drawImage(imageObj, x, y);
				var imageData = context.getImageData(x, y, imageObj.width, imageObj.height);
				var data = imageData.data;
				var max_to_trans=25;
				var min_to_white=50;
				for(var i = 0; i < data.length; i += 4) {
					// red
					data[i] = 255 - data[i];
					// green
					data[i + 1] = 255 - data[i + 1];
					// blue
					data[i + 2] = 255 - data[i + 2];
					
					if(data[i]<=max_to_trans)data[i]=53;
					if(data[i+1]<=max_to_trans)data[i+1]=53;
					if(data[i+2]<=max_to_trans)data[i+2]=68;
					
				}

				// overwrite original image
				context.putImageData(imageData, x, y);
			}
			
			//Draw image
			var imageObj = new Image();
				imageObj.onload = function() {
				drawImage(this);
			};
		</script>
		<?php
			//Strict error checking
			error_reporting(E_ALL);
			include("../mods/docpage.php");
			
			//Get page objects(within $home)
			include("slides.php");
			
			$current_page=$home;
			$doc=new Documentation($home,$_GET["page"]);
			$doc->listPages();
			$doc->displayPage();
			
		?>
		</div>
	</body>
</html>
