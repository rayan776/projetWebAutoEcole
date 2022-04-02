<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ContMessagerie.php");
		
	class ModMessagerie {
		
		public $controleur;
		
		public function __construct() {
			$this->controleur = new ContMessagerie();
			
			$action = isset($_GET['action']) ? $_GET['action'] : "afficherMessages";
			
			switch ($action) {
				case "formulaireEnvoi":
					$this->controleur->formulaire();
					break;
				case "envoyer":
					$this->controleur->envoyerMessage();
					break;
				case "msgEnv":
					$this->controleur->listeMessages("false");
					break;
				case "confSupMsg":
					$this->controleur->confSupMessage();
					break;
				case "confSupprimerGroupeDeMessages":
					$this->controleur->confSupGroupMessages();
					break;
				case "supMsg":
					$this->controleur->supMessage();
					break;
				case "supprimerGroupeDeMessages":
					$this->controleur->supGroupMessages();
					break;
				case "afficherMessage":
					$this->controleur->afficherMessage();
					break;
				case "marquerCommeLu":
					$this->controleur->marquerGroupMessagesCommeLu();
					break;
				default:
					$this->controleur->listeMessages("true");
			}
		}
		
	}
	
?>
