<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<?php if (!is_array($qcm)): ?>
	<h3 class="textesEnTete"> Ce QCM n'existe pas. </h3>
	
	<a href="index.php?module=ModMoniteur&action=gererQcm"> Retour à la liste des QCM </a>
<?php else: ?>
		
<h2 class="textesEnTete"> QCM: <?=$nomQcm?> </h2>

<div class="bilanQcm">

	<?php
		$questionsDejaMises = array();
		$numeroQuestion = 0;
		
	?>

	<?php foreach ($questions as $question): ?>
	
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
					
					<img src="<?=$question['illustration']?>" alt="Illustration" class="imgQcm" style="margin-top: 50; margin-left: 200;"/>
				<?php
			}
			
			// affichage des réponses
		?>
		
			<?php
				$checked = 0;
				$style = "";
					
				$checked=1;
				
				if ($question['correct']==0) {
					$style = "style='color:white;'";
					$checked=0;
				}
				else {
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

<?php endif; ?>
