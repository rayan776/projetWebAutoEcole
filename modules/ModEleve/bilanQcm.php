<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

?>
		
		
<h2 class="textesEnTete"> QCM: <?=$nomQcm?> </h2>

<div class="bilanQcm">

	<h3> Résultat: <?=$nbQuestionsEtNote->note ?> bonnes réponses sur <?=$nbQuestionsQcm?> questions. </h3>
	<h4> Pourcentage de réussite: <?=$pourcentageReussite?> </h4>

	<?php
		$questionsDejaMises = array();
		$numeroQuestion = 0;
		
	?>

	<?php foreach ($questionsReponses as $question): ?>
	
		<?php
			$nomIndex = "question" . $question['idQuestion'];
			
			// nom de la question
			if (!in_array($question['idQuestion'], $questionsDejaMises)) {
				$numeroQuestion++;
				if (count($questionsDejaMises)>0):
					?> </div> <?php
				endif;
			
				?> <div class="questionQcm" id="questionQcmBilan"> <?php
				
				$questionsDejaMises[] = $question['idQuestion'];
				?>
					<div class="nomQuestion"> Question <?=$numeroQuestion?> : <?=$question['nomQuestion']?> </div>
					
					<img src="<?=$question['illustration']?>" alt="Illustration" class="imgQcm" style="margin-top:50; margin-left:200"/>
				<?php
			}
			
			// affichage des réponses
		?>
		
			<?php
				$checked = 0;
				$style = "";
				
				if (count($reponsesUser)>0&&in_array($question['idReponse'], $reponsesUser[$nomIndex])) {
					
					$checked=1;
					
					if ($question['correct']==0) {
						$style = "style='color:red;'";
					}
				}
				
			
				if ($question['correct']==1) {
					$style = "style='color:lime;'";
				}

			?>
		
			<div class="divQuestionQcm">
				<?php if ($checked): ?>
					<input type="checkbox" onclick="this.checked=!this.checked;" checked/>
				<?php else: ?>
					<input type="checkbox" onclick="this.checked=!this.checked;"/>	
				<?php endif; ?>					
				<span <?=$style?> class="labelReponseQcm"> <?=$question['nomReponse']?> </span>
			</div>
		
	<?php endforeach; ?>
	
</div>
