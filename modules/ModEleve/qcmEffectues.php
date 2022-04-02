<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
     
?>

<h3 class="textesEnTete"> Consultez vos QCM </h3>

<div id="listeQcm" class="listeCompetencesOuQcmEleve">

	<?php if (count($listeQcm)>0): ?>
	
	<form method="POST" action="index.php?module=ModEleve&action=voirQcmEffectues">
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
				<a href ="index.php?module=ModEleve&action=resultatsQcm&idTentative=<?=$qcm['idTentative']?>"> Voir </a>
			</td>
		</tr>
	<?php endforeach; ?>
	
	</table>
	
	<?php else:
		if (isset($_POST['boutonTrier'])):
			$messageRecherche = "Désolé, aucun QCM correspondant à vos critères de recherche n'a été trouvé."; ?>  
			<h3 class="textesEnTete"> <?=$messageRecherche?> </h3>
			<a href="index.php?module=ModEleve&action=voirQcmEffectues"> Retour à la liste </a>
		<?php else: ?>
			<h3 class="textesEnTete"> Vous n'avez pas encore passé de QCM. </h3>
		<?php endif; ?>
	<?php endif; ?>

</div>
