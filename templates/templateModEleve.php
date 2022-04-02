<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

		<HEAD>
			<link href="stylesheets/espaceMoniteur.css" rel="stylesheet"/>
		</HEAD>
	

		<?php 
			echo $liensMenuPrincipal;
			
		?>
		
		<h3 class="textesEnTete"> Bienvenue dans l'espace élève, <?=$_SESSION['login']['login']?> </h3>
		
		<?php 
			echo $liensMenuSecondaire;
		
			echo $affichage; 
		?>
