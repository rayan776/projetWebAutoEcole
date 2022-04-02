<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	require_once "ContArticles.php";
	
	class ModArticles {
	
		public $controleur;
	
		public function __construct() {
		
			$this->controleur = new ContArticles();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "chercher";
			
			switch ($action) {
				case "formWrite":
					$this->controleur->showFormWrite();
					break;
				case "writeArticle":
					$this->controleur->writeArticle();
					break;
				case "formModifierArticle":
				case "editArticle":				
					$this->controleur->editArticle();
					break;
				case "categories":
					$this->controleur->listeCategories();
					break;
				case "chercher":
				case "deleteArticle":
					$this->controleur->listeArticles();
					break;
				case "voirArticle":
					$this->controleur->voirArticle();
					break;
				case "signaler":
					$this->controleur->showSignalForm();
					break;
				case "transmettreSignalement":
					$this->controleur->transmettreSignalement();
					break;
				case "signalements":
				case "supprimerSignalements":
					$this->controleur->gererSignalements();
					break;
			}
		}
	
	}

?>
