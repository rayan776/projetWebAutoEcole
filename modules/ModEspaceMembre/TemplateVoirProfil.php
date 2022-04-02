<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
	
	if (!isset($username))	
		$username = isset($_POST['login']) ? htmlspecialchars($_POST['login'], ENT_QUOTES) : "";
?>

<h2 class="textesEnTete"> Voir un profil </h2>

<?php require_once "RechercheProfil.php"; ?>

<div id="voirProfilContainer">
	
	<?php if($afficherInfos): require_once "formulaireInfosReadOnly.php"; endif; ?>

</div>
