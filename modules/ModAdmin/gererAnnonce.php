<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="texteEnTete"> Changer le message qui se situe à la page d'accueil </h3>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>

<?php endif; ?>

<div id="changerAnnonce">

	<form method="post" action="index.php?module=ModAdmin&action=changerAnnonce">
	
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	
		<div> Message d'accueil </div>
		
		<textarea id="changerAnnonceTextArea" rows="10" cols="50" name="annonce"><?=$annonce->annonce?></textarea>
		
		<style>
			#changerAnnonceTextArea {
				color:<?=$annonce->color?>;
				background-color:<?=$annonce->bgColor?>;
			}
		</style>
		
		<div> Couleur </div>
		
		<input type="color" onchange="document.getElementById('changerAnnonceTextArea').style.color=this.value;" name="color" value="<?=$annonce->color?>"/>
		
		<div> Couleur de fond </div>

		<input type="color" onchange="document.getElementById('changerAnnonceTextArea').style.background=this.value;" name="bgColor" value="<?=$annonce->bgColor?>"/>
		
		<input class="boutonsForms" type="submit" value="Changer"/>
	
	</form>

</div>
