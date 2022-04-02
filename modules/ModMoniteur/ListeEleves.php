<?php  
    if (!defined('CONSTANTE'))
        die("Accès interdit");
     
?>

<h3 class="textesEnTete"> Liste des élèves </h3>

<div class="textesEnTete"> Ici, cherchez la liste des élèves de l'auto-école avec la possibilité de consulter et/ou de modifier leurs compétences et leurs notes de QCM sur le code. </div>

<div id="div_formChercherEleves">
	<h3> Cherchez des élèves par login/nom/prénom </h3>
	
	<form action="" method="POST" id="formChercherEleves">
	
		<div id="critereContainer">
	
			<div class="div_critere_sup">
				<div class="div_criteres">
					<label for="chercherEleveParLogin"> Chercher par login </label>
					<input onchange="toggleInput('loginEleve');" type="checkbox" name="chercherEleveParLogin" <?=$checked[0]?>/>
				</div>
				
				<div class="div_criteres">
					<label for="loginEleve"> Login: </label>
					<input class="inputText" type="text" name="loginEleve" value="<?=$inputLoginEleve?>" id="loginEleve" <?=$disabled[0]?>/>
				</div>
			</div>
			
			<div class="div_critere_sup">
				<div class="div_criteres">
					<label for="chercherEleveParNom"> Chercher par nom </label>
					<input onchange="toggleInput('nomEleve');" type="checkbox" name="chercherEleveParNom" <?=$checked[1]?>/>
				</div>
				
				<div class="div_criteres">
					<label for="nomleve"> Nom: </label>
					<input class="inputText" type="text" name="nomEleve" value="<?=$inputNomEleve?>" id="nomEleve" <?=$disabled[1]?>/>
			</div>
			</div>
			
			<div class="div_critere_sup">
				<div class="div_criteres">	
					<label for="chercherEleveParPrenom"> Chercher par prénom </label>		
					<input onchange="toggleInput('prenomEleve');" type="checkbox" name="chercherEleveParPrenom" <?=$checked[2]?>/>
				</div>
				
				<div class="div_criteres">	
					<label for="loginEleve"> Prenom: </label>
					<input class="inputText" type="text" name="prenomEleve" value="<?=$inputPrenomEleve?>" id="prenomEleve" <?=$disabled[2]?>/>
				</div>
			</div>
		</div>
		
		<input type="submit" class="boutonsForms" value="Chercher"/>
	</form>
	
</div>


<table id="listeElevesTable">
		<tr>
			<td class="tdAlternate2"> Login </td>
			<td class="tdAlternate2"> Nom </td>
			<td class="tdAlternate2"> Prénom </td>
			<td class="tdAlternate2"> Compétences </td>
			<td class="tdAlternate2"> QCM </td>
		</tr>
		
		<?php $i=-1; ?>
		
		<?php foreach($listeEleves as $tuple): 
			$i++;
			$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
		?>
				<tr>
					<td class="tdListeEleves <?=$tdClass?>"> <a class="lienVoirProfil" id="lienTdListeEleves" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a> </td>
					<td class="tdListeEleves <?=$tdClass?>"> <?=$tuple['nom']?> </td>
					<td class="tdListeEleves <?=$tdClass?>"> <?=$tuple['prenom']?> </td>
					<td class="tdListeEleves <?=$tdClass?>"> 
						<a class="lienVoirProfil" href="index.php?module=ModMoniteur&action=compEleve&loginEleve=<?=$tuple['login']?>"> Voir compétences </a> 
					</td>
					<td class="tdListeEleves <?=$tdClass?>">
						<a class="lienVoirProfil" href="index.php?module=ModMoniteur&action=qcmEleve&loginEleve=<?=$tuple['login']?>"> Voir QCM </a>
					</td>
				</tr>
		<?php endforeach; ?>
</table>

<div style="position:relative; bottom:100; left:250" class="suggestions"></div>

<script>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLogins("loginEleve","yes");
	}, 100 );

</script>
