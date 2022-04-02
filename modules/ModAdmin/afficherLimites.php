<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<?php if (count($listeDroitsOuLimites)==1 && ($listeDroitsOuLimites[0]==-2)): ?>
	<h3> Cet utilisateur n'existe pas. </h3>
<?php else:
	$csrfToken = Tokens::insererTokenForm();
	
	$valeurLimite = isset($_POST['valeurLimite']) ? htmlspecialchars($_POST['valeurLimite']) : "";
	$joursLimite = isset($_POST['joursLimite']) ? htmlspecialchars($_POST['joursLimite']) : "";
	$heuresLimite = isset($_POST['heuresLimite']) ? htmlspecialchars($_POST['heuresLimite']) : "";
	$minutesLimite = isset($_POST['minutesLimite']) ? htmlspecialchars($_POST['minutesLimite']) : "";

?>

	<div class="affichageEtModifierDroits">
		<h4 class="affichageEtModifierDroitsH4"> Liste des limites <?=$roleOuUser?> : <?=$nomRoleOuUser?> </h4>
		<ul>
			<?php if (count($listeDroitsOuLimites)==0): ?>
				<?php if ($_POST['limitesDeQui']=="afficherParUser"): ?>
					<li> Aucune limite exceptionnelle n'existe pour cet utilisateur. </li>
				<?php elseif ($_POST['limitesDeQui']=="afficherParRole"): ?>
					<li> Aucune limite n'existe pour ce rôle. </li>
				<?php endif; ?>
			<?php else: ?>
				<?php if ($_POST['limitesDeQui']=="afficherParUser"): ?>
					<?php foreach ($listeDroitsOuLimites as $limite):
						$chaineperiod = ($limite['val'] != "infini") ? "Période: " . Utilitaires::convertirMinutes($limite['period']) : "";
					?>
					<li> <?=$limite['nomLimite']?> : <?=$limite['val']?>. <?= $chaineperiod ?> </li>
					<?php endforeach; ?>
				<?php elseif ($_POST['limitesDeQui']=="afficherParRole"): ?>
					<?php foreach ($listeDroitsOuLimites as $limite):
							$chaineperiod = ($limite['val'] != "infini") ? "Période: " . Utilitaires::convertirMinutes($limite['period']) : "";					
						?>
						<li> <?=$limite['nomLimite']?> : <?=$limite['val']?>. <?= $chaineperiod ?> </li>
						<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
	
	<?php if (isset($valRetourApresModif)): ?>
		<h4 id="valRetourApresModifDroit"> <?= $valRetourApresModif?> </h4>
	<?php endif; ?>
	
	
	<div id="changerLimites">
		<h4 class="affichageEtModifierDroitsH4"> Modifier les limites <?=$roleOuUser?> : <?=$nomRoleOuUser?> </h4>
		
		<form method="POST" action="" id="formModifierLimites">
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			
			<input type="hidden" name="modifierLimitesType" value="<?=$_POST['limitesDeQui']?>"/>
			<input type="hidden" name="nomRoleOuUser" value="<?=$nomRoleOuUser?>"/>
			<?php if ($_POST['limitesDeQui']=="afficherParUser"): ?>
				<input type="hidden" name="login" value="<?=$nomRoleOuUser?>"/>
			<?php elseif ($_POST['limitesDeQui']=="afficherParRole"): ?>
				<input type="hidden" name="nomRole" value="<?=$nomRoleOuUser?>"/>
			<?php endif; ?>

			<div id="infosChangementLimite">
				<h4> Choisir la limite à modifier </h4>
				<select name="nomLimite" id="limiteChoisi">
				
				<?php 	for ($i=0; $i<count($listeOptionsHtml); $i++) {
						echo $listeOptionsHtml[$i];
					}
				?>
				
				</select>
				
				<div class="divValeurLimite" id="choixValeurLimite">
					<label id="labelValeurLimite" for="valeurChangerLimite"> Valeur: </label>
					<input id="valeurLimite" class="inputText inputsLimit" type="text" value="<?=$valeurLimite?>" name="valeurLimite"/>
					
					
					<div id="divInfini" class="divValeurLimite">
						<label id="labelInfini" for="infiniLimite"> Infini (pas de limitation) </label>
						<input type="checkbox" id="infiniLimite" name="infiniLimite"/>
					</div>
				</div>
			
				<h4> Choisissez la période glissante durant laquelle vous souhaitez limiter le nombre d'actions possibles </h4>

				<div id="tempsChangerLimite">
					
					<div class="divValeurLimite">
						<label for="valeurJoursLimite"> Jours: </label>
						<input id="joursLimite" class="inputText inputsLimit" type="text" value="<?=$joursLimite?>" name="joursLimite"/>
					</div>
					
					<div class="divValeurLimite">
						<label for="valeurHeuresLimite"> Heures: </label>
						<input id="heuresLimite" class="inputText inputsLimit" type="text" value="<?=$heuresLimite?>" name="heuresLimite"/>
					</div>
					
					<div class="divValeurLimite">
						<label for="valeurMinutesLimite"> Minutes: </label>
						<input id="minutesLimite" class="inputText inputsLimit" type="text" value="<?=$minutesLimite?>"; name="minutesLimite"/>
					</div>			
				</div>
				
				<?php if ($_POST['limitesDeQui']=="afficherParUser"): ?>
					<div class="divValeurLimite">
						<label id="labelSupprimerLimite" for="supprimerLimite"> Supprimer </label>
						<input type="checkbox" name="supprimerLimite" id="supprimerLimite"/>
					</div>
				<?php endif; ?>

			</div>
					
			
			<input type="submit" id="submitModifierLimites" class="boutonsForms" value="OK"/>
		</form>
	</div>
	

	
	<SCRIPT> 
		disableInputsLimites(); 
	
	</SCRIPT>

<?php endif; ?>
