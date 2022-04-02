<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
?>

<h3 class="textesEnTete"> Supprimer votre compte </h3>

<?php if (!empty($msg)): ?>
<h3 class="textesEnTete"> <?=$msg?> </h3>
<?php endif; ?>

<div id="warningDeleteAccount">
	<div style="text-align:center">
		<h1 style="border-bottom:5px solid black; width:1700"> /!\ ATTENTION /!\ </h1>
	</div>
	<h2> Cette opération est irréversible et entraînera la disparition complète de votre compte sur le site. Toutes vos traces seront supprimées, sans exception. </h2>
	<h2> Vos articles et vos commentaires peuvent néanmoins rester présents si vous le souhaitez. </h2>
	
	<h2> Si vous êtes sûr de vouloir continuer, veuillez taper votre mot de passe, le confirmer, cocher la case de confirmation et cliquer sur le bouton. </h2>
	
	<form style="margin-top:50; margin-left:200" method="POST" action="index.php?module=ModEspaceMembre&action=deleteAccount">
		<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
		
		<div class="divDel" style="display:flex">
			<div class="labelDiv"> Mot de passe </div>
			<input id="mdpActu" class="inputText" style="position:relative; left:160" type="password" name="mdpActu"/>
			
			<div style="position:relative; left:200; top:5" id="msgMdpActu"></div>
		</div>
		
		
		<div class="divDel" style="display:flex">
			<div class="labelDiv"> Confirmation mot de passe </div>
			<input id="newMdpConf" class="inputText" style="position:relative; left:55" type="password" name="confMdpActu"/>
			
			<div style="position:relative; left:200; top:5" id="msgConfMdp"></div>
		</div>
		
		<div class="divDel" style="display:flex">
		
			<div class="labelDiv"> Garder mes articles et commentaires </div>
			<input type="checkbox" name="garderArticlesCom"/>
		
			<div class="labelDiv"> Je confirme vouloir supprimer mon compte </div>
			<input type="checkbox" name="conf"/>
		</div>
		
		<input type="submit" class="boutonsForms" style="width:250; position:relative; left:550; margin-top:50" value="Je supprime mon compte"/>
		
	</form>
</div>

<script>
var tid = setInterval( function () {
		    if ( document.readyState !== 'complete' ) return;
		    clearInterval( tid );       
		    getPwdActu("mdpActu",1);
		}, 100 );
</script>
