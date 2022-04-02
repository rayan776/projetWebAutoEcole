<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
?>

<h3 class="textesEnTete"> Réservations </h3>

<div id="legendeRes" style="color:white"> 
	<p> Ici, retrouvez votre emploi du temps, celui de vos collègues et celui des élèves de l'auto-école. Vous pouvez annuler une séance programmée à plus de 24 heures à l'avance, mais également accepter ou refuser les demandes de réservation formulées par les élèves. Vous devez prendre vos décisions plus de 24 heures avant le créneau concerné. </p>
	
	<p> Légende: </p>
	<ul>
		<div style="display:flex; color:orange; margin-bottom:20px;">
			<li> </li>
			<div style="border:1px solid black; height:25px; width:50px; background-color:orange;"> </div> 
			<div style="position:relative; left:40px;"> Réservé </div>
		</div>
		
		<div style="display:flex; color:green; margin-bottom:20px;">
			<li> </li>
			<div style="border:1px solid black; height:25px; width:50px; background-color:green;"> </div> 
			<div style="color:lightgreen; position:relative; left:40px;"> Disponible </div>
		</div>
		
		<div style="display:flex; color:cyan; margin-bottom:20px;">
			<li> </li>
			<div style="border:1px solid black; height:25px; width:50px; background-color:cyan;"> </div> 
			<div style="position:relative; left:40px;"> En attente de confirmation </div>
		</div>

	</ul>
</div>

<?php if (count($edt)==2&&$edt[0]==0): ?>
	<h3 class="textesEnTete"> <?=$edt[1]?> </h3>
<?php else: 
	$trierParMoniteurChecked="";
	$trierParEleveChecked="";
	
	if (!isset($_POST['trierPar'])) {
		$user = $_SESSION['login']['login'];
		$role = "moniteur";
	}
	elseif ($_POST['trierPar']=="moniteur"&&isset($_POST['loginMoniteur'])) {
		$user = $_POST['loginMoniteur'];
		$role = "moniteur";
		$trierParMoniteurChecked="checked";
	}
	elseif ($_POST['trierPar']=="eleve"&&isset($_POST['loginEleve'])) {
		$user = $_POST['loginEleve'];
		$role = "eleve";
		$trierParEleveChecked="checked";
	}

?>

<?php if (!empty($msgRetour)): ?>
	<h3 class="textesEnTete"> <?=$msgRetour?> </h3>
<?php endif; ?>


<h3 id="trierParReserv" class="textesEnTete"> Emploi du temps </h3>

<form method="POST" action="index.php?module=ModMoniteur&action=gererReservations">
	
	<div id="trierReservations">

		<span class="spanEdt"> Moniteur </span>
		<input type="radio" name="trierPar" value="moniteur" <?=$trierParMoniteurChecked?>/>
				
		<select id="loginMoniteur" name="loginMoniteur" style="margin-right:100;">
			<?php foreach ($moniteurs as $moniteur): ?>
				<option value="<?=$moniteur['login']?>"> <?=$moniteur['prenom']?> (<?=$moniteur['login']?>) </option>
			<?php endforeach; ?>
		</select>

		<button class="boutonsForms" type="button" onclick="document.getElementById('loginMoniteur').value='<?=$_SESSION['login']['login']?>';"> Mon login </button>
		
		<span class="spanEdt"> Eleve </span>
		<input type="radio" name="trierPar" value="eleve" <?=$trierParEleveChecked?>/>
				
		<input id="loginEleve" class="inputText" style="margin:0; margin-right:200;" type="text" name="loginEleve" value="<?=$loginEleve?>"/>


		<span class="spanEdt"> Semaine </span>
		
		<span class="btnChangerSem" onclick="changerSem(0);"> << </span>
		
		<select id="sem" name="sem">
			<?php foreach ($semaines as $lundi => $nomSemaine): ?>
				<option value="<?=$lundi?>"> <?=$nomSemaine?> </option>
			<?php endforeach; ?>
		</select>
		
		<span class="btnChangerSem" onclick="changerSem(1);"> >> </span>
		
		<input class="boutonsForms" style="width:50px;" type="submit" value="OK"/>
		
	</div>
	
	<div style="margin-top:15" class="suggestions"></div>
</form>

<h3 class="textesEnTete"> Emploi du temps de <?=$user?> (<?=$role?>) </h3>

