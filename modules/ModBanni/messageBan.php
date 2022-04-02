<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
	
?>

<h1 class="textesEnTete"> <?=$ban->login?>, vous êtes actuellement banni du site. </h1>

<div id="messageBan">

	<h2> Motif: <?=$ban->motif?> </h2>
	
	<h3> Banni par: <?=$banniPar?> </h3>
	
	<h3> Votre bannissement prendra fin à l'échéance suivante: <?=$dateFin?> </h3>

</div>
