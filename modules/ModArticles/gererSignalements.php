<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Gestion des signalements </h3>

<div style="color:white"> Ici, retrouvez la liste des signalements concernant les articles et les commentaires. Attention, vous ne pouvez pas voir les signalements qui portent sur des articles/commentaires que vous n'avez pas le droit de voir. Vous ne pouvez pas supprimer un article si vous n'avez pas le droit de suppression dessus. Un moniteur ne peut pas supprimer les commentaires de l'administrateur. </div>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>

<h3 style="margin-top:50" class="textesEnTete"> Articles signalés </h3>

<?php if (count($signalements[0])==0): ?>

	<div style="color:white"> Aucun article n'a encore été signalé. </div>

<?php else: ?>

	<div class="divGererSignalement">
		<form method="POST" action="index.php?module=ModArticles&action=supprimerSignalements">
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			<input type="hidden" name="type" value="article"/>
			<table id="tableSignalements">
			
				<tr class="trTitres">
					<td class="tdAlternate2"> Auteur du signalement </td>
					<td class="tdAlternate2"> Motif </td>
					<td class="tdAlternate2"> Article signalé </td>
					<td class="tdAlternate2"> </td>
				</tr>
				<?php $i=-1; ?>
				
				<?php foreach($signalements[0] as $signalement): 
					
					$i++;
					
					if ($i%2==0)
						$tdClass="tdAlternate1";
					else
						$tdClass="tdAlternate2";
				?>
					<tr>
						<td class="<?=$tdClass?>"> <a href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$signalement['login']?>"> <?=$signalement['login']?> </a> </td>
						<td class="<?=$tdClass?>"> <?=$signalement['motif']?> </td>
						<td class="<?=$tdClass?>"> <a href="index.php?module=ModArticles&action=voirArticle&idArt=<?=$signalement['idArt']?>"> <?=$signalement['nomArt']?> </a> </td>
						<td class="<?=$tdClass?>">
							<input type="checkbox" class="chkSignArt" name="idsSignalement[]" value="<?=$signalement['idSignal']?>"/>
						</td>
					</tr>
				<?php endforeach; ?>
			
			</table>
			
			<button class="boutonsForms boutonsSecondaires boutonsToutCocherSignaler" type="button" onclick="toutCocher(true, 'chkSignArt');"> Tout cocher </button>
			<button class="boutonsForms boutonsSecondaires boutonsToutCocherSignaler" type="button" onclick="toutCocher(false, 'chkSignArt');"> Tout décocher </button>	
			
			<div style="display:flex; margin-top:30">
				<input  style="margin:0" class="boutonsForms" type="submit" name="deleteSignOnly" value="Supprimer les signalements cochés"/>
				<input style="margin:0; width:450; margin-left:50" class="boutonsForms" name="deleteAll" type="submit" value="Supprimer les articles associés aux signalements cochés"/>
			</div>
		</form>
		</div>

<?php endif; ?>

<h3 style="margin-top:50px" class="textesEnTete"> Commentaires signalés </h3>

<?php if (count($signalements[1])==0): ?>

	<div style="color:white"> Aucun commentaire n'a encore été signalé. </div>

<?php else: ?>

	<div class="divGererSignalement">
		<form method="POST" action="index.php?module=ModArticles&action=supprimerSignalements">
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			<input type="hidden" name="type" value="comment"/>		
			<table id="tableSignalements">
			
				<tr class="trTitres">
					<td class="tdAlternate2"> Auteur du signalement </td>
					<td class="tdAlternate2"> Motif </td>
					<td class="tdAlternate2"> Commentaire signalé </td>
					<td class="tdAlternate2"> Article concerné </td>
					<td class="tdAlternate2"> </td>
				</tr>
				<?php $i=-1; ?>
				<?php foreach($signalements[1] as $signalement): 
					$i++;
					$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";					
						
					$idCom=$signalement['idCom'];
					$fantome=(Utilitaires::roleCommentaire($signalement['idCom'])=="supprime")?1:0;
					$posteurCom=Utilitaires::getLoginById($signalement['idPosteur']);
				?>
				
					<div class="commentaireCache" id="com<?=$idCom?>">
						<?php if ($fantome==0): ?>
						<a style="padding:0; margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$posteurCom?>"> <?=$posteurCom?> </a>
						<?php else: ?>
							Compte supprimé
						<?php endif; ?>
							<div style="text-align:right"> Le <?=Utilitaires::remplacerDate($signalement['dateCom'])?> </div>
							
							<div class="corpsCom" style="margin-top:20"> 
								<?=$signalement['contenu']?>
							</div>
							
							<button class="boutonsSecondaires boutonsForms boutonCacher" type="button" class="boutonsForms" onclick="voirCom('com<?=$idCom?>', true)"> Cacher </button>
					</div>
				
					<tr>
						<td class="<?=$tdClass?>"> <a href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$signalement['login']?>"> <?=$signalement['login']?> </a> </td>
						<td class="<?=$tdClass?>"> <?=$signalement['motif']?> </td>
						<td class="<?=$tdClass?>"> <button type="button" class="boutonsForms boutonsSecondaires" onclick="voirCom('com<?=$idCom?>', false);"> Voir commentaire </button> </td>
						<td class="<?=$tdClass?>"> <a href="index.php?module=ModArticles&action=voirArticle&idArt=<?=$signalement['idArt']?>"> <?=$signalement['nomArt']?> </a> </td>
						<td class="<?=$tdClass?>">
							<input class="chkSignCom" type="checkbox" name="idsSignalement[]" value="<?=$signalement['idSignal']?>"/>
						</td>
					</tr>
				<?php endforeach; ?>
			
			</table>
			
			<button class="boutonsForms boutonsSecondaires boutonsToutCocherSignaler" type="button" onclick="toutCocher(true, 'chkSignCom');"> Tout cocher </button>
			<button class="boutonsForms boutonsSecondaires boutonsToutCocherSignaler" type="button" onclick="toutCocher(false, 'chkSignCom');"> Tout décocher </button>		
			
			<div style="display:flex; margin-top:30">
				<input style="margin:0;" class="boutonsForms" name="deleteSignOnly" type="submit" value="Supprimer les signalements cochés"/>
				<input style="margin:0; width:450; margin-left:50" class="boutonsForms" name="deleteAll" type="submit" value="Supprimer les commentaires associés aux signalements cochés"/>
			</div>
		</form>
	</div>

<?php endif; ?>
