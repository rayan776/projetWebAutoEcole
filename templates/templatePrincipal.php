<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<HTML>
	<HEAD>
		<meta charset="utf-8"/>
		<link rel="shortcut icon" href="#">
		<title> <?= $titrePage ?> </title>
		<link href="stylesheets/main.css" rel="stylesheet"/>
		<link href="stylesheets/wbbtheme.css" rel="stylesheet"/>
		<script src="scripts/scripts.js"> </script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="scripts/jquery.wysibb.min.js"> </script>
		<script>
			$(document).ready(function() {
			var wbbOpt = {
			  buttons: "bold,italic,underline,|,fontsize,fontcolor,|,justifyleft,justifycenter,justifyright"
			}
			$("#editor").wysibb(wbbOpt);
			});
		</script>
		
	</HEAD>
	
	<BODY>
		<?php
			echo $header;
			
			echo $msgDesac;
			
			echo $compActu;
		 
			require_once $templateSecondaire;			
			
			echo $footer;
		?>
	</BODY>
	
</HTML>