<table id="reservations">

	<tr>
		<td class="reservationsTitres"> 
			Horaires
		</td>
	
		<?php foreach ($joursSemaine as $jour): ?>
			<td class="reservationsTitres">
				<?=Utilitaires::dateToJour($jour)?>
			</td>
		<?php endforeach; ?>
	</tr>
	
	<?php $numeroCase=1; ?>
	
	<?php for ($heure=8; $heure<=18; $heure++): ?>
		<tr>
			<td class="horaires">
				<?=$heure . "h - " . ($heure+1) . "h"?>
			</td>
			
			<?php foreach ($joursSemaine as $jour):
				
				$lundiSem = Utilitaires::getDateLundi($jour);
				$j = date("w", strtotime($jour)); 
				$idTd = "h$heure" . "j$j";
				
			?>
				<td id="<?=$idTd?>">
					<?php if ($queDisponible==0): ?>
						<?php if ($edt[$numeroCase-1]): 
							$objSeance=$edt[$numeroCase-1];
						?>
							<?php 
								
								$csrfToken=Tokens::insererTokenForm();


									if ($objSeance->confirmer=="1") {
										$bgcolor="orange";
										$color="black";
									}
									else {
										$bgcolor="cyan";
										$color="black";
									}
									
									echo "<style> #$idTd { background-color:$bgcolor; } #$idTd a { color:$color; } #$idTd a:hover { color:darkred; } </style>";
									
									if ($role=="eleve"):
									
										$objRes = Utilitaires::getLoginPrenomParIdEleveOuMoniteur(0, $objSeance->idMoniteur);
										$loginMoniteur=$objRes->login;
										$prenomMoniteur=$objRes->prenom;
									
									?>
										
										Moniteur: <a style="margin:0;" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$loginMoniteur?>"> <?=$prenomMoniteur?> (<?=$loginMoniteur?>) </a>
									<?php else: 
										
										
									?>
										Eleve: <a style="margin:0;" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$objSeance->login?>"> <?=$objSeance->prenom?> (<?=$objSeance->login?>) </a>
									<?php endif;
										if (Utilitaires::reservationPossible($jour,$heure)==1&&Utilitaires::getIdMoniteur(Utilitaires::getIdUser($_SESSION['login']['login']))==$objSeance->idMoniteur) {
											if ($objSeance->confirmer==1) {
												?>
													<div style="display:flex">
														<form method="POST" action="index.php?module=ModMoniteur&action=gererReservations">
															<input type="hidden" name="sem" value="<?=$lundiSem?>"/>
															<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
															<input type="hidden" name="idSeance" value="<?=$objSeance->idSeance?>"/>
															<input style="position: relative;
left: 40" class="boutonsForms boutonReserver" type="submit" name="annuler" value="Annuler"/>
														</form>
														
														<button class="boutons boutonRep" type="button" onclick="reporter(<?=$objSeance->idSeance?>, '<?=$csrfToken?>');"> Reporter </button>
													</div>
													
												<?php
											}
											else {
												?>
													<form method="POST" action="index.php?module=ModMoniteur&action=gererReservations">
														<input type="hidden" name="sem" value="<?=$lundiSem?>"/>
														<input type="hidden" name="csrfToken" value="<?=$csrfToken?>"/>
														<input type="hidden" name="idSeance" value="<?=$objSeance->idSeance?>"/>
														<div style="display:flex">
															<input class="boutonsForms boutonReserver" type="submit" name="accepter" value="Accepter"/>
															<input class="boutonsForms boutonReserver" type="submit" name="refuser" value="Refuser"/>
														</div>
													</form>
												<?php
											}
										}
									
								
								
							?>
						<?php else:
					
								echo "<style> #$idTd { background-color:green; } </style>";
								
						endif; ?>
					<?php else: ?>
						<?php
							echo "<style> #$idTd { background-color:green; } </style>";
						?>
					<?php endif; ?>
				</td>
			<?php $numeroCase++; endforeach; ?>

		</tr>
	<?php endfor; ?>
	
	

</table>

<?php endif; ?>

<h3 class="textesEnTete" style="margin-top:30"> Vos prochaines séances </h3>

<div id="allReservations">

	<div>
		<div style="margin-bottom:15; font-size:20"> Séances prévues </div>
		
		<?php if (count($allReservations[1])==0): ?>
			Aucune séance n'est prévue.
		<?php else: ?>
		
			<table>
				<tr class="tableTitres">
					<td class="tdAlternate2"> Eleve </td>
					<td class="tdAlternate2"> Date </td>
					<td class="tdAlternate2"> Horaires </td>
				</tr>
			<?php $i=-1; ?>				
			<?php foreach ($allReservations[1] as $tuple): 
			
				$i++;
				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";				
			?>
				<tr>
					
					<td class="<?=$tdClass?>"> <?=$tuple['prenom']?> (<a style="margin:0; padding:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a>) </td>
					<td class="<?=$tdClass?>"> <?=Utilitaires::remplacerDateSansHeure($tuple['dateSeance'])?> </td> 
					
					<td class="<?=$tdClass?>"> <?=$tuple['heureDeb']?>h à <?=$tuple['heureFin']?>h </td>
				</tr>
			<?php endforeach; ?>
			</table>
		
		<?php endif; ?>
		
		<div style="margin-bottom:15; font-size:20" id="demandesEnAttente"> Demandes en attente </div>
		
		<?php if (count($allReservations[0])==0): ?>
			Aucun élève ne souhaite réserver un créneau horaire avec vous pour le moment.
		<?php else: ?>
		
			<table>
				<tr class="tableTitres">
					<td class="tdAlternate2"> Eleve </td>
					<td class="tdAlternate2"> Date </td>
					<td class="tdAlternate2"> Horaires </td>
				</tr>
			<?php $i=-1; ?>
			<?php foreach ($allReservations[0] as $tuple): 
			
				$i++; 
				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";				
			?>
				<tr>
					<td class="<?=$tdClass?>"> <?=$tuple['prenom']?> (<a style="margin:0; padding:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a>) </td> 
					<td class="<?=$tdClass?>"> <?=Utilitaires::remplacerDateSansHeure($tuple['dateSeance'])?> </td> 
					<td class="<?=$tdClass?>"> <?=$tuple['heureDeb']?>h à <?=$tuple['heureFin']?>h </td>
				</tr>
			<?php endforeach; ?>
			
			</table>
		
		<?php endif; ?>
	</div>

</div>

<SCRIPT>
	var tid = setInterval( function () {
	    if ( document.readyState !== 'complete' ) return;
	    clearInterval( tid );
	    document.getElementById("sem").value="<?=$lundiSem?>";
	    getListLogins("loginEleve","yes");
	}, 100 );
	
	
</SCRIPT>

<div id="reports"></div>
