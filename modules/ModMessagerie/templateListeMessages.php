<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

?>
	<h3 class="textesEnTete"> Messages <?=$texteh3?> </h3>
	
	<?php if (count($liste) == 0): ?>
			<h3 class="textesEnTete"> Pas de messages à afficher. </h3>
	<?php else:
		$chaineCompterMsg = ""; 
		$premierNumeroMsg = (($page - 1) * 10) + 1;
		$dernierNumeroMsg = $premierNumeroMsg + count($liste) - 1;
		
		if ($dernierNumeroMsg - $premierNumeroMsg > 0)
			$chaineCompterMsg = "De $premierNumeroMsg à $dernierNumeroMsg";
			
		$chaineCompterMsg = htmlspecialchars($chaineCompterMsg, ENT_QUOTES);
	?>
		<span class="listeMsgSpan"> <?= $countMsg ?> message(s) </span>
		<?php if ($dernierNumeroMsg - $premierNumeroMsg > 0): ?>
			<span class="listeMsgSpan"> <?= $chaineCompterMsg ?> </span>
		<?php endif; ?>
	
	<?php if ($recu == "true"): ?>
		<span class="listeMsgSpan"> Les nouveaux messages apparaissent en gras. <?php if ($newMsg > 0): ?> Vous avez <?= $newMsg ?> nouveau(x) messages. <?php endif; ?></span>
	
	<?php endif; ?>
	
	<?php require_once "trierMessages.php"; ?>
	
	<div id="pagesMsgContainer">
		<?php
			$nbPages = ceil($countMsg/10);
			$orderByDate = isset($_POST['orderByDate']) ? $_POST['orderByDate'] : "";
			$orderByUser = isset($_POST['orderByUser']) ? $_POST['orderByUser'] : "";
			
			$action = ($recu == "true") ? "msgRec" : "msgEnv";
			
			for ($i=1; $i<=$nbPages; $i++):
		?>
			<form method="POST" action="index.php?module=ModMessagerie&action=<?=$action?>">
				<input type="hidden" name="page" value="<?=$i?>"/>
				<input type="hidden" name="orderByDate" value="<?=$orderByDate?>"/>
				<input type="hidden" name="orderByUser" value="<?=$orderByUser?>"/>
				<input class="submitPageListeMsg boutonsForms" id="page<?=$i?>" type="submit" value="Page <?=$i?>"/>
			</form>
			<?php if ($page == $i): ?>
			<style> #page<?=$i?> { color:red; }</style>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
	
	<table id="tableListeMsg">
		<tr class="trListeMsg" id="nomsColonnesListeMsg">
			<td class="tdListeMsg tdAlternate2"> Titre </td>
			<td class="tdListeMsg tdAlternate2"> <?=$texteh4?> </td>
			<td class="tdListeMsg tdAlternate2"> Date </td>
			<td class="tdListeMsg tdAlternate2"> </td>
		</tr>
		
		<?php $i=-1; ?>
		
		<?php foreach ($liste as $msg): 
		
			$i++;
			$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
			
			if ($recu == "true") {
				if ($msg['dejaLu']==0) {
					$href="<strong> <a class='lienTitreMsg' href='index.php?module=ModMessagerie&action=afficherMessage&recu=$recu&idMsg=" . $msg['idMessage'] . "'>" . $msg['titreMsg'] . "</a> </strong>";
				}
				else {
					$href="<a class='lienTitreMsg' href='index.php?module=ModMessagerie&action=afficherMessage&recu=$recu&idMsg=" . $msg['idMessage'] . "'>" . $msg['titreMsg'] . "</a>";
				}
			}
			else {
				$href="<a class='lienTitreMsg' href='index.php?module=ModMessagerie&action=afficherMessage&recu=$recu&idMsg=" . $msg['idMessage'] . "'>" . $msg['titreMsg'] . "</a>";
			}
		?>
		
			<tr class="trListeMsg">
				<td class="tdListeMsg <?=$tdClass?>"> <?=$href?> </td>
				<td class="tdListeMsg <?=$tdClass?>">
					<?php if ($msg['login']=="robot"): ?>
						<span style="font-size:22"> robot </span>
					<?php else: ?>
						<a class="lienVoirProfil" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$msg['login']?>"> <?=$msg['login']?> </a>
					<?php endif; ?>
				</td>
				<td class="tdListeMsg <?=$tdClass?>">
									<?php
										$timestamp = strtotime($msg['dateMsg']);
										$date = date("d/m/Y H:i:s", $timestamp);
										$explodeDate = explode(" ", $date);
										
										echo "Le " . $explodeDate[0] . " à " . $explodeDate[1];
									?> 
								 </td>
				
							<td class="tdListeMsg <?=$tdClass?>">
								<input class="cocherMsg" form="groupeDeMessages" type="checkbox" name="idMessages[]" value="<?=$msg['idMessage']?>" />
							</td>		
			</tr>

		<?php endforeach; endif; ?>
	</table>
