<?php if (!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Gérer les formules </h3>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>

<div id="listeFormules">

	<div> Gérez les formules du site affichées en page d'accueil. </div>

	<form method="POST" action="index.php?module=ModAdmin&action=gererFormules">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		

		<table>
		
			<tr style="font-weight:bold; text-align:center">
				<td class="tdAlternate2"> Nom </td>
				<td class="tdAlternate2"> Description </td>
				<td class="tdAlternate2"> </td>
			</tr>
		
			<?php $i=-1; ?>
			
			<?php foreach ($formules as $tuple):
				$i++;

				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
				
			?>
			
				<tr>
					<td class="<?=$tdClass?>"> <input name="titre[<?=$tuple['idFormule']?>]" class="inputText" style="margin:0; padding:0" value="<?=$tuple['titre']?>"/> </td>
					
					<td class="<?=$tdClass?>"> <input name="desc[<?=$tuple['idFormule']?>]" class="inputText" style="margin:0; padding:0" value="<?=$tuple['description']?>"/> </td>
					
					<td class="<?=$tdClass?>">
						<input style="margin:0; padding:0; width:50" class="chkListForm" type="checkbox" name="idFormule[]" value="<?=$tuple['idFormule']?>"/>
					</td>
				</tr>
				
			
			<?php endforeach; ?>
		
		
		
		</table>
		
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(true, 'chkListForm');"> Tout cocher </button>
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(false, 'chkListForm');"> Tout décocher </button>

		<input class="boutonsForms" type="submit" name="updateFormule" value="Mettre à jour"/>
		
		<input class="boutonsForms" type="submit" name="deleteFormule" value="Supprimer"/>

	</form>

</div>

<h3 class="textesEnTete"> Ajouter une nouvelle formule </h3>

<form method="POST" action="index.php?module=ModAdmin&action=gererFormules">
	<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	<div id="addNewFormule">
	
		<div style="display:flex">
			<div style="width:600"> Titre (30 caractères maxi) </div>
			<input class="inputText" type="text" name="titreFormule"/>
		</div>
		
		<div style="display:flex">
			<div style="width:600"> Description (50 caractères maxi) </div>
			<input class="inputText" type="text" name="descFormule"/>
		</div>
		
		<input class="boutonsForms" type="submit" name="addFormule" value="Ajouter"/>
	
	</div>
	

</form>
