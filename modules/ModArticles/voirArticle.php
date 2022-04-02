<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
	
?>

<div id="afficherArticle">
	<div style="font-size:30; text-align:center"> <?=$article->nomArt?> </div>
	
	<?php if ($lastModif): 
		if ($lastModif->idUser==Utilitaires::getIdFantome()) {
			$lastModifier="compte supprimé";
		}
		else {
			$lastModifier=$lastModif->login;
		}
	?>
		<div> Modifié par <?=$lastModifier?> le <?=Utilitaires::remplacerDate($lastModif->dateModif)?> </div>
	<?php endif; ?>
	
	<div id="gererArt" style="display:flex">
	<?php if (isset($_SESSION['login'])&&Utilitaires::estActive()&&$article->login!=$_SESSION['login']['login']): ?>
			<a href="index.php?module=ModArticles&action=signaler&idArt=<?=$_GET['idArt']?>"> <img class="imgResources" style="width:30; height:30" src="resources/flag.jpg" alt="Signaler"/> </a>
	<?php endif; ?>
		
	<?php if ($peutGerer): ?>
			<?php if ($peutSupprimer): ?>
				<form method="POST" action="index.php?module=ModArticles&action=deleteArticle">
					<input type="hidden" name="deleteArticle"/>
					<input type="hidden" name="idArt" value="<?=$_GET['idArt']?>"/>
					<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
					<input type="image" src="resources/bin.jpg" alt="Supprimer" width="30" height="30" style="position:relative; top:10; right:20; width:30; height:30">
				</form>
			<?php endif; ?>
			
			<?php if ($peutModifier): ?>
				<a href="index.php?module=ModArticles&action=formModifierArticle&idArt=<?=$_GET['idArt']?>"> <img class="imgResources" src="resources/crayon.jpg" style="width:30; height:30" alt="Modifier"/> </a>
			<?php endif; ?>
		
	<?php endif; ?>
	</div>
	
	<h4> Catégorie: <?=$article->category?> </h4>
	<h4 style="text-align:right; margin-right:100"> Par: <?php 
		if ($fantome==0): 
			if (isset($_SESSION['login'])): ?> 
				<a style="margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$article->login?>"> <?=$article->login?> </a> 
			<?php else: ?>
				<?=$article->login?>
			<?php endif; ?>
		<?php else: ?> 		
			Compte supprimé 
		<?php endif; ?> </h4>
	<h4> Posté le <?=Utilitaires::remplacerDate($article->datePub)?> </h4>
	
	<div id="corpsArticle">
		<?=$corpsArticle?>
	</div>
</div>

<?php if ($peutCommenter): ?>
<div id="insererCom">

	<?php if (!empty($msgRetourCom)): ?>
		<h3 class="textesEnTete"> <?=$msgRetourCom?> </h3>
	<?php endif; ?>

	<h3> Insérez un commentaire (250 caractères max) </h3>
	
	<form method="POST" action="index.php?module=ModArticles&action=voirArticle&idArt=<?=$_GET['idArt']?>">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		<input type="hidden" name="idArt" value="<?=$_GET['idArt']?>"/>
		
		<textarea rows="10" cols="83" name="comment" id="writeComment" wrap="hard"></textarea>
		
		<input type="submit" style="margin:0; margin-top:30; position:relative; left:175; margin-bottom:100" name="posterCom" value="Envoyer" class="boutonsForms"/>
	</form>
</div>
<?php else: ?>
	<h3 class="textesEnTete"> Vous n'êtes pas autorisé à commenter cet article. </h3>
<?php endif; ?>

<h3 class="textesEnTete" style="margin-top:10; margin-bottom:10"> Commentaires </h3>

<?php if (count($commentaires)>0): ?>
	<?php foreach ($commentaires as $comment): 
		$fantomeComment=0;
		if (Utilitaires::roleCommentaire($comment['idCom'])=="supprime")
			$fantomeComment=1;
	
		if (Utilitaires::getRoleCurrentUser()=="moniteur"&&Utilitaires::roleCommentaire($comment['idCom'])=="admin")
			$peutSupprimerCom=0;
	?>
		<div class="com" style="margin-bottom:15">
			<?php if ($fantomeComment==0): 
				if (isset($_SESSION['login'])):
			?>
				<a style="padding:0; margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$comment['login']?>"> <?=$comment['login']?> </a>
			  <?php else: ?>
				<?=$comment['login']?>
			  <?php endif; ?>
			<?php else: ?>
				Compte supprimé
			<?php endif; ?>
			
			<div style="text-align:right"> Le <?=Utilitaires::remplacerDate($comment['dateCom'])?> </div>
			
			<div class="corpsCom" style="margin-top:20"> 
				<?=$comment['contenu']?>
			</div>
			
			<div style="display:flex; margin-top:10">
				<?php if (isset($_SESSION['login'])&&Utilitaires::estActive()&&$comment['login']!=$_SESSION['login']['login']): ?>
					<a href="index.php?module=ModArticles&action=signaler&idCom=<?=$comment['idCom']?>">
						<img class="imgResources" style="width:30; height:30" src="resources/flag.jpg" alt="Signaler"/> 
					</a>
				<?php endif; ?>
				
				
				<?php if ($peutSupprimerCom): ?>
					<form method="POST" action="index.php?module=ModArticles&action=voirArticle&idArt=<?=$_GET['idArt']?>">
						<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
						<input type="hidden" name="deleteCom"/>
						<input type="hidden" name="idCom" value="<?=$comment['idCom']?>"/>
						<input type="image" src="resources/bin.jpg" alt="Supprimer" width="30" height="30" style="width:30; height:30; position:relative; top:10">
					</form>
				<?php endif; ?>
			
			</div>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<h3 class="textesEnTete"> Aucun commentaire n'a encore été posté pour cet article. </h3>
<?php endif; ?>
