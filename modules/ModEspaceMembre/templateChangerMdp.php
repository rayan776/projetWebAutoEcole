<?php
	if  (!defined('CONSTANTE'))
		die("Accès interdit");
	
	?>
	
	<div class="infos_pwd" id="infos_pwd_changer" style="position:absolute; top:700; left:500"> Votre mot de passe doit être composé d'au moins 8 caractères, avec au moins 1 majuscule et 1 minuscule, ainsi que 3 chiffres et 1 caractère spécial. </div>

	<h2 class="textesEnTete"> Changement de mot de passe </h2>
	
	<div id="changerMdp">
		<form id="formChangerMdp" class="formulaire" action="index.php?module=ModEspaceMembre&action=changerMdp" method="post">
		
			<div class="labelinput">
				<label for="mdpActu"> Votre mot de passe actuel: </label>
				<input class="inputText" class ="inputs" id="mdpActu" name="mdpActu" type="password"/>
			</div>
			
			<div id="msgMdpActu"></div>
			
			<div onmouseover="document.getElementById('infos_pwd_changer').style.opacity='100%';" onmouseleave="document.getElementById('infos_pwd_changer').style.opacity='0%';" class="labelinput">			
				<label for="password"> Nouveau mot de passe: </label>
				<input class="inputText" class="inputs" id="password" name="password" type="password"/>
			</div>
			
			<div id="pwd_reponse"></div>
			
			<div class="labelinput">
				<label for="newMdpConf"> Confirmez votre nouveau mot de passe: </label>
				<input class="inputText" class="inputs" id="newMdpConf" name="newMdpConf" type="password"/>
			</div>
			
			<div id="msgConfMdp"></div>
					
			<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
			
			<input  class="boutonsForms" id="valider" type="submit" value="Valider"/>
		</form>
	</div>
	
	<script>
		var tid = setInterval( function () {
		    if ( document.readyState !== 'complete' ) return;
		    clearInterval( tid );       
		    getPwdActu("mdpActu",0);
		}, 100 );
	</script>
