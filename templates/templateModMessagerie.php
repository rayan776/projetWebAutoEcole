<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

	<HEAD>
		<link href="stylesheets/msgstyle.css" rel="stylesheet"/>
	</HEAD>
	

		<?php		 
			echo $liensMenuPrincipal;
			
		?>
		
		<div class="textesEnTete"> <h3> Bienvenue sur la messagerie </h3> </div>
		
		<?php
			
			echo $liensMenuSecondaire;
		
			echo $affichage;
		?>

