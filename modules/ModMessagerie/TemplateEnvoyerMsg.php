<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h3 class="textesEnTete"> Ecrire un message </h3>

<div id="ecrireMsg">
	<form class="formulaire" id="formMsg" method="post" action="index.php?module=ModMessagerie&action=envoyer">
	
		<div class="labelinput">
			<label for="dest"> Destinataire </label>
			<input name="dest" type="text" id="dest" class="inputText" value="<?=$dest?>"/>
		</div>
			
		<div class="labelinput">
			<label for="titreMsg"> Titre <span id="titreMsgMaxLength"> (100 caractères maximum) </span> </label>
			<input name="titreMsg" type="text" id="titreMsg" class="inputText" value="<?=$titreMsg?>"/>
		</div>
		
		<textarea id="editor" name="message"> </textarea>
						
		<input type="hidden" name ="csrfToken" value="<?=$csrfToken ?>"/>
		<input class="boutonsForms" type="submit" value="Envoyer"/>
		
	</form>
</div>

<div style="position:relative; bottom:180; left:150" class="suggestions"> </div>

<script>
	
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    document.getElementsByClassName("wysibb-body")[0].innerHTML = "<?=$message?>";
	    getListLogins("dest","no");
	}, 100 );

</script>
