<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	
	$action = ($recu == "true") ? "msgRec" : "msgEnv";
	$tableJs = ($recu == "true") ? "recevoir" : "envoyer";
	
?>

<div id="formContainer">

	<form method="POST" action="index.php?module=ModMessagerie&action=<?=$action?>">
		<div id="trierContainer">
			<label id="labelOrderByDate" for="orderByDate">Trier par date: </label>
			<select id="orderByDate" name="orderByDate">
				<?php if ($orderByDate == "DESC"): ?>
					<option value="DESC"> Plus récent au plus ancien </option>
					<option value="ASC"> Plus ancien au plus récent </option>
				<?php else: ?>
					<option value="ASC"> Plus ancien au plus récent </option>
					<option value="DESC"> Plus récent au plus ancien </option>
				<?php endif; ?>
			</select>
			
			<label id="labelOrderByUser" for="orderByUser"> Trier par <?= $destOuExp ?> </label>
			<input id="orderByUser" class="inputText" type="text" name="orderByUser" value="<?= $orderByUser ?>"/>
			
			<input type="hidden" name="page" value="<?=$page?>"/>
			<input class="boutonsForms" id="submitTrierMessages" type="submit" value="Trier"/>
			
		</div>
	</form>
	
	<button onclick="toutCocher(true, 'cocherMsg');" class="boutonsForms boutonCocher"> Tout cocher </button>
	<button onclick="toutCocher(false, 'cocherMsg');" class="boutonsForms boutonCocher"> Tout décocher </button>
		
	<div id="groupeMsg">
		
		<form method="POST" action="" id="groupeDeMessages">
				<input class="boutonsForms" id="submitSupprimerGroupeDeMessages" formaction="index.php?module=ModMessagerie&action=confSupprimerGroupeDeMessages" type="submit" value="Supprimer"/>
				<?php if ($recu == "true"): ?>
				<input class="boutonsForms" id="submitMarquerGroupeMessagesCommeLu" formaction="index.php?module=ModMessagerie&action=marquerCommeLu" type="submit" value="Marquer comme lu"/>
				<?php endif; ?>
				<input type="hidden" name="orderByDate" value="<?=$orderByDate?>"/>
				<input type="hidden" name="orderByUser" value="<?=$orderByUser?>"/>
				<input type="hidden" name="page" value="<?=$page?>"/> 
				<input type="hidden" name="recu" value="<?=$recu?>"/>
		</form>
	</div>	

</div>

<div class="suggestions suggestionsMsg"></div>

<script>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );       
	    getListLoginsMsg("orderByUser","<?=$tableJs?>")
	}, 100 );
</script>
