<?php if (!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Les articles </h3>


<h3 class="textesEnTete"> Trier par... </h3>

<form method="POST" action="index.php?module=ModArticles&action=chercher">
	<div id="trierArticles">
	
		<div style="margin-top:10">
			<input type="checkbox" name="trierPar[]" value="cat" <?=$checkedCat?>/>
			<p> Catégorie </p>
			<select name="trierParCat" id="trierParCat" style="position:relative; right:10">
				<?php foreach ($categories as $cat): ?>
					<option value="<?=$cat['idCat']?>"> <?=$cat['category']?> </option>
				<?php endforeach; ?>
			</select>
			
			<input style="margin-left:517" type="checkbox" name="trierPar[]" value="aut" <?=$checkedAut?>/>
			<p> Auteur </p>
			<input id="autArticle" type="text" class="inputText" style="width: 600;position: relative;left: 13" name="trierParAut" value="<?=$trierParAut?>"/>
			
		</div>
		
		<script>
			document.getElementById("trierParCat").value=<?=$category?>;
		</script>
		
		
		
			
		<div>
			<input type="checkbox" name="trierPar[]" value="titre" <?=$checkedTitre?>//>
			<p> Titre </p>
			<input type="text" style="position: relative;left: 30;width: 600" class="inputText" name="titreArt" value="<?=$trierParTitre?>"/>*
			
			<input style="margin-left:100" type="checkbox" name="trierPar[]" value="cont" <?=$checkedContenu?>//>
			<p> Contenu </p>
			<input style="width:600" type="text" class="inputText" name="contenuArt" value="<?=$contenuArt?>"/>
		</div>
		
		
		<div style="margin-bottom:0"> 
			<input type="checkbox" name="trierPar[]" value="date" <?=$checkedDate?>//>
			<p> Date </p>
			
			<p> Du </p>
			<input type="date" name="dateDeb" value="<?=$dateDeb?>"/>
			
			<p> Au </p>
			<input type="date" name="dateFin" value="<?=$dateFin?>"/>
			
			<select name="ordreDate" id="ordreDate" style="position:relative; left:10">
				<option value="DESC"> Du plus récent au moins récent </option>
				<option value="ASC"> Du moins récent au plus récent </option>
			</select>
		</div>
		
		<script>
			document.getElementById("ordreDate").value='<?=$ordreDate?>';
		</script>
	
		<input type="submit" class="boutonsForms" value="Rechercher"/>
	</div>
	
	<div class="suggestions"></div>

</form>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>

<?php if (count($liste)>0): ?>
	<h3 class="textesEnTete"> <?=count($liste)?> articles </h3>
	
	<div id="listeArticles">
	<?php
		$nb=3; // nombre d'articles par ligne
			
		for ($i=0; $i<count($liste); $i++) {
			
			$fantome=0;
		
			if (Utilitaires::getIdFantome()==$liste[$i]['idUser'])
				$fantome=1;
				
		
			if ($i==0||$i>0&&$i%$nb==0) {
				echo "<div style='display:flex'>";
			}
			
			?>
				<div class="boxListeArticles">
				
					<h3 style="text-align:center"> <a style="padding:0" href="index.php?module=ModArticles&action=voirArticle&idArt=<?=$liste[$i]['idArt']?>"> <?=$liste[$i]['nomArt']?> </a> </h3>
					
					<h4> Catégorie: <?=$liste[$i]['category']?> </h4>
					<?php if ($fantome==0): ?>
					<h4> Auteur: <a href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$liste[$i]['login']?>"> <?=$liste[$i]['login']?> </a> </h4>
					<?php else: ?>
					<h4> Compte supprimé </h4>
					<?php endif; ?>
					<h4> Posté le: <?=Utilitaires::remplacerDate($liste[$i]['datePub'])?> </h4>
					
				</div>
			
			<?php
			
			
			
			if ( ($i+1)%$nb==0||$i==count($liste)-1) {
				echo "</div>";
			}
			
		}
	?>
	</div>
<?php else: ?>
	<h3 class="textesEnTete"> Aucun article n'a été trouvé. </h3>
<?php endif; ?>

<script>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLogins("autArticle","no");
	}, 100 );
</script>
