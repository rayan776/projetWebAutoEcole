<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class VueMessagerie extends VueGenerique {
		public function __construct() {
			parent::__construct();
		}
	
		public function afficherListeMessages($tab, $recu) {
		
			$liste = $tab[0];
			$countMsg = $tab[1];
			if ($recu == "true")
				$newMsg = $tab[2];

			$texteh3 = "reçus";
			$texteh4 = "Envoyé par ";
			
			if ($recu == "false") {
				$texteh3 = "envoyés";
				$texteh4 = "Envoyé à ";
			}
			
			$destOuExp = ($recu == "true") ? "expéditeur: " : "destinataire: ";
		
			$orderByDate = "DESC"; 
			
			if (isset($_POST['orderByDate']))
				if ($_POST['orderByDate'] == "ASC" || $_POST['orderByDate'] == "DESC")
					$orderByDate = $_POST['orderByDate'];
			
			if (isset($_POST['orderByUser']))
				$orderByUser = htmlspecialchars($_POST['orderByUser'], ENT_QUOTES);
			else
				$orderByUser = "";
			
			$page = isset($_POST['page']) ? $_POST['page'] : 1;
			
			require_once "templateListeMessages.php";
			
		}
		
		public function afficherMessage($msg, $recu) {
			$texteh4 = "Envoyé à : ";
			
			if ($recu == "true") {
				$texteh4 = "Envoyé par : ";
			}
			
			require_once "templateMessage.php";
		}
		
		public function afficher($chaine) {
			?> <h4 align="right" class="textesEnTete"> <?=$chaine?> </h2> <?php
			if ($chaine != "Message envoyé." && isset($_GET['action']) && $_GET['action'] == "formulaireEnvoi" || $_GET['action'] == "envoyer") {
				self::formulaire();
			}
		}
		
		public function formulaire() {
		
			$csrfToken = Tokens::insererTokenForm();
			
			$dest = isset($_POST['dest']) ? htmlspecialchars($_POST['dest'], ENT_QUOTES) : "";
			$titreMsg = isset($_POST['titreMsg']) ? htmlspecialchars($_POST['titreMsg'], ENT_QUOTES) : "";
			$message = isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES) : "";
			
			$parser=new JBBCode\Parser();
			$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
			$parser->parse($message);
			
			$message=$parser->getAsHtml();

			require_once "TemplateEnvoyerMsg.php";
		}
		
		public function formConfSup($msg) {
		
			$idMsg = "";
			$recu = "";
			
			if(isset($_GET['idMsg']))
				$idMsg = $_GET['idMsg'];
			else
				return false;
				
			if (isset($_GET['recu'])) {
				if ($_GET['recu'] == "true" || $_GET['recu'] == "false") {
					$recu = $_GET['recu'];
				}
				else
					return false;
			}
			else {
					return false;
			}
			
			$this->afficherMessage($msg, $recu);
			$csrfToken = Tokens::insererTokenForm();
			
			require_once "templateConfSupMsg.php";
		}
		
		public function formConfSupGroupesMsg() {
			$csrfToken = Tokens::insererTokenForm();
			require_once "templateConfSupGroupesMsg.php";
		}
		
	}
	
?>
