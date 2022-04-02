<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
     
?>

<?php if (count($listeQcm)==1&&$listeQcm[0]==-1): ?>
	<h3 class="textesEnTete"> Cet utilisateur n'existe pas. </h3>
<?php else: ?>

<h3 class="textesEnTete"> Les notes des QCM de l'élève <a href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$_GET['loginEleve']?>"> <?=$_GET['loginEleve']?> </a> </h3>

<div id="listeQcm" class="listeCompetencesOuQcmEleve">

	<?php if (count($listeQcm)>0): ?>
	
	<form method="POST" action="index.php?module=ModMoniteur&action=qcmEleve&loginEleve=<?=$_GET['loginEleve']?>">
		<h3 class="textesEnTete"> Trier par... </h3>
		
		<div id="trierDivSup">
		
			<div id="trierParNom">
				<input id="chkTriNom" type="checkbox" name="trierQcm[]" value="trierParNom" <?=$checkboxTrierQcmParNom?>/>
				<span id="spanQCM"> QCM </span>			
				<select id="trierParNomQcm" name="trierParNom">
					<?= $optionsNomQcm?>
				</select>
			</div>
			
			
			<div id="trierQcm">
				<input id="chkTriNote" type="checkbox" name="trierQcm[]" value="trierParNote" <?=$checkboxTrierQcmParNote?>/>
				<span id="spanNote"> Pourcentage de réussite </span>			
				<select id="trierParNoteQcm" name="trierParNote">
					<?php if ($trierQcmParNote == "ASC"): ?>
						<option value="ASC"> Du pire au meilleur </option>
						<option value="DESC"> De meilleur au pire </option>
					<?php else: ?>
						<option value="DESC"> De meilleur au pire </option>
						<option value="ASC"> Du pire au meilleur </option>					
					<?php endif; ?>
				</select>
				
				<div id="trierQcmNoteMaxMin">
					<span id="spanNoteMin"> Mini </span>
					<select class="selectNote" name="trierNoteMin" id="trierNoteMin">
						
						<?=$optionsNoteMin?>
					</select>
					
					<span id="spanNoteMax"> Maxi </span>
					<select class="selectNote" name="trierNoteMax" id="trierNoteMax">
						<?=$optionsNoteMax?>
					</select>
				</div>
				
				<div id="trierQcmDate">
					<div id="chkSpanDate">
						<input id="chkTriDate" type="checkbox" name="trierQcm[]" value="trierParDate" <?=$checkboxTrierQcmParDate?>/>
						<span id="spanDate"> Date </span>
					</div>
					
					<span id="spanDateDeb"> Du </span>
					<input id="inputDateDeb" type="date" name="trierQcmDateDeb" value="<?=$trierQcmDateDeb?>"/>
					
					<span id="spanDateFin"> Au </span>
					<input id="inputDateFin" type="date" name="trierQcmDateFin" value="<?=$trierQcmDateFin?>"/>
				</div>
				
				<input type="submit" name="boutonTrier" class="boutonsForms" value="Trier"/>
			</div>
		</div>
	
	</form>
	
		
	<table>
		<tr class="tableTitres">
			<td class="tdAlternate2"> Nom du QCM </td>
			<td class="tdAlternate2"> Date </td>
			<td class="tdAlternate2"> Note </td>
			<td class="tdAlternate2"> Pourcentage de réussite </td>
			<td class="tdAlternate2"> Résultats </td>
		</tr>

	<?php $i=-1; ?>
	
	<?php foreach ($listeQcm as $qcm):
	
		$i++;
		
		$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
		$nbQuestionsQcm = Utilitaires::getNbQuestionsParIdQcm($qcm['idQcm']);
		$percent = Utilitaires::arrondirPourcentage($qcm['pourcentageReussite']);
	?>
		<tr>
			<td class="<?=$tdClass?>"> <?=$qcm['nomQcm']?> </td>
			<td class="<?=$tdClass?>"> <?="Le " . Utilitaires::remplacerDate($qcm['dateTentative'])?> </td>
			<td class="<?=$tdClass?>"> <?=$qcm['note']?>/<?=$nbQuestionsQcm?></td>
			<td class="<?=$tdClass?>"> <?=$percent?> </td>
			<td class="<?=$tdClass?>">
				<a href ="index.php?module=ModMoniteur&action=resultatsQcm&idTentative=<?=$qcm['idTentative']?>"> Voir </a>
			</td>
		</tr>
	<?php endforeach; ?>
	
	</table>
	
	<?php else:
		if (isset($_POST['boutonTrier'])):
			$messageRecherche = "Désolé, aucun QCM correspondant à vos critères de recherche n'a été trouvé."; ?>  
			<h3 class="textesEnTete"> <?=$messageRecherche?> </h3>
			<a href="index.php?module=ModMoniteur&action=qcmEleve&loginEleve=<?=$_GET['loginEleve']?>"> Retour aux QCM de l'élève <?=$_GET['loginEleve']?> </a>
			<?php
		else:
			$messageRecherche = "Cet élève n'a pas encore passé de QCM."; ?>
			<h3 class="textesEnTete"> <?=$messageRecherche?> </h3> 
			<?php
		?>
		<?php endif; ?>
	<?php endif; ?>

</div>

<?php endif; ?>

<div id="retourListeEleves">
	<a id="retourListeEleves" href="index.php?module=ModMoniteur&action=listeEleves"> Retour à la liste des élèves </a>
</div>
