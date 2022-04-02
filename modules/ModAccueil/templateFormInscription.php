<?php if (!defined('CONSTANTE')) {
	die("Accès interdit");
} 
?>

<div class="infos_pwd" id="infos_pwd_insc" style="position:absolute; top:450; right:450"> Votre mot de passe doit être composé d'au moins 8 caractères, avec au moins 1 majuscule et 1 minuscule, ainsi que 3 chiffres et 1 caractère spécial. </div>

<h1 class="textesEnTete"> <?=$tache ?>-vous </h1>

			<form method='post' action='index.php?module=ModAccueil&action=<?=$task ?>'>
			
			<div id="formulaireInsc" class="formulaire">
				
					<div id="superContainer">
				
					<div id="loginEtMdp">
						
					
						<h3 id='infosPersosLoginMdp'> Vos informations de connexion </h3>
			
						<div id="infosLoginMdp">
							<div class="labelinput">
								<label for='login'> Nom d'utilisateur: </label>
								<input class="inputText" id='login' type='text' value='<?=$login ?>' name='login'/>
							</div>
							
							<div id="uname_response"> </div>
					
							<div onmouseover="document.getElementById('infos_pwd_insc').style.opacity='100%';" onmouseleave="document.getElementById('infos_pwd_insc').style.opacity='0%';" class="labelinput">
								<label for='password'> Mot de passe: </label>
								<input class="inputText" id='password' type='password' name='password'/>
							</div>
							
							<div id="pwd_reponse"> </div>
							
							<div class="labelinput">
								<label for='newMdpConf'> Confirmer votre mot de passe: </label>
								<input class="inputText" id='newMdpConf' type='password' name='newMdpConf'/>
							</div>
							
							<div id="msgConfMdp"></div>
						</div>

						
						<h3 id='type'> Votre rôle </h3>
						
						<div id="moniteurOuEleve">

							<div class="labelinput">
								<label for="moniteur"> Moniteur </label>
								<input id='moniteur' onchange="disableField('nephInput', true);" type="radio" name="type" value="moniteur" <?= $moniteurCheck ?>/>
							</div>
						
							<div class="labelinput">
								<label for="eleve"> Eleve </label>
								<input id='eleve' onchange="disableField('nephInput', false);" type="radio" name="type" value="eleve" <?= $eleveCheck ?> />
							</div>
						</div>
						
						<div class="labelinput">
							<label for="neph"> Numéro NEPH: </label>
							<p id="nephNotice"> (seulement si vous êtes élève) </p> 
							<input id="nephInput" class="inputText" id='neph' type='text' value='<?=$neph ?>' name='neph'/ <?= $nephDisabled ?>>
						</div>
							
						
					</div>
						
					<div id="infosPersoInscription">

						<h3 id='infosPersosTitre'> Vos informations personnelles </h3>
						
						<div id='infosPersos'>					
						
							<div class="labelinput">
								<label for="nom"> Nom: </label>
								<input class="inputText nomPrenomVille" id='nom' type='text' value='<?=$nom ?>' name='nom'/>
							</div>
							
							
							<div class="labelinput">
								<label for="prenom"> Prénom: </label>
								<input class="inputText nomPrenomVille" id='prenom' type='text' value='<?=$prenom ?>' name='prenom'/>
							</div>
							
							<div class="labelinput">
								<label for="ville"> Ville: </label>
								<input class="inputText nomPrenomVille" id='ville' type='text' value='<?=$ville ?>' name='ville'/>
							</div>
							
							<div class="labelinput">
								<label for="cp"> Code postal: </label>
								<input class="inputText" id='cp' type='text' value='<?=$cp ?>' name='cp'/>
							</div>
							
							<div class="labelinput">
								<label for="phone"> Numéro de téléphone: </label>
								<input class="inputText" id='phone' type='text' value='<?=$phone ?>' name='phone'/>
							</div>
						</div>
						
					</div>
					
				</div>
				
					<input type="hidden" name="csrfToken" value="<?= $csrfToken ?>"/>
					<input id='submit' class='valider' type='submit' value="<?= $valSubmit ?>"/>
			
			</div>
			
			</form>

<SCRIPT>
	$(document).ready(function(){
		
		initAjaxLogin();
		initVerifPwd();

	});


</SCRIPT>
