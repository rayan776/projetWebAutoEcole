<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
     
?>

<?php if (count($listeComps)==1 && $listeComps[0]==-1): ?>
	<h3 class="textesEnTete"> Cet utilisateur n'existe pas. </h3>
<?php else: ?>

<h3 class="textesEnTete"> Compétences de l'élève <a class="lienVoirProfil" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$_GET['loginEleve']?>"/> <?=$_GET['loginEleve']?> </a> </h3>

<div class="listeCompetencesOuQcmEleve">
		
		<h3 class="textesEnTete"> Trier par... </h3>
		
		<form id="formTrierComps" method="POST" action="index.php?module=ModMoniteur&action=compEleve&loginEleve=<?=$_GET['loginEleve']?>">
			<div id="trierComps">
		
				<input type="hidden" name="loginEleve" value="<?=$_GET['loginEleve']?>"/>
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
						
						<option value="<?=$trierParMoniteur['login']?>"> <?=$trierParMoniteur['prenom']?> (<?=$trierParMoniteur['login']?>) </option>
						
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
		
		<form method="POST" action="index.php?module=ModMoniteur&action=updateComps&loginEleve=<?=$_GET['loginEleve']?>" id="formUpdateComps">
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			<input type="hidden" name="loginEleve" value="<?=$_GET['loginEleve']?>"/>
		
		<table>
			<tr class="tableTitres">
				<td class="tdAlternate2" id="tdComp"> Compétence </td>
				<td class="tdAlternate2"> Progression </td>
				<td class="tdAlternate2" id="titreRemarques"> Remarques </td>
				<td class="tdAlternate2"> Moniteur </td>
				<td class="tdAlternate2"> Dernière mise à jour </td>
				<td class="tdAlternate2"> Mettre à jour </td>
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
					<select class="selectEval selectEvalTable" id="eval<?=$tuple['idComp']?>" name="eval[<?=$tuple['idComp']?>]">
						<option value="<?=$tuple['eval']?>"> <?=$tuple['eval']?> </option>
						<?php foreach ($evalNom as $eval): ?>
						<?php	if ($tuple['eval'] != $eval): ?>
							<option value="<?=$eval?>"> <?=$eval?> </option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</td>
				
				<td class="<?=$tdClass?>">
					<input id="inputComp<?=$tuple['idComp']?>" class="inputText inputComp" name="remarques[<?=$tuple['idComp']?>]" type="text" value="<?php echo htmlspecialchars($tuple['remarques'], ENT_QUOTES); ?>"/>
				</td>
				
				<td class="<?=$tdClass?>">
					<a id="lienProfilMoniteurComps" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['prenom']?> </a>
				</td>
				
				<td class="<?=$tdClass?>">
					<?="Le " . Utilitaires::remplacerDate($tuple['lastUpdateDate'])?>
				</td>
				
				<td class="<?=$tdClass?>">
					<input class="checkboxIdComp" type="checkbox" name="ids[]" value="<?=$tuple['idComp']?>"/>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		
		<div class="fonctionsComps" id="boutonsCocher">
			<button type="button" class="boutonsForms" onclick="toutCocher(true, 'checkboxIdComp');"> Tout cocher </button>
			<button type="button" class="boutonsForms" onclick="toutCocher(false, 'checkboxIdComp');"> Tout décocher </button>
		</div>

		
		<div class="fonctionsComps" id="appliquerProgToutesComps">
			<select class="selectEval" id="evalMultiples">
				<?php foreach ($evalNom as $eval): ?>
					<option value="<?=$eval?>"> <?=$eval?> </option>
				<?php endforeach; ?>
			</select>
			
			<button type="button" class="boutonsForms" onclick="appliquerProgToutesLesComps();"> Appliquer cette progression à toutes les compétences cochées </button>
		</div>
	
		<div class="fonctionsComps" id="appliquerRemarqueToutesComps">
			<input type="text" class="inputText" id="remarqueToutesLesComps"/>
			<button type="button" class="boutonsForms" onclick="changerRemarquesComps();"> Appliquer cette remarque à toutes les compétences cochées </button>
			<button type="button" class="boutonsForms" onclick="resetRemarquesComps();"> Reset les remarques des compétences cochées </button>
		</div>	
			
		<input type="submit" class="boutonsForms" value="Mettre à jour"/>
	</form>
	
	<?php else: ?>
		<h3 class="textesEnTete"> La recherche n'a rien donné. </h3>
	<?php endif; ?>
</div>

<h3 id="updateCompsMsg" class="textesEnTete"> <?= $updateMsg ?> </h3>

<script> espaceMoniteurSelectChangeColorListener(); </script>

<?php endif; ?>

<a id="retourListeEleves" href="index.php?module=ModMoniteur&action=listeEleves"> Retour à la liste des élèves </a>

