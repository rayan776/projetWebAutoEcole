<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> <?=$msgEnTete?> </h3>

<div id="signalement">

<?php if ($type=="comment"): ?>

	<h3 class="textesEnTete"> Vous avez choisi de signaler le commentaire suivant: </h3>
	
	<div class="com" style="margin-bottom:15">
		<?php if ($fantome==0): ?>
		<a style="padding:0; margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple->login?>"> <?=$tuple->login?> </a>
		<?php else: ?>
			Compte supprimé
		<?php endif; ?>
		<div style="text-align:right"> Le <?=Utilitaires::remplacerDate($tuple->dateCom)?> </div>
		
		<div class="corpsCom" style="margin-top:20"> 
			<?=$tuple->contenu?>
		</div>
	</div>
	
	<h3 class="textesEnTete"> Avant de valider votre signalement, veuillez expliquer brièvement pourquoi est-ce-que vous souhaitez signaler ce commentaire. </h3>
	
	<div id="warningSignalement"> Nous vous rappelons qu'en cas de signalements abusifs, vous pouvez être sanctionné par l'équipe d'administration. </div>
	
	<form style="margin-top:30" method="POST" action="index.php?module=ModArticles&action=transmettreSignalement">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		<input type="hidden" name="type" value="comment"/>
		<input type="hidden" name="idCom" value="<?=$tuple->idCom?>"/>
		
		<div style="color:white; margin-bottom:10"> Motif du signalement </div>		
		<textarea id="motif" name="motif" rows="10" cols="50"></textarea>
	
		<input type="submit" class="boutonsForms" style="margin:0; margin-top:20; position:relative; left:50" value="Signaler"/>
	</form>
<?php else: ?>
	<h3 class="textesEnTete"> Vous avez choisi de signaler l'article suivant: <a style="margin:0; padding:0" href="index.php?module=ModArticles&action=voirArticle&idArt=<?=$tuple->idArt?>"> <?=$tuple->nomArt?> </a> </h3>
	
	<h3 class="textesEnTete"> Avant de valider votre signalement, veuillez expliquer brièvement pourquoi est-ce-que vous souhaitez signaler cet article. </h3>
	
	<div id="warningSignalement"> Nous vous rappelons qu'en cas de signalements abusifs, vous pouvez être sanctionné par l'équipe d'administration. </div>
	
	<form style="margin-top:30" method="POST" action="index.php?module=ModArticles&action=transmettreSignalement">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		<input type="hidden" name="type" value="article"/>
		<input type="hidden" name="idArt" value="<?=$tuple->idArt?>"/>
		
		<div style="color:white; margin-bottom:10"> Motif du signalement </div>
		<textarea id="motif" name="motif" rows="10" cols="50"></textarea>
	
		<input type="submit" style="margin:0; margin-top:20; position:relative; left:50" class="boutonsForms" value="Signaler"/>
	</form>


<?php endif; ?>

</div>
