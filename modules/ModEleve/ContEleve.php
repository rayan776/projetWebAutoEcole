<?php if(!defined('CONSTANTE'))
	die("Accès interdit");


	require_once "ModeleEleve.php";
	require_once "VueEleve.php";

	
	class ContEleve {
	
		public $modele;
		public $vue;
	
		public function __construct() {
			$this->modele = new ModeleEleve();
			$this->vue = new VueEleve();
		}
		
		public function listeDesQcm() {
			$this->vue->showListeQcm($this->modele->getListeQcm());
		}
		
		public function lesMoniteurs() {
			$this->vue->lesMoniteurs($this->modele->getMoniteurs());
		}
		
		public function afficherReglementQcm() {
			if (isset($_POST['idQcm'])) {
				$idQcm = intval($_POST['idQcm']);
				
				// on vérifie si le QCM est autorisé
				
				if (!$this->modele->qcmAutorise($idQcm))
					return;
				
				// récupération d'un ID de nouvelle tentative
				$idTentative = $this->modele->preparerNouveauQcm();

				$this->vue->afficherReglement($idTentative, $idQcm);				
			}
		}
		
		public function questionSuivanteQcm() {
		
			if (isset($_POST['idQcm'])&&isset($_POST['idTentative'])) {
				$idQcm = intval($_POST['idQcm']);
				$idTentative = intval($_POST['idTentative']);
				
				if ($idTentative==0)
					die();
				
				$this->vue->afficherQuestion($this->modele->questionSuivante($idQcm, $idTentative));
			}
		
		}
		
		public function voirQcmEffectues() {
			$this->vue->listeQcmEffectues($this->modele->getListeQcmEffectues(), $this->modele->getIdsNomsQcm());
		}
		
		public function voirResultatQcm() {
			if (isset($_GET['idTentative'])) {
				$this->vue->afficherResultatsQcm($this->modele->getBilanParTentative());
			}
		}
		
		public function voirCompetences() {
			$this->vue->voirCompetences($this->modele->getCompetences());
		}
		
		public function showReservations() {
		
			$msgRetour="";
		
			if (isset($_POST['reserver'])||isset($_POST['annuler']))
				$msgRetour=$this->modele->gererSeance();
		
			$moniteurs=Utilitaires::getMoniteursParPrenom(); 
			$eleves=Utilitaires::getElevesParPrenom(); 
			$semaines=Utilitaires::listeSemaines(24); 
			$edt=$this->modele->getReservations();
			$lundi=Utilitaires::getLundiSemaineChoisie();
			
			$joursSemaine=Utilitaires::listeJoursSemaineAPartirDeLundi($lundi);
			$this->vue->showReservations($moniteurs, $eleves, $semaines, $joursSemaine, $edt, $msgRetour, $this->modele->getAllReservations());
		
		}
	
	}

?>
