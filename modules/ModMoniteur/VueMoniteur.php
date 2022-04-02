<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	
	class VueMoniteur extends VueGenerique {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function showListeEleves($listeEleves) {
		
			$checked = array("", "", "");
			$disabled = array("disabled", "disabled", "disabled");
		
			if (isset($_POST['chercherEleveParLogin'])) {
				$checked[0]="checked";
				$disabled[0]="";
			}
			
			if (isset($_POST['chercherEleveParNom'])) {
				$checked[1]="checked";
				$disabled[1]="";
			}
				
			if (isset($_POST['chercherEleveParPrenom'])) {
				$checked[2]="checked";
				$disabled[2]="";
			}				
			
			$inputLoginEleve = (isset($_POST['loginEleve'])) ? htmlspecialchars($_POST['loginEleve'], ENT_QUOTES) : "";

			$inputNomEleve = (isset($_POST['nomEleve'])) ? htmlspecialchars($_POST['nomEleve'], ENT_QUOTES) : "";
				
			$inputPrenomEleve = (isset($_POST['prenomEleve'])) ? htmlspecialchars($_POST['prenomEleve'], ENT_QUOTES) : "";
			
			require_once "ListeEleves.php";
		}
		
		public function competencesEleve($listeComps, $updateMsg) {
		
			$checkboxTrierProg = "";
			$checkboxTrierMoniteur = "";
			$checkboxTrierMaj = "";
			$trierParProg = "";
			$trierParMoniteur = "";
			$trierMaj = "";
		
			if (isset($_POST['trierPar'])&&is_array($_POST['trierPar'])) {
				if (in_array("trierParProg", $_POST['trierPar'])) {
					$checkboxTrierProg = "checked";
					$trierParProg = $_POST['trierParProg'];	
				}
				
				if (in_array("trierParMoniteur", $_POST['trierPar'])) {
					$checkboxTrierMoniteur = "checked";
					$trierParMoniteur = array("login" => $_POST['trierParMoniteur'], "prenom" => Utilitaires::getPrenomByLogin($_POST['trierParMoniteur']));
				}
				
				if (in_array("trierParMaj", $_POST['trierPar'])) {
					$checkboxTrierMaj = "checked";
					$trierMaj = $_POST['trierParMaj'];
				}
			}
		
			$csrfToken = Tokens::insererTokenForm();
			
			$evalNom = array("Acquis", "En cours d'acquisition", "Non acquis", "Non evalue");		
			
			require_once "CompetencesEleve.php";
		}
		
		public function voirQcmEleve($listeQcm, $idsNomsQcm) {
	
			$checkboxTrierQcmParNom = "";
			$checkboxTrierQcmParNote = "";
			$trierQcmParNote = "DESC";
			$checkboxTrierQcmParDate = "";
			$trierQcmDateDeb = "";
			$trierQcmDateFin = "";
			$optionsNoteMin = "";
			$optionsNoteMax = "";
			$optionsNomQcm = "";
			$nomModule = "ModMoniteur";
			
			for ($i=0; $i<=100; $i++) {
				$optionsNoteMin .= " <option value='$i'> $i </option> ";
			}
			
			for ($i=100; $i>=0; $i--) {
				$optionsNoteMax .= " <option value='$i'> $i </option> ";
			}
			
			foreach ($idsNomsQcm as $idQcm => $nomQcm) {
				$optionsNomQcm .= " <option value='$idQcm'> $nomQcm </option>";
			}
			
			if (isset($_POST['trierQcm'])&&is_array($_POST['trierQcm'])) {
			
				if (in_array("trierParNom", $_POST['trierQcm'])) {
					$checkboxTrierQcmParNom = "checked";
					$idQcmChoisi = htmlspecialchars($_POST['trierParNom'], ENT_QUOTES);
					
					$nomQcmChoisi = $idsNomsQcm[$idQcmChoisi];
					
					$optionsNomQcm = "";
					
					$optionsNomQcm .= " <option value='$idQcmChoisi'> $nomQcmChoisi </option>";
					
					foreach ($idsNomsQcm as $idQcm => $nomQcm) {
						if ($idQcm != $idQcmChoisi) {
							$optionsNomQcm .= " <option value='$idQcm'> $nomQcm </option>";
						}
					}
					
				}
			
				if (in_array("trierParNote", $_POST['trierQcm'])) {
					$checkboxTrierQcmParNote = "checked";
					$trierQcmParNote = $_POST['trierParNote'];
					
					if (isset($_POST['trierNoteMin'])) {
						$optionsNoteMin = "";
						$noteMin = htmlspecialchars($_POST['trierNoteMin']);
						$optionsNoteMin .= " <option value='$noteMin'> $noteMin </option> ";
						
						for ($i=0; $i<=100; $i++) {
							if ($i != intval($_POST['trierNoteMin'])) {
								$optionsNoteMin .= " <option value='$i'> $i </option> ";
							}
						}
					}
					
					if (isset($_POST['trierNoteMax'])) {
						$optionsNoteMax = "";
						$noteMax = htmlspecialchars($_POST['trierNoteMax']);
						$optionsNoteMax .= " <option value='$noteMax'> $noteMax </option> ";						
						for ($i=100; $i>=0; $i--) {

							if ($i != intval($_POST['trierNoteMax'])) {
								$optionsNoteMax .= " <option value='$i'> $i </option> ";
							}
						}
					}
					
					
				}
				
				if (in_array("trierParDate", $_POST['trierQcm'])) {
					$checkboxTrierQcmParDate = "checked";
					$trierQcmDateDeb = htmlspecialchars($_POST['trierQcmDateDeb'], ENT_QUOTES);
					$trierQcmDateFin = htmlspecialchars($_POST['trierQcmDateFin'], ENT_QUOTES);
				}
			}
		
			require_once "qcmEleve.php";
		}
		
		public function afficherResultatsQcm($tabResultats) {
		
			require_once "modules/ModEleve/VueQcmEleve.php";
		
			$vueQcmEleve = new VueQcmEleve();
		
			$vueQcmEleve->showResultats($tabResultats);
		}
		
			
		public function gererQcm($listeQcm, $msgApresUpdate) {
			$csrfToken = Tokens::insererTokenForm();
			require_once "gererQcm.php";
		}
		
		
		public function voirQcm($qcm) {
			$nomQcm=$qcm[0];
			$questions=$qcm[1];
			
			require_once "voirQcm.php";	
		}
		
		public function menuAddQcm($messageRetour,$illustrations){
		
			$csrfToken = Tokens::insererTokenForm();
			
			require_once "addQcm.php";
		}
		
		public function showReservations($moniteurs, $eleves, $semaines, $joursSemaine, $edt, $allReservations, $msgRetour) {
		
			$loginEleve = isset($_POST['loginEleve']) ? htmlspecialchars($_POST['loginEleve'], ENT_QUOTES) : "";
			
			$queDisponible=0;
			
			if ($edt[0]==-1) {
				$queDisponible=1;
			}
			
		
			require_once "voirReservations.php";
		}
		
		public function gererComps($listeComps, $msgRetour) {
			$csrfToken=Tokens::insererTokenForm();
			
			require_once "listeComps.php";
		}
		
		
	
		
	
	}

?>
