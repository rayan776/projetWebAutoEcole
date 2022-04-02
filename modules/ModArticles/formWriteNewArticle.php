<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
	
?>

<h3 class="textesEnTete"> <?=$msgEnTete?> </h3>

<h3 id="msgRetour" class="textesEnTete">
	<ul>
	<?php if (count($msgRetour)>0): ?>
		<?php foreach ($msgRetour as $msg): ?>
			<li>
				<?=$msg?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
	</ul>
</h3>

<div id="redigerArticle">

	<form method="POST" action="<?=$action?>">
	
		<input type="hidden" name ="csrfToken" value="<?=$csrfToken ?>"/>
		<?php if ($edit==1): ?>
			<input type="hidden" name="idArt" value="<?=$idArt?>"/>
		<?php endif; ?>
	
		<div id="titre"> Titre (100 caractères maxi.) </div>
		
		<input style="width:1200" id="titreArt" type="text" class="inputText" name="titreArt" value="<?=$titreArt?>"/>
		
		<div id="category"> Catégorie </div>
		
		<select name="catArticle" id="catArticle">
			<?php foreach ($categories as $cat): ?>
				<option value="<?=$cat['idCat']?>"> <?=$cat['category']?> </option>
			<?php endforeach; ?>
		</select>
		
		<script>
			document.getElementById("catArticle").value="<?=$catArticle?>";
		</script>
		
		<div id="divTextArea">
	
			<textarea id="editor" name="contenuArticle"></textarea>
		</div>
		
		<div style="margin-top:50">
			<h4> Gestion des droits </h4>
			<?php if (Utilitaires::getRoleCurrentUser()=="admin"): ?>
			<h3> Si un moniteur n'a pas les droits de lecture sur un article, les autres droits seront désactivés automatiquement. </h3>
			<?php endif; ?>
		</div>
			
		<div id="droits">
			
			<?php if (Utilitaires::getRoleCurrentUser()=="eleve"): ?>
				Les moniteurs auront tous les droits sur votre article.
				Les élèves pourront lire et commenter.
				Les visiteurs pourront seulement lire.
			<?php else: ?>
			
				<?php
					if ($edit==0) {
				
						$droits = array("lire", "supprimer", "commenter", "modifier");
									
						if (Utilitaires::getRoleCurrentUser()=="admin") {
							$roles = array("moniteur" => "moniteurs", "eleve" => "élèves", "visiteur" => "visiteurs");
						}
						else {
							$roles = array("eleve" => "élèves", "visiteur" => "visiteurs");					
						}
						
						foreach ($roles as $keyRole => $nomRole) {
							echo "<div>";
								echo "<h4> Un $keyRole peut... </h4>";
								
								foreach ($droits as $droit) {
									$checked="checked";
										if ($keyRole=="moniteur"||($keyRole=="eleve"&&($droit=="lire"||$droit=="commenter"))||($keyRole=="visiteur"&&$droit=="lire")) {
										
										
											echo "<p> $droit l'article </p>";
											echo "<input type='checkbox' id='$keyRole$droit' class='droits$keyRole' name='droits[$keyRole][$droit]' value='1' $checked/>";
										}
									
								}
							echo "</div>";
							
						}
					
					}
					else {
					
						$roles=array("3"=>"eleve","8"=>"visiteur","4"=>"moniteur");
						
						foreach ($droits as $tuple) {
							echo "<div class='modifArt'>";
							$checkedLire = ($tuple['lire']==1) ? "checked" : "";
							$checkedMod = ($tuple['modifier']==1) ? "checked" : "";
							$checkedSup = ($tuple['supprimer']==1) ? "checked" : "";
							$checkedCom = ($tuple['commenter']==1) ? "checked" : "";
						
							?>
							
								<?php if ($roles[$tuple['idRole']]=="moniteur"):
									
									if (Utilitaires::getRoleCurrentUser()=="admin"):
										
								?>
								
									<h4> Un <?=$roles[$tuple['idRole']]?> peut... </h4>
									
									<div> lire l'article </div>
									<input id='moniteurlire' type='checkbox' name='droits[moniteur][lire]' value='1' <?=$checkedLire?>/>
									
									<div> modifier l'article </div>									
									<input class='droitsmoniteur' type='checkbox' name='droits[moniteur][modifier]' value='1' <?=$checkedMod?>/>
									
									<div> supprimer l'article </div>
									<input class='droitsmoniteur' type='checkbox' name='droits[moniteur][supprimer]' value='1' <?=$checkedSup?>/>
								
									<div> commenter l'article </div>
									<input class='droitsmoniteur' type='checkbox' name='droits[moniteur][commenter]' value='1' <?=$checkedCom?>/>
							
										
									<?php endif; ?>
								<?php else: ?>
								
									<h4> Un <?=$roles[$tuple['idRole']]?> peut... </h4>
									
									<div> lire l'article </div>
									<input type='checkbox' name='droits[<?=$roles[$tuple['idRole']]?>][lire]' value='1' <?=$checkedLire?>/>
								
									<?php if ($roles[$tuple['idRole']]=="eleve"): ?>
									
										<div> commenter l'article </div>
										<input type='checkbox' name='droits[<?=$roles[$tuple['idRole']]?>][commenter]' value='1' <?=$checkedCom?>/>
									
									<?php endif; ?>
								
								<?php endif; ?>
							<?php
						
							echo "</div>";
						}
					}
				
				?>
			<?php endif; ?>
		
			
		</div>
		
		<input class="boutonsForms" type="submit" value="<?=$valSubmit?>"/>
	
	
	</form>


</div>

<script>
	
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );
	    
	   <?php if ($edit==1): ?>
	   	$.ajax({
		   url: 'models/ajaxArt.php',
		   type: 'POST',
		   data: {idArt:<?=$idArt?>},
		   success: function(response){

			$(".wysibb-body").html(response);

		   }
		});
	   <?php endif; ?>
	   
	   var moniteurLire = document.getElementById("moniteurlire");
	   if (moniteurLire!=null) {
	 	  var chkmoniteur = document.getElementsByClassName("droitsmoniteur");
	   		
	   		moniteurLire.addEventListener("change", function() {
		   		if (!this.checked) {
		   			for (var i=0; i<chkmoniteur.length; i++) {
		   				chkmoniteur[i].checked=false;
		   			}
		   		}
	   		
	   		});
	   		
	   		for (var i=0; i<chkmoniteur.length; i++) {
	   			chkmoniteur[i].addEventListener("change", function() {
	   				if (this.checked&&!moniteurLire.checked) {
	   					this.checked=false;
	   				}
	   			});
   			}	
	   	
	   }
	}, 100 );
	
	
	
</script>
