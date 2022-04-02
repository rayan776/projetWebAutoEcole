<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	$page = isset($_POST['page']) ? $_POST['page'] : "1";
	$orderByDate = isset($_POST['orderByDate']) ? $_POST['orderByDate'] : "";
	$orderByUser = isset($_POST['orderByUser']) ? $_POST['orderByUser'] : "";
	$ids = (isset($_POST['idMessages'])&&is_array($_POST['idMessages'])) ? $_POST['idMessages'] : array();
	$recu = (isset($_POST['recu'])) ? $_POST['recu'] : "true";
	
	if (count($ids) == 0) {
		$action = ($recu == "true") ? "msgRec" : "msgEnv";
		?>
			<h3 class="textesEnTete"> Vous n'avez rien coché. </h3>
			
			<form method="post" action="index.php?module=ModMessagerie&action=<?=$action?>">
				<input type="hidden" name="page" value="<?=$page?>"/>
				<input type="hidden" name="orderByDate" value="<?=$orderByDate?>"/>
				<input type="hidden" name="orderByUser" value="<?=$orderByUser?>"/>
				<input type="submit" class="boutonsForms" value="Retour en arrière"/>
			</form>
		<?php
	}else{
	
?>

<form class="formulaire" id="confirmerSupMsg" action="index.php?module=ModMessagerie&action=supprimerGroupeDeMessages" method="post">
				<div id="confirmSupMsgContainer">
					<label id="labelConfirmChk" for="confirmChk"> Je confirme la suppression des messages cochés </label>
					<input id="confirmChk" type="checkbox" name="confirm"/>
				</div>
				<input type="hidden" name="recu" value="<?= $recu ?>"/>
				<input type="hidden" name="csrfToken" value="<?= $csrfToken ?>"/>
				<input type="hidden" name="orderByDate" value="<?=$orderByDate?>"/>
				<input type="hidden" name="orderByUser" value="<?=$orderByUser?>"/>
				<?php foreach ($ids as $id): ?>
				<input type="hidden" name="idMessagesASupprimer[]" value="<?=$id?>"/>
				<?php endforeach; ?>
				<input class="boutonsForms" id="validerConfirmSupMsg" type="submit" value="Valider"/>
</form>

<?php } ?>
