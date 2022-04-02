<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	$page = isset($_POST['page']) ? $_POST['page'] : "1";
	$orderByDate = isset($_POST['orderByDate']) ? $_POST['orderByDate'] : "";
	$orderByUser = isset($_POST['orderByUser']) ? $_POST['orderByUser'] : "";
	
?>

<form class="formulaire" id="confirmerSupMsg" action="index.php?module=ModMessagerie&action=supMsg&idMsg=<?=$idMsg?>" method="post">
				<div id="confirmSupMsgContainer">
					<label id="labelConfirmChk" for="confirmChk"> Je confirme la suppression de ce message </label>
					<input id="confirmChk" type="checkbox" name="confirm"/>
				</div>
				<input type="hidden" name="recu" value="<?= $recu ?>"/>
				<input type="hidden" name="csrfToken" value="<?= $csrfToken ?>"/>
				<input type="hidden" name="page" value="<?=$page?>"/>
				<input type="hidden" name="orderByDate" value="<?=$orderByDate?>"/>
				<input type="hidden" name="orderByUser" value="<?=$orderByUser?>"/>
				<input class="boutonsForms" id="validerConfirmSupMsg" type="submit" value="Valider"/>
</form>
