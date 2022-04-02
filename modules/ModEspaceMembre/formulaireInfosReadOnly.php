<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
?>

                <?php

                    ?>
                    <div class="textesEnTete" id="profilDe">
                  	 <h3> Utilisateur: <?= $username ?> </h3>
                   	 <h4> Rôle: <?= $role ?> </h4>
                   	 <?php if (Utilitaires::estBanni($username)): ?>
                   	 	<h4> Cet utilisateur est actuellement banni. </h4>
                   	 <?php endif; ?>
                   	 <?php if ($username != $_SESSION['login']['login']): ?>
                   	 	<form method="POST" action="index.php?module=ModMessagerie&action=formulaireEnvoi">
                   	 		<input type="hidden" name="dest" value="<?=$username?>"/>
                   	 		<input type="submit" class="boutonsForms" value="Envoyer un message privé"/>
                   	 	</form>
                   	 <?php endif; ?>
                    </div>
                    
                    <?php
                    	$roleCurrentUser = Utilitaires::getRoleCurrentUser();
                    	
                    	$visibleParEleves = Utilitaires::infosVisiblesParEleves($username);
                    	
                    	if ($username==$_SESSION['login']['login']||$roleCurrentUser=="moniteur"||$roleCurrentUser=="admin"||($roleCurrentUser=="eleve"&&$visibleParEleves=="1")):
			
		?>
						
				<div id="infosPersoVoirProfil" class="infosPerso">
						<div class="labelinput">
							<label for="nom"> Nom: </label>
							<input id='nom' type='text' value='<?=$nom ?>' readonly="readonly"/>
						</div>
						
						<div class="labelinput">
							<label for="prenom"> Prénom: </label>
							<input id='prenom' type='text' value='<?=$prenom ?>' readonly="readonly"/>
						</div>
						
						<div class="labelinput">
							<label for="ville"> Ville: </label>
							<input id='ville' type='text' value='<?=$ville ?>' readonly="readonly"/>
						</div>
						
						<div class="labelinput">
							<label for="cp"> Code postal: </label>
							<input id='cp' type='text' value='<?=$codePostal ?>' readonly="readonly"/>
						</div>
						
						<div class="labelinput">
							<label for="phone"> Numéro de téléphone: </label>
							<input id='phone' type='text' value='<?=$numTel ?>' readonly="readonly"/>
                      				  </div>
						
						<div class="labelinput">
							<label for="neph"> Numéro NEPH: </label>
							<input id='neph' type='text' value='<?=$neph ?>' readonly="readonly"/>
                       			 </div>

             		  	 </div>
             		  	 
             		 <?php endif; ?>
