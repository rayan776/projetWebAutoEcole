<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Les catégories d'articles </h3>

<?php if($peutGerer):
	echo "<h3 class='textesEnTete'> $msgRetour </h3>";
	$csrfToken=Tokens::insererTokenForm();
?>
	<form id="formCat" style="margin-bottom:50" method="POST" action="index.php?module=ModArticles&action=categories"> </form>
	<input form="formCat" type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
<?php endif; ?>

<table id="tableCat">

	<tr id="tableTitres">
		<td class="tdAlternate2";> Nom </td>
		<td class="tdAlternate2"> Nombre d'articles </td>
		<td class="tdAlternate2"> Voir liste articles </td>
		<?php if($peutGerer): ?>
		<td class="tdAlternate2"> Mettre à jour </td>
		<?php endif; ?>
	</tr>
	
	<?php $i=-1; ?>
	
	<?php foreach ($categories as $cat): 
		$i++;
		$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";	
	?>
		<tr>
			<td class="<?=$tdClass?>">
				<input form="formCat" type="text" class="inputText" name="categories[<?=$cat['idCat']?>]" value="<?=$cat['category']?>" <?=$readonly?>/>		
			</td>
			
			<td class="<?=$tdClass?>">
				<?=$cat['nbArticles']?>
			</td>
			
			<td class="<?=$tdClass?>">
				<form method="post" action="index.php?module=ModArticles&action=chercher"/>
					<input type="hidden" name="trierParCat" value="<?=$cat['idCat']?>"/>
					<input type="hidden" name="trierPar[]" value="cat"/>
					<input style="width:50" type="submit" class="boutonsForms" value="Liste"/>
				</form>
			</td>
			
			<?php if($peutGerer): ?>
				<td class="<?=$tdClass?>">
					<input form="formCat" class="checkboxesCat" style="margin:0; display:inline; width:50" type="checkbox" name="majCat[]" value="<?=$cat['idCat']?>"/>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>

</table>

<?php if($peutGerer): ?>

		<input form="formCat" style="margin:0; margin-bottom:15; margin-top:15" class="boutonsForms" type="submit" name="majCategories" value="Changer le nom des catégories cochées"/>
		<input form="formCat" style="margin:0" class="boutonsforms" type="submit" name="deleteCat" value="Supprimer les catégories cochées"/>
		
		<div style="margin-top:30">
			<button class="boutonsForms boutonCocher" type="button" onclick="toutCocher(1, 'checkboxesCat');"> Tout cocher </button>
			<button class="boutonsForms boutonCocher" type="button" onclick="toutCocher(0, 'checkboxesCat');"> Tout décocher </button>
		</div>
<?php endif; ?>


<?php if($peutGerer): ?>
	<h3 class="textesEnTete"> Ajouter une nouvelle catégorie </h3>
	
	<form id="addNewCat" method="POST" action="index.php?module=ModArticles&action=categories">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		
		<div style="color:white; position:relative; left:100; margin-bottom:10; font-weight:bold"> Nom (50 caractères maxi) </div>
		<input type="text" class="inputText" name="nomNewCat"/>
		
		<input style="margin-top:25; margin-bottom:20" class="boutonsForms" style="margin:0" type="submit" name="addCat" value="Ajouter catégorie"/>
	</form>
	
<?php endif; ?>
