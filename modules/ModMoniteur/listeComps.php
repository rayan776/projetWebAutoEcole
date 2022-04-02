<?php if (!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Gérer les compétences </h3>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>

<div id="listeComps">

	<div> Les nouvelles compétences seront automatiquement attribuées à tous les élèves. Celles qui sont supprimées, seront retirées à tous les élèves. </div>

	<form method="POST" action="index.php?module=ModMoniteur&action=listeComps">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		

		<table>
		
			<tr style="font-weight:bold; text-align:center">
				<td class="tdAlternate2"> Nom </td>
				<td class="tdAlternate2"> </td>
			</tr>
		
			<?php $i=0; ?>
			
			<?php foreach ($listeComps as $comp): 
				$i++;
				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
			?>
			
				<tr>
					<td class="<?=$tdClass?>"> <input name="titre[<?=$comp['idComp']?>]" class="inputText" style="margin:0; padding:0" value="<?=$comp['titreCompetence']?>"/> </td>
					
					<td class="<?=$tdClass?>">
						<input style="margin:0; padding:0; width:50" class="chkListComp" type="checkbox" name="idComp[]" value="<?=$comp['idComp']?>"/>
					</td >
				</tr>
				
			
			<?php endforeach; ?>
		
		
		
		</table>
		
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(true, 'chkListComp');"> Tout cocher </button>
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(false, 'chkListComp');"> Tout décocher </button>

		<input class="boutonsForms" type="submit" name="majListComps" value="Mettre à jour"/>
		
		<input class="boutonsForms" type="submit" name="deleteListComps" value="Supprimer"/>

	</form>

</div>

<h3 class="textesEnTete"> Ajouter une nouvelle compétence </h3>

<form method="POST" action="index.php?module=ModMoniteur&action=listeComps">
	<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	
	<div id="addNewComp">
	
		<div style="display:flex">
			<div style="width:600"> Titre (60 caractères maxi) </div>
			<input class="inputText" type="text" name="nameComp"/>
		</div>
		
		<input class="boutonsForms" type="submit" name="addComp" value="Ajouter"/>
	
	</div>
	

</form>
