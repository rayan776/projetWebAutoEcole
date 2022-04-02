<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h2 class="textesEnTete"> Liste des utilisateurs </h2>
				
<table id="tableMenuAdmin">
	<tr class="trMenuAdmin" id='tab_users_titres'>
		<td class="tdMenuAdmin"> Login </td>
		<td class="tdMenuAdmin"> Role </td>
		<td class="tdMenuAdmin"> Activer </td>
	</tr>
	
	<?php foreach($tab as $user): 
		$nomRole = $user['nomRole']; 
	?>
		<tr class="trMenuAdmin">
			<td class="tdMenuAdmin" id="loginNameMenuAdmin"> <?=$user['login'] ?> </td>
			<td class="tdMenuAdmin"> <?=$nomRole ?> </td>
			<?php
				$idUser = $user['idUser'];
				if ($user['active'] == 0) {
					$csrfToken = Tokens::insererTokenForm();
					$valeurActiveOuPas = "<div class='boutonActiver boutonsForms'> <form action='index.php?module=ModAdmin&action=activerCompte' method='post'> <input type='hidden' name='csrfToken' value='$csrfToken'/> <input type='hidden' name='idUser' value='$idUser'/> <input type='hidden' name='nomRole' value='$nomRole'/> <input class='activer boutonsForms' type='submit' value='Cliquez pour activer'/> </form> </div>";
				}
				else {
					$valeurActiveOuPas = "Déjà activé";
				}


			?>
			<td class="tdMenuAdmin"> <?=$valeurActiveOuPas ?> </td>
		

		</tr>
	<?php endforeach; ?>

</table>
