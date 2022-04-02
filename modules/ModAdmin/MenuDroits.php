<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

?>

<h2> Gestion des droits </h2>

<div id="containerDroits">

	<div id="menuDroitsDivSup">

		<h4 id="titreAfficherDroits"> Affichage des droits d'un rôle ou des autorisations/interdictions exceptionnelles accordées à un utilisateur </h4>

		<form id="formAfficherDroits" method="POST" action="index.php?module=ModAdmin&action=menuDroits">
			
			<div id="choisirRoleOuUserDroits">

				<label id="labelRole" for="nomRole"> Rôle: </label>
				<select name="nomRole" id="nomRole">
					<?php 	for ($i=0; $i<count($rolesOptionsHtml); $i++) {
							echo $rolesOptionsHtml[$i];
						}
					?>
				</select>
				
				<label id="labelLogin" for="login"> Nom d'utilisateur: </label>
				<input id="login" value="<?=$valueLogin?>" class="inputText" type="text" name="login"/>
				
			</div>
			
		</form>

		<div id="deuxRadios">

			<div id="divRadioRole" class="divRadio">
				<label for="radioRole"> Choisir un rôle </label>
				<input id="radioRole" onchange="disableSelectRoleOuUser('role');" form="formAfficherDroits" type="radio" value="afficherParRole" name="droitsDeQui" <?= $radioRoleChecked ?>>
			</div>

			<div id="divRadioUser" class="divRadio">
				<label for="radioUser"> Choisir un nom d'utilisateur </label>
				<input id="radioUser" onchange="disableSelectRoleOuUser('afficherParUser');" form="formAfficherDroits" type="radio" value="afficherParUser" name="droitsDeQui" <?= $radioUserChecked ?>>
			</div>
			
			<input id="submitShowDroits" form="formAfficherDroits" type="submit" class="boutonsForms" value="Afficher droits"/>
		
		</div>

	</div>
	
	<div id="explicationDroits">
		<p> Les droits d'un utilisateur sont, par défaut, ceux de son rôle. En revanche, il est possible d'attribuer exceptionnellement des autorisations/interdictions à un utilisateur précis. Attention, il est inutile d'accorder des droits d'administration à un élève, il ne pourra pas en profiter. </p>
	
		<div class="suggestions"></div>
	</div>

</div>

<?php echo $codeJsDisable; ?>

<script>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLogins("login","no");
	}, 100 );
</script>
