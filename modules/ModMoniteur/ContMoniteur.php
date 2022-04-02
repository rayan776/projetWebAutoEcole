<?php if(!defined('CONSTANTE'))
	die("Accès interdit");


	require_once "ModeleMoniteur.php";
	require_once "VueMoniteur.php";

	
	class ContMoniteur {
	
		public $modele;
		public $vue;
	
		public function __construct() {
			$this->modele = new ModeleMoniteur();
			$this->vue = new VueMoniteur();
		}
		
		public function listeEleves() {

			$this->vue->showListeEleves($this->modele->getListeEleves());
			
		}
		
		public function voirCompEleve() {


			if (isset($_GET['loginEleve'])) {
				$this->vue->competencesEleve($this->modele->getCompEleve(), "");
			}
			
		}
		
		public function updateCompEleve() {
		

			if (isset($_POST['loginEleve'])) {
				$updateCompRetour = $this->modele->updateCompEleve();
				$this->vue->competencesEleve($this->modele->getCompEleve(), $updateCompRetour);
			}
			

		}
		
		public function voirQcmEleve() {

			if (isset($_GET['loginEleve'])) {
				$this->vue->voirQcmEleve($this->modele->getQcmEleve(), $this->modele->getIdsNomsQcm());
			}
		
		}
		
		public function resultatsQcm() {
			if (isset($_GET['idTentative'])) {
				$this->vue->afficherResultatsQcm($this->modele->getResultatsQcm());
			}
		}
		
		public function gererQcm() {
			if (Utilitaires::getRoleCurrentUser()=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'],"gerer qcm")) {
				$msgApresUpdate = "";
				if (isset($_POST['updateQcm'])) {
					$msgApresUpdate=$this->modele->updateQcm();
				}
				elseif(isset($_POST['destroyQcm'])) {
					$msgApresUpdate=$this->modele->destroyQcm();
				}
				$this->vue->gererQcm($this->modele->getAllQcm(), $msgApresUpdate);
			}
		}
		
		public function menuAddQcm() {
			if (Utilitaires::getRoleCurrentUser()=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'],"creer qcm")) {
				
				$this->vue->menuAddQcm(array(0),$this->modele->getIllustrations());
			}
		}
		
		public function addQcm() {
			if (Utilitaires::getRoleCurrentUser()=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'],"creer qcm")) {
			
				$this->vue->menuAddQcm($this->modele->addQcm(),$this->modele->getIllustrations());
				
			}
		}
		
		public function voirQcm() {
			if (isset($_GET['idQcm'])) {
				$this->vue->voirQcm($this->modele->getQuestionsReponsesQcm());
			}
		}
		
		
		public function showReservations() {
			$msgRetour="";
			
			if (isset($_POST['accepter'])||isset($_POST['annuler'])||isset($_POST['refuser']))
				$this->modele->gererSeance();
			
			if (isset($_POST['report'])) {
				$msgRetour=$this->modele->reporterSeance();
			}
		
			$moniteurs=Utilitaires::getMoniteursParPrenom(); 
			$eleves=Utilitaires::getElevesParPrenom(); 
			$semaines=Utilitaires::listeSemaines(24); 
			$edt=$this->modele->getReservations();
			$lundi=Utilitaires::getLundiSemaineChoisie();
			
			$joursSemaine=Utilitaires::listeJoursSemaineAPartirDeLundi($lundi);
			$this->vue->showReservations($moniteurs, $eleves, $semaines, $joursSemaine, $edt, $this->modele->getAllReservations(), $msgRetour);
		}
		
		public function listeComps() {
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer competences")) {
			
				$msgRetour="";
				
				if (isset($_POST['deleteListComps'])) {
					$msgRetour=$this->modele->updateListComps("delete");
				}
				elseif (isset($_POST['majListComps'])) {
					$msgRetour=$this->modele->updateListComps("update");
				}
				elseif (isset($_POST['addComp'])) {
					$msgRetour=$this->modele->addNewComp();
				}
			
				$this->vue->gererComps($this->modele->getAllComps(), $msgRetour);
			}
		}
		
		
	}

?>
