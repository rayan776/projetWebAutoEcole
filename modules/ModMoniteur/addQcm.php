<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h3 class="textesEnTete"> Créer un nouveau QCM </h3>

<div style="color:white;">
	<p>
		Attention, il n'est pas possible de modifier un QCM une fois qu'il existe.
		<br/> Ceci afin d'éviter que l'affichage des résultats, pour ceux qui l'ont déjà passé, ne soit déformé.
	</p>
</div>

<?php if(isset($_POST['etapes'])&&in_array("1", $_POST['etapes'])&&in_array("2", $_POST['etapes'])&&in_array("3", $_POST['etapes'])): ?>
	
	<?php if (count($messageRetour)>0): 
		foreach ($messageRetour as $msg): ?>
		<h3 class="textesEnTete"> <?=htmlspecialchars($msg, ENT_QUOTES)?> </h3>
		<?php endforeach; ?>
	<?php endif; ?>
<?php else: 

if (isset($_POST['etapes'])&&in_array("1", $_POST['etapes'])&&in_array("2", $_POST['etapes'])) {
	$action="index.php?module=ModMoniteur&action=addQcm";
}
else {
	$action="index.php?module=ModMoniteur&action=menuAddQcm";
}
	?>

<div id="createQcm">
	<form method="POST" action=<?=$action?>>
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		
		<?php if (!isset($_POST['etapes'])): ?>
			<input type="hidden" name="etapes[]" value="1"/>
			<div id="nomQcm">
				<div style="display:flex;">
					<span> Nom du QCM </span>
					<input class="inputText" id="createQcm_nomQcm" type="text" name="nomQcm"/>
				</div>
				
				<div id="createQcm_nbQuestions" style="display:flex;">
					<span> Nombre de questions </span>
					<select name="nbQuestionsQcm">
						<?php for ($i=1; $i<=40; $i++): ?>
							<option value="<?=$i?>"> <?=$i?> </option>
						<?php endfor; ?>
					</select>
				</div>
			</div>
		<?php elseif (in_array("1", $_POST['etapes'])): ?>
			<input type="hidden" name="etapes[]" value="1"/>
			<?php if (in_array("2", $_POST['etapes'])): ?>

				<input type="hidden" name="etapes[]" value="2"/>
				<input type="hidden" name="nomQcm" value="<?=$_POST['nomQcm']?>"/>
				<input type="hidden" name="nbQuestionsQcm" value="<?=$_POST['nbQuestionsQcm']?>"/>
				
				<?php foreach ($_POST['questions'] as $key => $val): ?>
					<input type="hidden" name="questions[<?=$key?>]" value="<?=$val?>"/>
				<?php endforeach; ?>
				
				<?php foreach ($_POST['nbReponses'] as $key => $val): ?>
					<input type="hidden" name="nbReponses[<?=$key?>]" value="<?=$val?>"/>
				<?php endforeach; ?>
				
				<?php foreach ($_POST['limiteTemps'] as $key => $val): ?>
					<input type="hidden" name="limiteTemps[<?=$key?>]" value="<?=$val?>"/>
				<?php endforeach; ?>
				
				<?php foreach ($_POST['illustrations'] as $key => $val): ?>
					<input type="hidden" name="illustrations[<?=$key?>]" value="<?=$val?>"/>
				<?php endforeach; ?>
				
				<div id="etape3_createQcm">
					<input type="hidden" name="etapes[]" value="3"/>
					<div> Nom du QCM: <?=htmlspecialchars($_POST['nomQcm'], ENT_QUOTES)?> </div>
					
					<div> Nombre de questions: <?=htmlspecialchars($_POST['nbQuestionsQcm'], ENT_QUOTES)?> </div>
					
					<?php $i=1; foreach ($_POST['questions'] as $key => $val): ?>
						<div style="border:1px solid white; padding:10px; margin-top:20px; margin-bottom:20px;">
							Question <?=$i?> : " <?=htmlspecialchars($val, ENT_QUOTES)?> "
							
							<div>
								<img src="<?=$_POST['illustrations']["q$i"]?>" alt="Illustration" class="imgQcm" style="margin-left: 200; margin-top: 50; margin-bottom: 50"/>
							</div>
							
							<ul>
							<?php for($j=1; $j<=intval($_POST['nbReponses']["q$i"]); $j++): ?>
								
								<li>
								
									<div style="display:flex; margin-bottom:25px;">
										<span> Réponse <?=$j?> </span>

										<input style="width:600px;" inputText" type="text" name="reponsesQ<?=$i?>[r<?=$j?>]"/>
										
										<span> Correct </span>
										<input style="margin:0; width:50;" type="checkbox" name="correctQ<?=$i?>[]" value="r<?=$j?>"/>
										
									</div>
								</li>
								
							<?php endfor; ?>
							</ul>
						</div>
						<?php $i++; ?>
					<?php endforeach; ?>
				</div>	

			<?php else: ?>
				<div id="etape2_createQcm">
					<input type="hidden" name="nomQcm" value="<?=$_POST['nomQcm']?>"/>
					<input type="hidden" name="nbQuestionsQcm" value="<?=$_POST['nbQuestionsQcm']?>"/>
					<input type="hidden" name="etapes[]" value="2"/>
					
					<div> Nom du QCM: <?=htmlspecialchars($_POST['nomQcm'], ENT_QUOTES)?> </div>
					
					<div> Nombre de questions: <?=htmlspecialchars($_POST['nbQuestionsQcm'], ENT_QUOTES)?> </div>
					
					<?php for ($i=1; $i<=intval($_POST['nbQuestionsQcm']); $i++): ?>
					
						<div class="etape2_createQcm_inputs">
						
							<div style="display:flex;">
								<span> Nom de la question <?=$i?> </span>
								
								<input style="position: relative; right: 30; width: 700" class="inputText" type="text" name="questions[q<?=$i?>]"/>
							</div>
							
							<div style="display:flex; margin-top: 30">
								<span> Secondes pour répondre </span>
								
								<select style="width: 50; height:30" name="limiteTemps[q<?=$i?>]">
									<?php for ($s=10; $s<=60; $s++): ?>
										<option value="<?=$s?>"> <?=$s?> </option>
									<?php endfor; ?>
								</select>
							
							</div>
							
							<div style="display:flex; margin-top:30">
								<span> Illustration </span>
								
								<select style="height: 30;
margin-left: 103" onchange="document.getElementById('qcmIllustration<?=$i?>').src=this.value;" name="illustrations[q<?=$i?>]">
									<?php foreach ($illustrations as $tuple): ?>
										<option value="<?=$tuple['illustration']?>"> <?=$tuple['description']?> </option>
									<?php endforeach; ?>
								</select>
								
								<img id="qcmIllustration<?=$i?>" class="imgQcm" style="margin-left:30" src="resources/imgQcmDefault.jpg" alt="Illustration"/>
							</div>
							
							<div style="display:flex; position:relative; bottom:230">
							
								<span> Nombre de réponses </span>
								
								<select style="width:50px; height:30px; position:relative; left:27" name="nbReponses[q<?=$i?>]">
									<?php for ($j=2; $j<=4; $j++): ?>
										<option value="<?=$j?>"> <?=$j?> </option>
									<?php endfor; ?>
								</select>
							</div>
						
						</div>
						
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<input id="createQcmEtapeSuivante" type="submit" class="boutonsForms" value="Passage à l'étape suivante"/>
	</form>
</div>

<?php endif; ?>
