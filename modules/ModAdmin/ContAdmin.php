<?php
	if(!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleAdmin.php");
	require_once("VueAdmin.php");
	
	class ContAdmin {
		public $modele;
		public $vue;
		
		function __construct() {
			$this->modele = new ModeleAdmin();
			$this->vue = new VueAdmin();
		}
		
		function getListeUsers() {
			if (isset($_SESSION['login'])) {
				if (Utilitaires::getRoleCurrentUser() == 'admin') {
					$this->vue->afficherListeUsers($this->modele->recupererListeUsers());
				}
				else
					die("Accès interdit");
			}
			else
				die("Accès interdit");
		}
		
		function activerCompte() {
		
			if  (isset($_SESSION['login'])) {	
				if (Utilitaires::getRoleCurrentUser() == 'admin') {
				
					if (Tokens::checkCSRF()) {
						$this->vue->recommencer();
						return;
					}
					$this->modele->activerCompte();
					$this->vue->activerOK();
				}
				else
					die("Accès interdit");
			}
			else
				die("Accès interdit");
		}

		function afficherInfos() {
			if (Utilitaires::getRoleCurrentUser() == 'admin') {
				if (isset($_GET['id']))
					$this->vue->afficherInfos($this->modele->getInfos($_GET['id']));
			}
			else {
				die("Accès interdit");
			}
		}
		
		public function menuDroitsOuLimites() {
		
			if (Utilitaires::getRoleCurrentUser() == "admin") {
			
				if ($_GET['action']=="menuDroits") {
					if (!isset($_POST['droitsDeQui'])&&isset($_POST['modifierDroitsType'])) {
						$_POST['droitsDeQui']=$_POST['modifierDroitsType'];
					}
				
					$this->vue->afficherMenuDroitsOuLimites($this->modele->listeRoles(), "droits");
					if (!isset($_POST['modifierDroitsType'])&&isset($_POST['droitsDeQui'])) {
						$this->vue->afficherDroitsOuLimites($this->modele->getDroits(), $this->modele->getListAllDroits(), "");
					}
					elseif (isset($_POST['modifierDroitsType'])) {
						$valRetourApresModifDroit = $this->modele->modifierDroits();
						$this->vue->afficherDroitsOuLimites($this->modele->getDroits(), $this->modele->getListAllDroits(), $valRetourApresModifDroit);
					}
				}
				elseif($_GET['action']=="menuLimites") {
					if (!isset($_POST['limitesDeQui'])&&isset($_POST['modifierLimitesType'])) {
						$_POST['limitesDeQui']=$_POST['modifierLimitesType'];
					}
				
					$this->vue->afficherMenuDroitsOuLimites($this->modele->listeRoles(), "limites");
					if (!isset($_POST['modifierLimitesType'])&&isset($_POST['limitesDeQui'])) {
						$this->vue->afficherDroitsOuLimites($this->modele->getLimites(), $this->modele->getListAllLimites(), "");
					}
					elseif (isset($_POST['modifierLimitesType'])) {
						$valRetourApresModifLimite = $this->modele->modifierLimites();
						$this->vue->afficherDroitsOuLimites($this->modele->getLimites(), $this->modele->getListAllLimites(), $valRetourApresModifLimite);
					}
				}
			
				
			}
			else
				die("Accès interdit");
		}
		
		public function menuBan() {
			if (Utilitaires::getRoleCurrentUser()!="admin"&&Utilitaires::getRoleCurrentUser()!="moniteur") {
				die("Accès interdit");
			}
			
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer bannissements")) {
			
				$msgRetour="";
				
				if (isset($_POST['deleteBans'])) {
					$msgRetour=$this->modele->supprimerBans();
				}
				elseif (isset($_POST['bannir'])) {
					$msgRetour=$this->modele->bannir();
				}
			
				$this->vue->menuBan($this->modele->getListBans(), $msgRetour);
			}
			else
				die("Accès interdit");
			
		}
		
		public function gererFormules() {
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer formules")) {
				$msgRetour="";
				
				if (isset($_POST['addFormule'])) {
					$msgRetour=$this->modele->addFormule();
				}
				elseif (isset($_POST['updateFormule'])) {
					$msgRetour=$this->modele->updateFormule();
				}
				elseif (isset($_POST['deleteFormule'])) {
					$msgRetour=$this->modele->deleteFormule();
				}
				
				$this->vue->gererFormules($this->modele->getFormules(),$msgRetour);
			}
			else
				die("Accès interdit");
		}
		
		public function annonce() {
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer annonce")) {
				$msgRetour="";
				
				if (isset($_POST['annonce'])) {
					$msgRetour=$this->modele->changerAnnonce();
				}
				
				$this->vue->annonce($this->modele->getAnnonce(), $msgRetour);
			}
			else
				die("Accès interdit");
		}
		
		
	}
	
?>
