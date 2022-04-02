<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
     
?>

<h3 class="textesEnTete"> Vos compétences </h3>

<div class="listeCompetencesOuQcmEleve">
		
		<h3 class="textesEnTete"> Trier par... </h3>
		
		<form id="formTrierComps" method="POST" action="index.php?module=ModEleve&action=gererCompetences">
			<div id="trierComps">
		
				<input id="checkboxTrierParProg" type="checkbox" name="trierPar[]" value="trierParProg" <?=$checkboxTrierProg?> />
				<span id="spanProg"> Progression </span>
				<select class="selectEval" id="trierParProg" name="trierParProg">
					<?php if (!empty($trierParProg)): ?>
					<option value="<?=$trierParProg?>"> <?=$trierParProg?> </option>
					<?php foreach ($evalNom as $eval): ?>
						<?php if ($trierParProg != $eval): ?>
							<option value="<?=$eval?>"> <?=$eval?> </option>
						<?php endif; ?>
					<?php endforeach; ?>						
					<?php else: ?>
						<?php foreach ($evalNom as $eval): ?>
						<option value="<?=$eval?>"> <?=$eval?> </option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				
				<input id="checkboxTrierParMoniteur" type="checkbox" name="trierPar[]" value="trierParMoniteur" <?=$checkboxTrierMoniteur?>/>			
				<span id="spanMoniteur"> Moniteur </span>
				<select name="trierParMoniteur" id="trierParMoniteur">
					<?php
						$listePrenomsMoniteurs = Utilitaires::getMoniteursParPrenom();
						
						if (isset($trierParMoniteur)&&is_array($trierParMoniteur)): ?>
						
						<option value=<?=$trierParMoniteur['login']?>> <?=$trierParMoniteur['prenom']?> (<?=$trierParMoniteur['login']?>) </option>
						
						<?php
						
							foreach ($listePrenomsMoniteurs as $moniteur):
							if ($moniteur['login']!=$trierParMoniteur['login']):
					?>
						<option value="<?=$moniteur['login']?>"> <?=$moniteur['prenom']?> (<?=$moniteur['login']?>) </option>
							<?php endif; ?>
							<?php endforeach; ?>
						<?php else:
							foreach ($listePrenomsMoniteurs as $moniteur):
					?>
						<option value="<?=$moniteur['login']?>"> <?=$moniteur['prenom']?> (<?=$moniteur['login']?>) </option>
							<?php endforeach; ?>
						<?php endif; ?>
				</select>
				
				<input ID="checkboxTrierParMaj" type="checkbox" name="trierPar[]" value="trierParMaj" <?=$checkboxTrierMaj?>/>
				<span id="spanMaj"> Dernière mise à jour </span>
				<select name="trierParMaj" id="trierParMaj">
					<?php if ($trierMaj == "asc"): ?>
						<option value="asc"> Moins récente vers la plus récente </option>
						<option value="desc"> Plus récente vers la moins récente </option>						
					<?php else: ?>
						<option value="desc"> Plus récente vers la moins récente </option>
						<option value="asc"> Moins récente vers la plus récente </option>
					<?php endif; ?>
				</select>
				
				<input id="submitTrierComp" class="boutonsForms" type="submit" value="Trier"/>
			</div>
		</form>
		
		<?php if (count($listeComps)>0): ?>
		
		
		<table>
			<tr class="tableTitres">
				<td class="tdAlternate2" id="tdComp"> Compétence </td>
				<td class="tdAlternate2"> Progression </td>
				<td class="tdAlternate2" id="titreRemarques"> Remarques </td>
				<td class="tdAlternate2"> Moniteur </td>
				<td class="tdAlternate2"> Dernière mise à jour </td>
			</tr>
			
		<?php $i=-1; ?>
		
		<?php foreach ($listeComps as $tuple):
			$i++;
			$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
		?>
			<tr>
				<td class="<?=$tdClass?>">
					<label for="eval<?=$tuple['idComp']?>"> <?=$tuple['titreCompetence']?> </label>
				</td>
				
				<td class="<?=$tdClass?>">
					<select class="selectEval selectEvalTable" id="eval<?=$tuple['idComp']?>">
						<option value="<?=$tuple['eval']?>"> <?=$tuple['eval']?> </option>
					</select>
				</td>
				
				<td class="<?=$tdClass?>">
					<input id="inputComp<?=$tuple['idComp']?>" class="inputText inputComp" name="remarques[<?=$tuple['idComp']?>]" type="text" value="<?php echo htmlspecialchars($tuple['remarques'], ENT_QUOTES); ?>" readonly="readonly"/>
				</td>
				
				<td class="<?=$tdClass?>">
					<a id="lienProfilMoniteurComps" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['prenom']?> </a>
				</td>
				
				<td class="<?=$tdClass?>">
					<?="Le " . Utilitaires::remplacerDate($tuple['lastUpdateDate'])?>
				</td>
			
			</tr>
		<?php endforeach; ?>
		</table>
		
		
	</form>
	
	<?php else: ?>
		<h3 class="textesEnTete"> La recherche n'a rien donné. </h3>
	<?php endif; ?>
</div>

<script> espaceMoniteurSelectChangeColorListener(); </script>

