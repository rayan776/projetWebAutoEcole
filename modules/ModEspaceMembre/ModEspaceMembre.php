<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

	require_once("ContEspaceMembre.php");

	class ModEspaceMembre {
		
		public $controleur;
		
		public function __construct() {
		
			if (!isset($_SESSION['login']))
				die("Accès interdit");
		
			$this->controleur = new ContEspaceMembre();
			
			$action=isset($_GET['action'])?$_GET['action']:"";
		
			switch ($action) {
				case "formulaireChangerMdp":
					$this->controleur->formulaireChangerMdp();
					break;
				case "changerMdp":
					$this->controleur->changerMdp();
					break;
				case "afficherInfosPerso":
					$this->controleur->afficherInfosPerso();
					break;
				case "updateInfosPerso":
					$this->controleur->updateInfosPerso();
					break;
				case "voirProfil":
					$this->controleur->voirProfil();
					break;
				case "formDeleteAccount":
					$this->controleur->formDeleteAccount("");
					break;
				case "deleteAccount":
					$this->controleur->deleteAccount();
					break;
			}
		}
	}
?>
