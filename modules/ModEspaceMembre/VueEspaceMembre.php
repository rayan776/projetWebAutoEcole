<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class VueEspaceMembre extends VueGenerique {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function formulaireChangerMdp() {
			$csrfToken = Tokens::insererTokenForm();
			require_once("templateChangerMdp.php");
		}
		
		public function afficher($tab) {
			for ($i=0; $i<count($tab); $i++):
			?>
				<div class="erreur"> <?=$tab[$i]?> </div>
			<?php endfor;
		}

		public function formulaireInfosPerso($infos) {

			$csrfToken = Tokens::insererTokenForm();

			$nom = $infos->nom;
			$prenom = $infos->prenom;
			$numTel = $infos->numTel;
			$ville = $infos->ville;
			$codePostal = $infos->codePostal;
			$neph = $infos->neph;
			$role = Utilitaires::getRoleCurrentUser();
			$autoriserElevesChecked = Utilitaires::infosVisiblesParEleves($_SESSION['login']['login']) ? "checked" : "";
			$autoriserElevesEDTChecked = Utilitaires::edtVisibleParEleves($_SESSION['login']['login']) ? "checked" : "";
			$listeLimitesRole = Limites::getLimitesRole($role);
			$listeLimitesUser = Limites::getLimitesUser($_SESSION['login']['login']);
			$valeursLimites=Limites::getValLimitesUser($_SESSION['login']['login']);
			$affichage=array("limite messages" => "messages envoyés", "limite reservations" => "réservations effectuées", "limite signalements" => "signalements effectués", "limite commentaires" => "commentaires postés", "limite articles" => "nouveaux articles publiés");

			require_once "TemplateInfosPerso.php";
		}

		public function updateInfosPersoReussi() {
			require_once "UpdateInfosPersoReussi.php";
		}

		public function updateInfosPersoEchec($tab) {
			require_once "UpdateInfosPersoEchec.php";
		}
		
		public function formulaireRechercheProfil() {
			$afficherInfos = 0;
			require_once "TemplateVoirProfil.php";

		}
		
		public function profilInexistant() {
			?>
				<h3 class="textesEnTete"> Soit vous n'avez rien saisi, soit le profil en question n'existe pas dans notre base de données. On rappelle que le robot ne possède pas de compte sur le site. </h3>
			<?php
			$this->formulaireRechercheProfil();
		}
		
		public function afficherProfil($infos) {
		
			$afficherInfos = 0;
		
			if ($infos != NULL) {
		
				$afficherInfos = 1;
			
				$nom = $infos->nom;
				$prenom = $infos->prenom;
				$numTel = $infos->numTel;
				$ville = $infos->ville;
				$codePostal = $infos->codePostal;
				$neph = (isset($infos->NEPH)) ? $infos->NEPH : "N/A";
				$role = $infos->nomRole;
				$username = $infos->login;
			}

			require_once "TemplateVoirProfil.php";
		}
		
		public function formDeleteAccount($msg) {
			$csrfToken=Tokens::insererTokenForm();
			
			require_once "formDeleteAccount.php";
		}
		
		public function bye() {
			echo "<h1 style='color:white'> Votre compte a bien été supprimé. </h1> <br/> <a style='color:white' href='index.php'> Retour en page d'accueil </a>";
		}	


	}
	
?>
