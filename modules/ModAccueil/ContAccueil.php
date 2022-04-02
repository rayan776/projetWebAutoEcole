<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleAccueil.php");
	require_once("VueAccueil.php");
	
	class ContAccueil {
	
		public $modele;
		public $vue;
	
		public function __construct() {
			$this->modele = new ModeleAccueil();
			$this->vue = new VueAccueil();
		}
		
		public function afficherFormConn() {
			if (!isset($_SESSION['login']))
				$this->vue->formulaireConnexion();
		}
		
		public function afficherFormInsc() {
			if (!isset($_SESSION['login']))		
				$this->vue->formulaireInscription();
		}
		
		public function effectuerConnexion() {
			if (!isset($_SESSION['login'])) {
				$connexion = $this->modele->validerConnexion();
				if ($connexion == 1) {
					$this->vue->connexionReussie();
				}
				else {
					$this->afficherFormConn();
					$this->vue->afficherError($connexion);
				}
			}
		}
		
		public function effectuerInscription() {
			if (isset($_SESSION['login']))
				return;
			
			$errors = $this->modele->inscrireCompte();
			if (count($errors) == 0) {
				$this->vue->inscriptionReussie();
			}
			else {
				$this->afficherFormInsc();
				$this->vue->afficherErrors($errors);
			}
		}
		
		public function bienvenue() {
			$this->vue->bienvenue($this->modele->getFormules(), $this->modele->lastPublishedArticles(), $this->modele->getAnnonce());
		}
		
		public function deconnexion() {
			if (isset($_SESSION['login'])) {
				unset($_SESSION['login']);
				unset($_SESSION['token']);
				$this->bienvenue();
			}
		}
		
	}

?>
