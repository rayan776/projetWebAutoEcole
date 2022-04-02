<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h1 class="textesEnTete"> <?=$tache ?>-vous </h1>
			
			<div id="formulaireConnexion" class='formulaire'>
			
				<form method='post' action='index.php?module=ModAccueil&action=<?=$task ?>'>
			
						<div class="labelinput">
							<label for='login'> Nom d'utilisateur: </label>
							<input class="inputText" id='login' type='text' value='<?=$login ?>' name='login'/>
						</div>
				
						<div class="labelinput">
							<label for='password'> Mot de passe: </label>
							<input class="inputText" id='password' type='password' name='password'/>
						</div>
							
					<input type="hidden" name="csrfToken" value="<?= $csrfToken ?>"/>							
					<input class='valider' type='submit' value="<?= $valSubmit ?>"/>
			
				</form>
			
			</div>
