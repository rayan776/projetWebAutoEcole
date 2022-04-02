<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

?>



				<div id='message'>
						<div id='enTeteMsg'>
							<div class="elements_enTete">
							
								<div style="display:flex;"> <h4 id='personneMsg'> <?=$texteh4 ?> </h4> 
								
								<?php if($msg->login=="robot"): ?>
									<div style="position:relative; top:22; left:10"> robot </div>
								<?php else: ?>
								<a id="lienLoginVoirMsg" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$msg->login ?>"/> <?=$msg->login?> </a> 
								<?php endif; ?>
								</div>
							</div>
							
						
							<div class="elements_enTete">
								<h4 id='dateMsg'> 
									<?php
										$timestamp = strtotime($msg->dateMsg);
										$date = date("d/m/Y H:i:s", $timestamp);
										$explodeDate = explode(" ", $date);
										
										echo "Le " . $explodeDate[0] . " à " . $explodeDate[1];
									?> 
								</h4>
							</div>
							
							<?php
								$act = "";
								if (isset($_GET['action']))
									$act = $_GET['action'];
									
								switch ($act):
									case "confSupMsg":
										break;
									default:
							?>
							
							<div class="elements_enTete">
								<a id="boutonSupprimerAfficherMsg" class="boutonsForms" href="index.php?module=ModMessagerie&action=confSupMsg&recu=<?=$recu?>&idMsg=<?=$msg->idMessage ?>"> Supprimer </a>
							</div>
							<?php endswitch; ?>
						</div>
						
						<div id="titreMsg">					
							<h3> Titre: <?= $msg->titreMsg ?> </h3>
						</div>
						
						<div id='corpsMsg'>
						<?php
							$contenu = $msg->contenu; 
							$parser = new JBBCode\Parser();
							$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
							$parser->parse($contenu);
							
							echo $parser->getAsHtml();
						?> 

						</div>
				</div>
