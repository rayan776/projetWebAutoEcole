<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ContAccueil.php");
	
	class ModAccueil {
	
		public $controleur;
	
		function __construct() {
			$this->controleur = new ContAccueil();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			
			switch ($action) {
				case "afficherConnexion":
					$this->controleur->afficherFormConn();
					break;
				case "connexion":
					$this->controleur->effectuerConnexion();
					break;
				case "afficherInscription":
					$this->controleur->afficherFormInsc();
					break;
				case "inscription":
					$this->controleur->effectuerInscription();
					break;
				case "deconnexion":
					$this->controleur->deconnexion();
					break;
				default:
					$this->controleur->bienvenue();
			}
		}
		
	}

?>
