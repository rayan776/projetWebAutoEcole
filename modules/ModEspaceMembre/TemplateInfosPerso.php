<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
?>

<form method="POST" action="index.php?module=ModEspaceMembre&action=updateInfosPerso">
   <h2 class="textesEnTete"> Mes informations personnelles </h2>
	<div id="toutLeFormulaire">

                <?php

                    ?>
                 		<div class="infosPerso">
                 		
                 				<h3 id="h3infosperso"> Vos infos </h3>
                 		
						<div class="labelinput">
							<label for="nom"> Nom: </label>
							<input class="inputText" id='nom' type='text' value='<?=$nom ?>' name='nom'/>
						</div>
						
						<div class="labelinput">
							<label for="prenom"> Prénom: </label>
							<input class="inputText" id='prenom' type='text' value='<?=$prenom ?>' name='prenom'/>
						</div>
						
						<div class="labelinput">
							<label for="ville"> Ville: </label>
							<input class="inputText" id='ville' type='text' value='<?=$ville ?>' name='ville'/>
						</div>
						
						<div class="labelinput">
							<label for="cp"> Code postal: </label>
							<input class="inputText" id='cp' type='text' value='<?=$codePostal ?>' name='cp'/>
						</div>
						
						<div class="labelinput">
							<label for="phone"> Numéro de téléphone: </label>
							<input class="inputText" id='phone' type='text' value='<?=$numTel ?>' name='phone'/>
						</div>

                        <?php if ($neph != 0): ?>
						
						<div class="labelinput">
							<label for="neph"> Numéro NEPH: </label>
							<input class="inputText" id='neph' type='text' value='<?=$neph ?>' name='neph'/>
						</div>

                        <?php endif; ?>

				</div>
				
				<div id="preferences">
					<h3> Vos préférences </h3>
					<div id="preferences1">
						<label for="autoriserElevesVoirInfos"> Permettre aux élèves du site de voir mes informations personnelles </label>
						<input type="checkbox" id="autoriserElevesVoirInfos" name="autoriserElevesVoirInfos" <?=$autoriserElevesChecked ?>/>
					</div>
					
					<?php if (Utilitaires::getRoleCurrentUser()=="eleve"): ?>
					<div id="preferences2" style="display:flex; margin-top:20">
						<label for="autoriserElevesVoirEDT"> Permettre aux élèves du site de voir mon emploi du temps </label>
						<input style="margin:0; position:relative; right:36" type="checkbox" id="autoriserElevesVoirEDT" name="autoriserElevesVoirEDT" <?=$autoriserElevesEDTChecked ?>/>
					</div>
					<?php endif; ?>											
				</div>
				
				<div id="limites">

					<h3> Les limites de votre rôle (<?= $role?>) </h3>
					
					<?php if (Utilitaires::getRoleCurrentUser()!="admin"): ?>					
					<div id="listeLimitesRole">
						<ul>
						<?php foreach ($listeLimitesRole as $limite):
							$chaineperiod = ($limite['val']!="infini") ? "Période: " . Utilitaires::convertirMinutes($limite['period']) : "";					
						?>
						<li style="margin-bottom:10"> 
 							<div> <?=$limite['nomLimite']?> : <?=$limite['val']?> </div>
							<div> <?= $chaineperiod ?> </div>
						</li>
						
						<?php endforeach; ?>
						
						</ul>
					</div>
					
			
					
					<h3> Vos limites exceptionnelles </h3>
					<div id="listeLimitesUser">
						<?php if (count($listeLimitesUser)>0): ?>
							<ul>
							<?php foreach ($listeLimitesUser as $limite):
								$chaineperiod = ($limite['val']!="infini") ? "Période: " . Utilitaires::convertirMinutes($limite['period']) : "";					
							?>
							<li> <?=$limite['nomLimite']?> : <?=$limite['val']?>. <?= $chaineperiod ?> </li>
							<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<h4> Aucune limite exceptionnelle ne vous concerne actuellement. </h4>
						<?php endif; ?>
					</div>
					
					<h3> Nombre d'actions effectuées lors de la dernière période glissante </h3>
					<div id="listeActions">
						<ul>
						<?php foreach ($listeLimitesRole as $limite): ?>
							<li style="margin-bottom:10">
								<div> Nombre de <?=$affichage[$limite['nomLimite']]?> lors de la dernière période glissante: <?=$valeursLimites[$limite['nomLimite']]?> </div>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
					<?php else: ?>
						<div> Vous n'avez aucune limite, étant administrateur. </div>
					<?php endif; ?>
				</div>



				<input type="hidden" name="csrfToken" value="<?= $csrfToken ?>"/>
                    		<input type="hidden" name="type" value="<?= $role ?>"/>
                    		<input type="hidden" name="update" value="true"/>
					
	</div>
	
	<input class="boutonsForms" id='submitModifierInfos' type='submit' value="Modifier mes informations"/>
</form>
