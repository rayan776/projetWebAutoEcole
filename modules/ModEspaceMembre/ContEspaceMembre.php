<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleEspaceMembre.php");
	require_once("VueEspaceMembre.php");
		
	class ContEspaceMembre {
	
		public $modele;
		public $vue;
		
		public function __construct() {
			$this->modele = new ModeleEspaceMembre();
			$this->vue = new VueEspaceMembre();
		}
		
		public function formulaireChangerMdp() {
			$this->vue->formulaireChangerMdp();
		}
		
		public function changerMdp() {
			$this->vue->afficher($this->modele->traitementChangerMdp());
		}

		public function afficherInfosPerso() {
			$this->vue->formulaireInfosPerso($this->modele->getInfosPerso());
		}

		public function updateInfosPerso() {
			$update = $this->modele->updateInfosPerso();

			if (count($update) == 1 && $update[0] == 1) {
				$this->vue->updateInfosPersoReussi();
			}
			else {
				$this->vue->updateInfosPersoEchec($update);
			}
		}
		
		public function voirProfil() {
		
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&!Utilitaires::estBanni($_SESSION['login']['login'])) {
			
				if (isset($_GET['login'])) {
					$infosProfil = $this->modele->getInfos();
					if ($infosProfil != NULL)
						$this->vue->afficherProfil($infosProfil);
					else {
						$this->vue->profilInexistant();
					}
				}
				else {
					$this->vue->afficherProfil(NULL);
				}			
			}
		

		}
		
		public function formDeleteAccount($msg) {
			$this->vue->formDeleteAccount($msg);
		}
		
		public function deleteAccount() {
		
			$msgRetour=$this->modele->deleteAccount();
			if ($msgRetour=="OK") {
				unset($_SESSION['login']);
				unset($_SESSION['token']);
				$this->vue->bye();
			}
			else {
				$this->formDeleteAccount($msgRetour);
			}
		}
		

	}

?>
