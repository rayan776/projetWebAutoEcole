<?php 
	if(!defined('CONSTANTE'))
		die("Accès interdit");
?>


<div class="questionQcm">

	<div style="display:flex;">

		<h3> QCM <?=$nomQcm?> - Question <?=$numeroQuestion+1?> </h3>
			
		
		<div id="timerDiv">
			<?php if (isset($msgRestant)): ?> 
				<div> <?=$msgRestant?> </div> 
			<?php else: ?>
				<span id="tempsRestant"> Temps restant: </span> <span id="timer"> <?=$tempsRestant?> secondes </span>
			<?php endif; ?>
		</div>
	
	</div>

	<form id="repondre" method="POST" action="index.php?module=ModEleve&action=questionSuivanteQcm">
		<div class="nomQuestion"> <?=$question->nomQuestion?> </div>
		
		<img class="imgQcm" style="margin-top:50; margin-left:600" src="<?=$question->illustration?>" alt="Illustration"/>
		
		<?php if (count($tabDejaRep)>0): ?>
			<h3 class="textesEnTete"> Vous avez déjà répondu à cette question! Vous ne pouvez plus changer vos réponses. </h3>
		<?php endif; ?>
		
		<?php foreach ($reponses as $reponse):
			
			$checkedOuPas = in_array($reponse['idReponse'], $tabDejaRep) ? "checked" : "";
		
		?>
			
			<div class="divQuestionQcm">
					
					<input type="checkbox" name="reponses[]" value="<?=$reponse['idReponse']?>" <?=$onclick?> <?=$checkedOuPas?>/>					
					<span class="labelReponseQcm"> <?=$reponse['nomReponse']?> </span>
			</div>
			
		<?php endforeach; ?>
		
		<input type="hidden" name="idQcm" value="<?=$_POST['idQcm']?>"/>
		<input type="hidden" name="idTentative" value="<?=$idTentative?>"/>
		<input type="hidden" name="idQuestion" value="<?=$question->idQuestion?>"/>
		<input type="hidden" name="answer"/>
		<input type="hidden" name="numeroQuestion" value="<?=$numeroQuestion+1?>"/>
		<input type="submit" class="boutonsForms" value="Passer à la question suivante"/>
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	</form>
</div>

<?php if (!isset($msgRestant)): ?>
<SCRIPT>
	var refreshInterval = setInterval(function () {
		var timerVal = document.getElementById("timer").innerHTML;
		
		var secondesRestantes = parseInt(timerVal, 10);
		
		if (secondesRestantes <= 0) {
			document.getElementById("tempsRestant").innerHTML = "";
			document.getElementById("timer").innerHTML = "Vous avez mis trop de temps à répondre à cette question. Votre réponse ne sera donc pas prise en compte, même si elle est juste.";
			document.getElementById("timer").style.color = "red";
			clearInterval(refreshInterval);
		}
		else {
			var newSeconds = secondesRestantes-1;
			document.getElementById("timer").innerHTML = newSeconds + " secondes";
		}
		
		
		
	}, 1000);		
</SCRIPT>
<?php endif; ?>
