<?php

if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h3 class="textesEnTete"> Liste des bannis </h3>

<?php if (!empty($msgRetour)): ?>
	<h3 id="valRetourApresModifDroit" class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>

<?php if (count($bannis)>0): ?>

<div id="listeBans">

	<form method="POST" action="index.php?module=ModAdmin&action=menuBan">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>


		<table>
		
			<tr class="titresTr">
				<td class="tdAlternate2"> Login </td>
				<td class="tdAlternate2"> Banni par </td>
				<td class="tdAlternate2"> Motif </td>
				<td class="tdAlternate2"> Date de fin </td>
				<td class="tdAlternate2"> </td>
			</tr>
			
			<?php $i=-1;?>
			
			<?php foreach ($bannis as $ban): 
				$i++;
				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
			?>
			
				<tr>
					<td class="<?=$tdClass?>"> <a style="margin:0; padding:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$ban['login']?>"> <?=$ban['login']?> </a> </td>
					
					<td class="<?=$tdClass?>"> <a style="margin:0; padding:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$banniPar[$ban['idBan']]?>"> <?=$banniPar[$ban['idBan']]?> </a> </td>
					
					<td class="<?=$tdClass?>"> <?=$ban['motif']?> </td>
					
					<td class="<?=$tdClass?>"> <?=Utilitaires::remplacerDate($ban['dateFin'])?> </td>
					
					<td class="<?=$tdClass?>"> <input style="margin:0; width:50" type="checkbox" class="chkMenuBan" name="idBan[]" value="<?=$ban['idBan']?>"/> </td>
				
				</tr>
			
			<?php endforeach; ?>
		
		</table>
		
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(true, 'chkMenuBan');"> Tout cocher </button>
		<button class="boutonsForms boutonsSecondaires" type="button" onclick="toutCocher(false, 'chkMenuBan');"> Tout décocher </button>	
		
		<input type="submit" style="margin:0; margin-top:20; width:237" class="boutonsForms" name="deleteBans" value="Débannir"/>

	</form>

</div>

<?php else: ?>
	<h3 class="textesEnTete"> Aucun bannissement n'existe actuellement sur le site. </h3>
<?php endif; ?>

<h3 class="textesEnTete"> Bannir quelqu'un </h3>

<h4 id="reglesBan" class=textesEnTete"> 
	<ul>
		<li> Si l'utilisateur est déjà banni, alors les informations relatives à son bannissement seront écrasées par les nouvelles. </li> 
		<li> L'administrateur peut bannir qui il veut. </li>
		<li> Un moniteur ne peut bannir que des élèves. </li> 
		<li> Un bannissement prend fin à minuit, le jour de sa date de fin. </li> 
	</ul>
</h4>

<form action="index.php?module=ModAdmin&action=menuBan" method="POST">
	<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
	<div id="bannirQqn">
		<div style="display:flex">
			<div style="position: relative; top: 5"> Login </div>
			
			<input id="loginToBan" style="position: relative; left: 50" class="inputText" type="text" name="loginToBan"/>
		</div>
		
		<div style="display:flex">
			<div style="position: relative;top: 15;"> Date de fin </div>
			
			<input style="position: relative; left: 12; top: 10;" name="dateFinBan" type="date"/>
		</div>
		
		<div style="margin-top: 40; margin-bottom: 10;"> Motif </div>
		
		<textarea name="motifBan" rows="10" cols="50"></textarea>
		
		<input style="margin:0; margin-top:20" type="submit" class="boutonsForms" name="bannir" value="Bannir"/>
	</div>
</form>

<div style="position:relative; right:150; bottom:380" class="suggestions"></div>


<script>

	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLogins("loginToBan","no");
	}, 100 );
</script>
