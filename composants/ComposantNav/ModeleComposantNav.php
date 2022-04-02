<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class ModeleComposantNav extends ConnectDB {
	
		public function __construct() {
			parent::connect();
		}
		
		public function menuModAccueil() {
			$role;
			$liens=array();
			
			$liens["index.php"]="Accueil";
			
			if (isset($_SESSION['login'])&&Utilitaires::estBanni($_SESSION['login']['login'])) {
				$liens["index.php?module=ModEspaceMembre"]="Espace membre";			
				$liens["index.php?module=ModAccueil&action=deconnexion"]="Deconnexion";
				return $liens;
			}
			
			$liens["index.php?module=ModArticles"]="Articles";
			
			if (isset($_SESSION['login'])) {
				$role=Utilitaires::getRoleCurrentUser();
				
				if($role=="admin"||$role=="moniteur")
					$liens["index.php?module=ModAdmin"] = "Menu admin";
				
				$liens["index.php?module=ModEspaceMembre"] = "Espace membre";
				
				if ($role=="admin"||$role=="moniteur")
					if (Utilitaires::estActive())
						$liens["index.php?module=ModMoniteur"] = "Espace moniteur";
				
				if ($role == "eleve")
					if (Utilitaires::estActive())
						$liens["index.php?module=ModEleve"] = "Espace élève";
				
				$liens["index.php?module=ModMessagerie"] = "Messagerie";
				$liens["index.php?module=ModAccueil&action=deconnexion"] = "Deconnexion";
			}
			else {
				$liens["index.php?module=ModAccueil&action=afficherConnexion"] = "Connexion";
				$liens["index.php?module=ModAccueil&action=afficherInscription"] = "Inscription";		
			}
			
			return $liens;
		}
		
		public function menuModMessagerie() {
			$liens = array();
			
			$liens["index.php?module=ModMessagerie&action=msgRec"] = "Messages reçus";
			
			if (Utilitaires::estActive()) {
				$liens["index.php?module=ModMessagerie&action=msgEnv"] = "Messages envoyés";
				$liens["index.php?module=ModMessagerie&action=formulaireEnvoi"] = "Ecrire un message";
			}
			
			return $liens;
		}
		
		public function menuModEspaceMembre() {
			$liens = array();
			
			if (!isset($_SESSION['login'])) {
				$liens["index.php"] = "Retour en page d'accueil";
				return $liens;
			}
			
			$liens["index.php?module=ModEspaceMembre&action=formulaireChangerMdp"] = "Changer mot de passe";
			$liens["index.php?module=ModEspaceMembre&action=afficherInfosPerso"] = "Mes informations personnelles";
			$liens["index.php?module=ModEspaceMembre&action=formDeleteAccount"] = "Supprimer mon compte";			
			
			if (Utilitaires::estActive()&&!Utilitaires::estBanni($_SESSION['login']['login'])) {
				$liens["index.php?module=ModEspaceMembre&action=voirProfil"] = "Voir un profil";
			}
			
			return $liens;
		}
		
		public function menuModAdmin() {
			$liens = array();
			
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer annonce"))
				$liens["index.php?module=ModAdmin&action=changerAnnonce"] = "Message d'accueil";
			
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer formules"))
				$liens["index.php?module=ModAdmin&action=gererFormules"] = "Formules";
			
			if (Utilitaires::getRoleCurrentUser()=="admin") {
				$liens["index.php?module=ModAdmin&action=listeUsers"] = "Utilisateurs";				
				$liens["index.php?module=ModAdmin&action=menuDroits"] = "Droits";
				$liens["index.php?module=ModAdmin&action=menuLimites"] = "Limites";
			}
			
			if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer bannissements"))			
				$liens["index.php?module=ModAdmin&action=menuBan"] = "Bannir";
			
			return $liens;
		}
		
		
		
		public function menuModMoniteur() {
			$liens = array();
			
			$nomRole=Utilitaires::getRoleCurrentUser();
			
			if (Utilitaires::estActive()) {
				$liens["index.php?module=ModMoniteur&action=listeEleves"] = "Les élèves";
				$liens["index.php?module=ModMoniteur&action=gererReservations"] = "Réservations";
				
				if ($nomRole=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'], "gerer qcm"))
					$liens["index.php?module=ModMoniteur&action=gererQcm"] = "Gérer les QCM";
				
				if ($nomRole=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'], "creer qcm"))
					$liens["index.php?module=ModMoniteur&action=menuAddQcm"]="Créer un QCM";
				
				if ($nomRole=="admin"||Utilitaires::possedePermission($_SESSION['login']['login'], "gerer competences"))
					$liens["index.php?module=ModMoniteur&action=listeComps"]="Gérer les compétences";
			}
			
			return $liens;
		}
		
		public function menuModEleve() {
			$liens = array();
			
			if (Utilitaires::estActive()) {
				$liens["index.php?module=ModEleve&action=lesMoniteurs"] = "Les moniteurs";			
				$liens["index.php?module=ModEleve&action=voirListeQcm"]="Effectuer un QCM";
				$liens["index.php?module=ModEleve&action=voirQcmEffectues"]="Voir mes QCM effectués";
				$liens["index.php?module=ModEleve&action=gererCompetences"] = "Compétences";
				$liens["index.php?module=ModEleve&action=gererReservations"] = "Réservations";				
			}
			
			return $liens;
		}
		
		public function menuModArticles() {
			$liens = array();
			
			$liens["index.php?module=ModArticles&action=categories"] = "Catégories";
			$liens["index.php?module=ModArticles&action=chercher"] = "Recherche";
			
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&Utilitaires::possedePermission($_SESSION['login']['login'], "ecrire article")) {
				$liens["index.php?module=ModArticles&action=formWrite"] = "Rédiger un nouvel article";
			}
			
			if (Utilitaires::getRoleCurrentUser()=="admin"||Utilitaires::getRoleCurrentUser()=="moniteur") {
				$liens["index.php?module=ModArticles&action=signalements"] = "Voir signalements";
			}
			
			return $liens;
		}
		
		public function menuModBanni() {
			$liens=array();
			
			$liens["index.php?module=ModEspaceMembre"] = "Espace membre";
			
			return $liens;
		}
		
	}
	
?>
