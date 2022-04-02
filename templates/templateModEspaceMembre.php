<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

?>

	<HEAD>
		<link href="stylesheets/espaceMembre.css" rel="stylesheet"/>
	</HEAD>

	<?php 
			echo $liensMenuPrincipal;
			
			?> <h3 class="textesEnTete"> Bienvenue dans l'espace membre </h3> <?php
			
			echo $liensMenuSecondaire;

		
			echo $affichage;
	?>
