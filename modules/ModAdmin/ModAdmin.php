<?php
	if(!defined('CONSTANTE'))
		die("Accès interdit");

	require_once("ContAdmin.php");

	class ModAdmin {
	
		public $controleur;
	
		function __construct() {
			if (!isset($_SESSION['login'])) {
				die("Accès interdit");
			}
			else {
				if (Utilitaires::getRoleCurrentUser()!="admin"&&Utilitaires::getRoleCurrentUser()!="moniteur") {
					die ("Accès interdit");
				}
	
			}
		
			$this->controleur = new ContAdmin();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			
			switch ($action) {
				case "listeUsers":
					$this->controleur->getListeUsers();
					break;
				case "activerCompte":
					$this->controleur->activerCompte();
					break;
				case "afficherInfos":
					$this->controleur->afficherInfos();
					break;
				case "menuLimites":
				case "menuDroits":
					$this->controleur->menuDroitsOuLimites();
					break;
				case "menuBan":
					$this->controleur->menuBan();
					break;
				case "gererFormules":
					$this->controleur->gererFormules();
					break;
				case "changerAnnonce":
					$this->controleur->annonce();
					break;
			}
			
		}
	
	}
	
?>
