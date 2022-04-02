<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleMessagerie.php");
	require_once("VueMessagerie.php");
		
	class ContMessagerie {
		
		public $modele;
		public $vue;
		
		public function __construct() {
			$this->modele = new ModeleMessagerie();
			$this->vue = new VueMessagerie();
		}
		
		public function formulaire() {
			if (Utilitaires::estActive()) {
				if (Utilitaires::possedePermission($_SESSION['login']['login'], "envoyer messages"))
					$this->vue->formulaire();
				else
					die("Vous n'avez pas la permission d'effectuer cette action");
			}
		}
		
		public function listeMessages($recu) {
			$this->vue->afficherListeMessages($this->modele->getListeMessages($recu), $recu);
		}
		
		public function afficherMessage() {
			if (isset($_GET['idMsg']) && isset($_GET['recu'])) {
				if ($_GET['recu'] == "true" || $_GET['recu'] == "false") {
					$this->modele->marquerCommeLu($_GET['idMsg']);
					$this->vue->afficherMessage($this->modele->getMessage($_GET['idMsg']), $_GET['recu']);
				}
			}
		}
		
		public function envoyerMessage() {
			if (Utilitaires::estActive()) {
				if (Utilitaires::possedePermission($_SESSION['login']['login'], "envoyer messages")) {
					$retour = $this->modele->sendMessage();
					$this->vue->afficher($retour);
				}
				else
					die("Accès interdit");
			}
			
		}
		
		public function confSupMessage() {
			if (isset($_GET['idMsg']) && isset($_GET['recu'])) {
				$msg = $this->modele->getMessage($_GET['idMsg'], $_GET['recu']);
				if (!$msg) {
				
				}
				else {
					$this->vue->formConfSup($msg);
				}
			}
					
		}
		
		public function confSupGroupMessages() {
			if (isset($_POST['recu']))
				$this->vue->formConfSupGroupesMsg();
		}
		
		public function supMessage() {
			$this->vue->afficher($this->modele->supprimerMessage());
			$recu = (isset($_POST['recu'])) ? $_POST['recu'] : "false";
			self::listeMessages($recu);
		}
		
		public function marquerGroupMessagesCommeLu() {
			
			$this->modele->marquerGroupeCommeLu();
			self::listeMessages("true");
		}
		
		public function supGroupMessages() {

			$this->vue->afficher($this->modele->supprimerGroupeDeMessages());
			$recu = isset($_POST['recu']) ? $_POST['recu'] : "true";
			self::listeMessages($recu);
			
		}
	}
	
?>
