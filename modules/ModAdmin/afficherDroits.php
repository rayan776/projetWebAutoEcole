<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<?php if (count($listeDroitsOuLimites)==1 && ($listeDroitsOuLimites[0]==-1)): ?>
	<h3> Cet utilisateur n'existe pas. </h3>
<?php else:
	$csrfToken = Tokens::insererTokenForm();

?>

	<div class="affichageEtModifierDroits">
		<h4 class="affichageEtModifierDroitsH4"> Liste des <?=$droitsOuExceptions?> <?=$roleOuUser?> : <?=$nomRoleOuUser?> </h4>
		<ul>
			<?php if (count($listeDroitsOuLimites)==0): ?>
				<?php if ($_POST['droitsDeQui']=="afficherParUser"): ?>
					<li> Aucune autorisation/interdiction exceptionnelle n'existe pour cet utilisateur. </li>
				<?php elseif ($_POST['droitsDeQui']=="afficherParRole"): ?>
					<li> Aucun droit n'existe pour ce rôle. </li>
				<?php endif; ?>
			<?php else: ?>
				<?php if ($_POST['droitsDeQui']=="afficherParUser"): ?>
					<?php foreach ($listeDroitsOuLimites as $droit):
						$style = ($droit['val']=="Interdiction") ? "color:red" : "color:lightgreen";
					?>
						<li style="<?=$style?>"> <?=$droit['titrePerm']?> : <?=$droit['val']?> </li>
					<?php endforeach; ?>
				<?php elseif ($_POST['droitsDeQui']=="afficherParRole"): ?>
					<?php foreach ($listeDroitsOuLimites as $droit): ?>
						<li> <?=$droit['titrePerm']?> </li>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
	
	<?php if (isset($valRetourApresModif)): ?>
		<h4 id="valRetourApresModifDroit"> <?= $valRetourApresModif?> </h4>
	<?php endif; ?>
	
	
	<div class="affichageEtModifierDroits">
		<h4 class="affichageEtModifierDroitsH4"> Modifier les droits <?=$roleOuUser?> : <?=$nomRoleOuUser?> </h4>
		
		<form method="POST" action="" id="formModifierDroits">
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			
			<input type="hidden" name="modifierDroitsType" value="<?=$_POST['droitsDeQui']?>"/>
			<input type="hidden" name="nomRoleOuUser" value="<?=$nomRoleOuUser?>"/>
			<?php if ($_POST['droitsDeQui']=="afficherParUser"): ?>
				<input type="hidden" name="login" value="<?=$nomRoleOuUser?>"/>
			<?php elseif ($_POST['droitsDeQui']=="afficherParRole"): ?>
				<input type="hidden" name="nomRole" value="<?=$nomRoleOuUser?>"/>
			<?php endif; ?>

			<select name="titrePerm" id="droitChoisi">
				<?php 	for ($i=0; $i<count($listeOptionsHtml); $i++) {
						echo $listeOptionsHtml[$i];
					}
				?>
			</select>
			
			<label id="labelAutoriserDroit" for="inputAutoriserDroit"> Autoriser </label>
			<input id="inputAutoriserDroit" type="radio" name="autoriserOuInterdireDroit" value="autoriser"/>
			
			<label id="labelInterdireDroit" for="inputInterdireDroit"> Interdire </label>
			<input id="inputInterdireDroit" type="radio" name="autoriserOuInterdireDroit" value="interdire"/>
			
			<?php if ($_POST['droitsDeQui']=="afficherParUser"): ?>
				<label for="inputSupprimerDroit"> Supprimer </label>
				<input id="inputSupprimerDroit" type="radio" name="autoriserOuInterdireDroit" value="supprimer"/>
			<?php endif; ?>
			
			<input type="submit" id="submitModifierDroits" class="boutonsForms" value="OK"/>
		</form>
	</div>

<?php endif; ?>
