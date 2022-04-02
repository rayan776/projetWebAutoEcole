<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	require_once "ContEleve.php";
	
	class ModEleve {
	
		public $controleur;
	
		public function __construct() {
			if (!isset($_SESSION['login'])) {
				die("Accès interdit");
			}
			else {
				if (Utilitaires::getRoleCurrentUser() != "eleve") {
					die ("Accès interdit");
				}
	
				if (!Utilitaires::estActive())
					return;
			}
		
			$this->controleur = new ContEleve();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			
			switch ($action) {
				case "voirListeQcm":
					$this->controleur->listeDesQcm();
					break;
				case "voirQcmEffectues":
					$this->controleur->voirQcmEffectues();
					break;
				case "resultatsQcm":
					$this->controleur->voirResultatQcm();
					break;
				case "afficherReglementQcm":
					$this->controleur->afficherReglementQcm();
					break;
				case "questionSuivanteQcm":
					$this->controleur->questionSuivanteQcm();
					break;
				case "gererCompetences":
					$this->controleur->voirCompetences();
					break;
				case "gererReservations":
					$this->controleur->showReservations();
					break;
				case "lesMoniteurs":
					$this->controleur->lesMoniteurs();
					break;
			}
		}
	
	}

?>
