<?php if (!defined('CONSTANTE'))
	die("Accès interdit");
?>

<p id='entete'> Bienvenue sur la page d'accueil </p>

<div style="color:white; display:flex">

	<div id="presentation"> 

	<?=$annonce->annonce?>

	</div>
	
	<style>
		#presentation {
			color:<?=$annonce->color?>;
			background-color:<?=$annonce->bgColor?>;
		}
	</style>
	
	<div class="animationBorderRouge" id="quiSommesNous">
	
		<h2> A propos de nous </h2>
		
		<div class="responsable">
			<div class="respNom"> Rayane BOUFENGHOUR, administrateur </div>
			<div> Directeur de l'établissement </div>
			<div> Contact: rboufenghour@iut.univ-paris8.fr </div>
		</div>
		
		<div class="responsable">
			<div class="respNom"> Yan MEDDOUR, moniteur </div>
			<div> 5 ans d'expérience en tant que moniteur </div>
			<div> Contact: ymeddour@iut.univ-paris8.fr </div>
		</div>
		
		<div class="responsable">
			<div class="respNom"> Yassine BADRI, moniteur </div>
			<div> Grand spécialiste des Audi </div>
			<div> Contact: ybadri@iut.univ-paris8.fr </div>
		</div>
	
	</div>
	
</div>

<div style="display:flex">
	<div id="formules">

		<h2> Nos formules </h2>
		
		
		<?php if (count($formules)>0): ?>
		
		<div class="formules">
		<?php
			$nb=3; // nombre de formules par ligne
				
			for ($i=0; $i<count($formules); $i++) {
			
			
				if ($i==0||$i>0&&$i%$nb==0) {
					echo "<div style='display:flex'>";
				}
				
				?>
					<div class="formule">
					
						<div class="titreFormule"> <?=$formules[$i]['titre']?> </div>
					
						<div id="corpsFormule"> <?=$formules[$i]['description']?> </div>
						
					</div>
				
				<?php
				
				if ( ($i+1)%$nb==0||$i==count($formules)-1) {
					echo "</div>";
				}
				
			}
		?>
		</div>
	<?php else: ?>
		<h3 class="textesEnTete"> Aucune formule pour le moment. </h3>
	<?php endif; ?>
	</div>
	
	<div id="lastArticles">
	
		<div style="text-align:center; font-weight:bold; font-size:20"> Derniers articles publiés </div>
		
		<?php foreach ($lastArticles as $tuple): 
		
			$fantome=0;
			
			if (Utilitaires::getIdFantome()==$tuple['idUser'])
				$fantome=1;
		?>
			<div id="lastArticle">
			
				<div> <a style="margin:0; padding:0" href="index.php?module=ModArticles&action=voirArticle&idArt=<?=$tuple['idArt']?>"> <?=$tuple['nomArt']?> </a> </div>
			
				<div> Publié le <?=Utilitaires::remplacerDate($tuple['datePub'])?> </div>
				
				<?php if ($fantome==1): ?>
					<div> Auteur: compte supprimé </div>
				<?php else: ?>
					<?php if (isset($_SESSION['login'])&&Utilitaires::estActive()): ?>
						<div> Auteur: <a style="margin:0; padding:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a> </div>
					<?php else: ?>
						<div> Auteur: <?=$tuple['login']?> </div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	
	
	</div>
		
		
		
</div>
