<?php if(!defined('CONSTANTE'))
	die("Accès interdit");


?>

<h3 class="textesEnTete"> Gérer les QCM </h3>

<?php if (isset($msgApresUpdate)): ?>
	<?php if (!is_array($msgApresUpdate)): ?>
		<div style="color:white;"> <?=$msgApresUpdate?> </div>
	<?php else: ?>
		<div id="updateMsgErrors">
		<?php foreach ($msgApresUpdate as $msg): ?>
			<ul>
				<li style="color:white;"> <?=$msg?> </li>
			</ul>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<form style="margin-top:20" method="POST" action="index.php?module=ModMoniteur&action=updateQcm">

<table class="listeCompetencesOuQcmEleve">
	<tr class="tableTitres">
		<td class="tdAlternate2"> Nom </td>
		<td class="tdAlternate2"> Autorisation </td>
		<td class="tdAlternate2"> Voir </td>
		<td class="tdAlternate2"> Mettre à jour </td>
	</tr>
	
	
		<?php $i=-1; ?>
		
		<?php foreach ($listeQcm as $tuple):
			$i++;
			$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
			
		 	$checked = "";
		 	
			if ($tuple['autoriser']==1) {
				$checked = "checked";
			}
			
			$nomQcm = htmlspecialchars($tuple['nomQcm'], ENT_QUOTES);
			
		?>
			<tr>
				<td class="<?=$tdClass?>"> 
					<input class="inputText" type="text" name="nomQcm[<?=$tuple['idQcm']?>]" value="<?=$nomQcm?>"/>
				</td>
				
				<td class="<?=$tdClass?>">
					<input style="width:50;" type="checkbox" name="autoriserQcm[<?=$tuple['idQcm']?>]" <?=$checked?>/>
				</td>
				
				<td class="<?=$tdClass?>">
					<a style="padding:0; margin:0" href="index.php?module=ModMoniteur&action=voirQcm&idQcm=<?=$tuple['idQcm']?>"> Voir </a>
				</td>
				
				<td class="<?=$tdClass?>">
					<input style="width:50;" type="checkbox" name="ids[]" value="<?=$tuple['idQcm']?>"/>
				</td>
			</tr>
		<?php endforeach; ?>
		
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	
	
</table>
		<input class="boutonsForms" id="updateQcm" type="submit" name="updateQcm" value="Mettre à jour"/>
		<input class="boutonsForms" id="destroyQcm" type="submit" name="destroyQcm" value="Supprimer"/>

</form>
