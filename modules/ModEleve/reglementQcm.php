<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
?>

<h3 class="textesEnTete"> Réglement des QCM </h3>

<div id="reglementQcm">
	<div id="lesReglesQcm">
		<ul>
			<li> Vous disposez d'un certain temps pour répondre à chaque question, à compter de l'affichage de la question. </li>
			<li> Si vous répondez après la fin du compte à rebours, votre réponse sera ignorée, même si elle est vraie. </li>
			<li> Vous ne pouvez pas revenir en arrière. </li>
			<li> A la fin du QCM, vos résultats s'affichent avec la correction. </li>
		</ul>
	</div>
	
	<form method="POST" action="index.php?module=ModEleve&action=questionSuivanteQcm"/>
		<input type="hidden" name="idQcm" value="<?=$idQcm?>"/>
		<input type="hidden" name="idTentative" value="<?=$idTentative?>"/>
		<div style="margin-top:50;" id="submitReglement">
			<input class="boutonsForms" type="submit" value="Commencer le QCM"/>
		</div>
	</form>
</div>

