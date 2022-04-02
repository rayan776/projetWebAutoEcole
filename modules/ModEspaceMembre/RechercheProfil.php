<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<div id="afficherProfil">
	<form method="GET" action="index.php">

	
		<div class="labelinput">
			<input type="hidden" name="module" value="ModEspaceMembre"/>
			<input type="hidden" name="action" value="voirProfil"/>
			<div id="labelVoirProfil">
				<label for="login"> Saisissez un nom d'utilisateur </label>
			</div>
			<input id="inputVoirProfil" class="inputText" type='text' value='<?=$username ?>' name='login'/>
		</div>
		
		<input class="boutonsForms" type="submit" value="Voir profil" id="submitVoirProfil"/>

	</form>
	
	<div style="position:relative; bottom:100; left:500" class="suggestions"></div>
	
</div>

<SCRIPT>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLogins("inputVoirProfil","no");
	}, 100 );
</SCRIPT>
