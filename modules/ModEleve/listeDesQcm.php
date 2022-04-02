<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

?>

<?php if (count($listeQcm)>0): ?>
<h3 class="textesEnTete"> Choisissez un QCM </h3>

	<div id="choisirUnQcm">
	
		<form method="POST" action="index.php?module=ModEleve&action=afficherReglementQcm">
		
			<select name="idQcm">
				<?php foreach ($listeQcm as $qcm): ?>
					<option value="<?=$qcm['idQcm']?>"> <?=$qcm['nomQcm']?> </option>
				<?php endforeach; ?>
			</select>
			
			<input class="boutonsForms" type="submit" value="Suivant"/>
		</form>
	
	</div>
<?php else: ?>
	<h3 class="textesEnTete"> Aucun QCM n'est disponible actuellement. </h3>
<?php endif; ?>
