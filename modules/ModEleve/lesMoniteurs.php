<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
?>

<h3 class="textesEnTete"> Les moniteurs </h3>


<div style="color:white; margin-bottom:30"> Retrouvez ici la liste des moniteurs de l'auto-école. </div>

<div style="color:white; display:flex; margin-bottom:25">
	<div> Recherchez un login </div>
	<input style="margin:0; position:relative; left:10; bottom:5" class="inputText" type="text" name="loginMoniteur" id="loginMoniteur"/>
</div>

<div id="lesMoniteurs">
<?php if(count($lesMoniteurs)>0): ?>
	<table>
		<tr class="tableTitres">
			<td class="tdAlternate2"> Login </td>
			<td class="tdAlternate2"> Prenom </td>
			<td class="tdAlternate2"> Nom </td>
		</tr>
		
		<?php $i=-1; ?>
		
		<?php foreach ($lesMoniteurs as $tuple):
			$i++;
			$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
		?>
		<tr>
			
			<td class="<?=$tdClass?>">
				<a style="padding:0;margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a>
			</td>
			
			<td class="<?=$tdClass?>">
				<?=$tuple['prenom']?>
			</td>
			
			<td class="<?=$tdClass?>">
				<?=$tuple['nom']?>
			</td>
		</tr>
			
		<?php endforeach; ?>

	</table>
	<?php else: ?>
		
	<?php endif; ?>
</div>

<SCRIPT>
	$(document).ready(function(){
	
		$("#loginMoniteur").keyup(function() {
		
			var login = $(this).val().trim();
			
			if (login != '') {
			
				$.ajax({
					url:"models/ajaxSuggestionsLogin.php",
					method:"POST",
					data:{login:login, inputName:"loginMoniteur", chercherEleve:"moniteur"},
					success:function(data) {
						$("#lesMoniteurs").html(data);
					}
			
				});
			}
			else {
				$.ajax({
					url:"models/ajaxSuggestionsLogin.php",
					method:"POST",
					data:{login:"", inputName:"loginMoniteur", chercherEleve:"moniteur"},
					success:function(data) {
						$("#lesMoniteurs").html(data);
					}
			
				});
			}	

		});
		
		
	});
</SCRIPT>
