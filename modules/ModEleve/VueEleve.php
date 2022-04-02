<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

require_once "VueQcmEleve.php";
	
	class VueEleve extends VueGenerique {
	
		public $vueQcmEleve;
	
		public function __construct() {
			parent::__construct();
			$this->vueQcmEleve = new VueQcmEleve();
		}
		
		public function showListeQcm($listeQcm) {
			require_once "listeDesQcm.php";
		}
		
		public function lesMoniteurs($lesMoniteurs) {
			require_once "lesMoniteurs.php";
		}
		
		public function afficherReglement($idTentative, $idQcm) {
			require_once "reglementQcm.php";
		}
		
		public function afficherQuestion($tabRetour) {
		
			// si la première case du tableau renvoyé par le modèle vaut -1, c'est que le token n'a pas pu être vérifié.
			// si elle vaut 0, c'est que le QCM n'est pas fini.
			// si elle vaut 1, c'est qu'il est fini.
			// si le tableau vaut 2, c'est que l'utilisateur tente d'envoyer une requête alors que les résultats ont déjà été affichés
		
			if ($tabRetour[0]==-1) {
				?> <h3 class="textesEnTete"> Formulaire expiré. </h3> <?php
			}
			if ($tabRetour[0]==0) {
				$this->vueQcmEleve->showQuestion($tabRetour);
			}
			elseif ($tabRetour[0]==1) {
				$this->vueQcmEleve->showResultats($tabRetour);
			}
			elseif ($tabRetour[0]==2) {
				?> <h3 class="textesEnTete"> Ce QCM est terminé. </h3> <?php
			}
			
		}
		
		public function listeQcmEffectues($listeQcm, $idsNomsQcm) {
		
			$nomModule = "ModEleve";
			$checkboxTrierQcmParNom = "";
			$checkboxTrierQcmParNote = "";
			$trierQcmParNote = "DESC";
			$checkboxTrierQcmParDate = "";
			$trierQcmDateDeb = "";
			$trierQcmDateFin = "";
			$optionsNoteMin = "";
			$optionsNoteMax = "";
			$optionsNomQcm = "";
			
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
		
			require_once "qcmEffectues.php";
		}
		
		public function afficherResultatsQcm($tabResultats) {
			$this->vueQcmEleve->showResultats($tabResultats);
		}
		
		
		public function voirCompetences($listeComps) {
		
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
			
			$evalNom = array("Acquis", "En cours d'acquisition", "Non acquis", "Non evalue");
		
			require_once "competencesEleve.php";
		}
		
		
		public function showReservations($moniteurs, $eleves, $semaines, $joursSemaine, $edt, $msgRetour, $allReservations) {
			
			$loginEleve = isset($_POST['loginEleve']) ? htmlspecialchars($_POST['loginEleve'], ENT_QUOTES) : "";
			
			$queDisponible=0;
			
			if (count($edt)==1) {
				$queDisponible=1;
			}
			
			require_once "eleveReservations.php";
		}
	
	}

?>
