<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	require_once "ContMoniteur.php";
	
	class ModMoniteur {
	
		public $controleur;
	
		public function __construct() {
			if (!isset($_SESSION['login'])) {
				die("Accès interdit");
			}
			else {
				if (Utilitaires::getRoleCurrentUser() != "moniteur" && Utilitaires::getRoleCurrentUser() != "admin") {
					die ("Accès interdit");
				}
				
				if (!Utilitaires::estActive())
					return;
	
			}
		
			$this->controleur = new ContMoniteur();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			
			switch ($action) {
				case "listeEleves":
					$this->controleur->listeEleves();
					break;
				case "compEleve":
					$this->controleur->voirCompEleve();
					break;
				case "updateComps":
					$this->controleur->updateCompEleve();
					break;
				case "qcmEleve":
					$this->controleur->voirQcmEleve();
					break;
				case "resultatsQcm":
					$this->controleur->resultatsQcm();
					break;
				case "gererQcm":
				case "updateQcm":
					$this->controleur->gererQcm();
					break;
				case "menuAddQcm":
					$this->controleur->menuAddQcm();
					break;
				case "addQcm":
					$this->controleur->addQcm();
					break;
				case "gererReservations":
					$this->controleur->showReservations();
					break;
				case "voirQcm":
					$this->controleur->voirQcm();
					break;
				case "listeComps":
					$this->controleur->listeComps();
					break;
			}
		}
	
	}

?>
