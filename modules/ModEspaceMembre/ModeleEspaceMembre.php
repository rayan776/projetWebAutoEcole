<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class ModeleEspaceMembre extends ConnectDB {
	
		public $validateur;
		
		public function __construct() {
			parent::connect();
			$this->validateur = new Validateur();
		}
		
		// vérifie si le mdp saisi par l'utilisateur dans le champ "mdp actuel" correspond à son mdp actuel
		public function verifierMdpActuel() {
			$mdpActu = isset($_POST['mdpActu']) ? $_POST['mdpActu'] : "";
			
			if (empty($mdpActu))
				return 0;
				
			$mdpActu = hash("sha256", $mdpActu);
			
			$login = $_SESSION['login']['login'];
			
			$sql = "SELECT password FROM users WHERE password = :mdpActu AND login = :login";
			
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":mdpActu", $mdpActu, PDO::PARAM_STR);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			$results = $query->fetchAll();
			
			if (count($results) > 0)
				return 1; // code retour 1 = le mot de passe existe dans la base de données et est associé à l'utilisateur
			else
				return 0;
		}
		
		// met à jour le mot de passe dans la base de données
		public function updateMdpDB() {
			$login = $_SESSION['login']['login'];
			$newMdp = hash("sha256", $_POST['password']);
			$sql = "UPDATE users SET password = :newMdp WHERE login = :login";
			
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":newMdp", $newMdp, PDO::PARAM_STR);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			
			if ($query->execute())
				return 1; // update réussi
			else
				return 0; // update échoué
		}
		
		// la fonction principale du modèle qui se sert des dernières fonctions
		public function traitementChangerMdp() {
		
			$valRetour = array();
			
			if (Tokens::checkCSRF()) {
				$valRetour[] = "Formulaire expiré, veuillez réessayer.";
				return $valRetour;
			}
		

			if ($this->verifierMdpActuel() == 0) {
				$valRetour[] = "Votre mot de passe actuel ne correspond pas à celui indiqué";
				return $valRetour;
			}
			
			$valRetour = Utilitaires::verifierConfirmationMDP();
			
			if (count($valRetour) > 0)
				return $valRetour;
			
			$tabVerifs = $this->validateur->verifierPwd();
			
			if (count($tabVerifs) > 0)
				return $tabVerifs;
			
			if ($this->updateMdpDB() == 0)
				$valRetour[] = "Erreur base de donneés: contactez l'administration";
			else
				$valRetour[] = "Mot de passe modifié avec succès";
			
			return $valRetour;
		}

		public function getInfosPerso() {

			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);

			$sql = "SELECT nom, prenom, numTel, ville, codePostal, CASE WHEN NEPH IS NULL THEN 0 ELSE NEPH END AS neph FROM infosPerso INNER JOIN correspondre USING (idInfo) INNER JOIN users USING (idUser) WHERE idUser = :idUser";

			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
			$query->execute();

			return $query->fetch(PDO::FETCH_OBJ);
		}

		public function updateInfosPerso() {

			$errors = array();

			if (Tokens::checkCSRF()) {
				$errors[]="Formulaire expiré";
				return $errors;
			}

			$validateur = new Validateur();

			$errors = $validateur->verifierInfosPerso(0);

			if (count($errors) > 0)
				return $errors;
			
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);

			$sql = "SELECT idInfo FROM infosPerso INNER JOIN correspondre USING (idInfo) INNER JOIN users USING (idUser) WHERE idUser = :idUser";

			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
			$query->execute();

			$res = $query->fetch(PDO::FETCH_OBJ);
			$idInfo = $res->idInfo;

			$nom = strtolower(trim($_POST['nom']));
			$prenom = strtolower(trim($_POST['prenom']));
			$cp = strtolower(trim($_POST['cp']));
			$ville = strtolower(trim($_POST['ville']));
			$phone = strtolower(trim($_POST['phone']));
			
			$sql = "UPDATE infosPerso SET nom = :nom, prenom = :prenom, codePostal = :cp, ville = :ville, numTel = :phone WHERE idInfo = :idInfo";

			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":nom", $nom, PDO::PARAM_STR);
			$query->bindValue(":prenom", $prenom, PDO::PARAM_STR);
			$query->bindValue(":cp", $cp, PDO::PARAM_STR);
			$query->bindValue(":ville", $ville, PDO::PARAM_STR);
			$query->bindValue(":phone", $phone, PDO::PARAM_STR);
			$query->bindValue(":idInfo", $idInfo, PDO::PARAM_INT);							
			$query->execute();

			if ($_POST['type']=="eleve"&&Utilitaires::getRoleCurrentUser()=="eleve") {
				$neph = trim($_POST['neph']);
				$sql = "UPDATE infosPerso SET NEPH = :neph WHERE idInfo = :idInfo";
				$query = parent::$bdd->prepare($sql);
				$query->bindValue(":neph", $neph, PDO::PARAM_STR);				
				$query->bindValue(":idInfo", $idInfo, PDO::PARAM_INT);	
				
				if (!$query->execute()) {
					$errors[] = "Le numéro NEPH que vous avez saisi est déjà utilisé, il est censé être unique. Veuillez contacter l'administration";
					return $errors;
				}
				
				$allowEdt = isset($_POST['autoriserElevesVoirEDT']) ? 1 : 0;
				
				$sql="UPDATE estEleve SET autoriserAutresElevesVoirEDT=$allowEdt WHERE idUser=$idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			$autoriser = 0;
			
			if (isset($_POST['autoriserElevesVoirInfos'])) {
				$autoriser = 1;
			}
			
			$sql = "UPDATE correspondre SET autoriserElevesVoirInfos = $autoriser WHERE idUser = :idUser";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);			
			$query->execute();

			$errors[] = 1;
			return $errors;
			
		}
		
		public function getInfos() {
		
			if (isset($_GET['login'])) {
				if (empty($_GET['login']))
					return NULL;
			} else {
				return NULL;
			}
			
			$idUser = Utilitaires::getIdUser(trim($_GET['login']));
		
			$sql = "SELECT login, nomRole, nom, prenom, ville, codePostal, numTel, NEPH FROM infosPerso INNER JOIN correspondre USING (idInfo) INNER JOIN users USING (idUser) INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE idUser = :idUser";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
			$query->execute();

			$res = $query->fetch(PDO::FETCH_OBJ);

			if (!$res)
				return NULL;
			
			return $res;
		}
		
		public function deleteAccount() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['mdpActu'])||!isset($_POST['confMdpActu'])||!isset($_POST['conf']))
				return "Vous n'avez pas confirmé.";
			
			// vérifier mot de passe
			
			$mdpVerifie=0;
			
			if ($this->verifierMdpActuel()==1) {
				
				$_POST['mdpActu']==$_POST['confMdpActu'];
				
				if ($this->verifierMdpActuel()==1) {
					$mdpVerifie=1;
				}
			}
			
			if ($mdpVerifie==0)
				return "Le mot de passe saisi ne correspond pas à votre mot de passe.";
			
			$garderArticlesCom = isset($_POST['garderArticlesCom']) ? 1 : 0;
			
			// préparation des requêtes SQL
			
			// s'agit-il d'un élève ou d'un moniteur?
			
			$nomRole=Utilitaires::getRoleCurrentUser();
			
			// l'admin ne doit pas pouvoir supprimer son compte
			
			if ($nomRole=="admin")
				return "L'admin ne peut pas supprimer son compte.";
				
			$nomId = ($nomRole=="eleve") ? "idEleve" : "idMoniteur";
			$nomTableGetId = ($nomRole=="eleve") ? "estEleve" : "estMoniteur";
			
			$idEleveOuMoniteur;
			$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);

			$sql="SELECT $nomId FROM $nomTableGetId INNER JOIN users USING (idUser) WHERE login = :login";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $_SESSION['login']['login'], PDO::PARAM_STR);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			$idEleveOuMoniteur=$objRes->$nomId;
		
			// début des suppressions
			
			$idFantome=Utilitaires::getIdFantome();
			
			// suppression des infos perso
			$sql="SELECT idInfo FROM infosPerso INNER JOIN correspondre USING (idInfo) WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			$idInfo=$objRes->idInfo;
			
			$sql="DELETE FROM correspondre WHERE idUser = $idUser; DELETE FROM infosPerso WHERE idInfo = $idInfo";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			// suppression des bannissements
			
			$sql="SELECT idBan FROM estBanni WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$idBans=$query->fetchAll();
			
			$sql="DELETE FROM estBanni WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			foreach ($idBans as $idBan) {
				$sql="DELETE FROM banniPar WHERE idBan = $idBan; DELETE FROM bannir WHERE idBan = " . $idBan['idBan'];
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			// suppression des messages privés
			
			$sql="SELECT idMsg FROM envoyer WHERE idExp = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$msgEnvoyes=$query->fetchAll();
			
			$sql="SELECT idMsg FROM recevoir WHERE idDest = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$msgRecus=$query->fetchAll();
			
			$sql="DELETE FROM envoyer WHERE idExp = $idUser OR idDest = $idUser; DELETE FROM recevoir WHERE idDest = $idUser OR idExp = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			foreach ($msgEnvoyes as $tuple) {
				$sql="DELETE FROM message WHERE idMsg = " . $tuple['idMessage'];
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			foreach ($msgRecus as $tuple) {
				$sql="DELETE FROM message WHERE idMsg = " . $tuple['idMessage'];
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			// suppression des tuples de detientRole
			
			$sql="DELETE FROM detientRole WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			// suppression des tables historique et des signalements
			
			$sql="DELETE FROM posterComHistorique WHERE idUser = $idUser; DELETE FROM creerArticle WHERE idUser = $idUser; DELETE FROM posterMsgPrv WHERE idUser = $idUser; DELETE FROM signalerHistorique WHERE idUser = $idUser; DELETE FROM signalerArt WHERE idUser = $idUser; DELETE FROM signalerCom WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
		
			// suppression des réservations
			
			$sql="SELECT idSeance FROM reserver WHERE $nomId = $idEleveOuMoniteur";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$idsSeances=$query->fetchAll();
			
			$sql="DELETE FROM reserver WHERE $nomId = $idEleveOuMoniteur";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			foreach ($idsSeances as $idSeance) {
				$sql="DELETE FROM seance WHERE idSeance = " . $idSeance['idSeance'];
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			// suppression des droits/limites d'exception
			
			$sql="DELETE FROM userPossedeDroits WHERE idUser = $idUser; DELETE FROM userPossedeLimite WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			if ($nomRole=="eleve") {
				// suppression des compétences
				$sql="DELETE FROM avoirCompetences WHERE idUser = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				
				// suppression des participations aux QCM
				
				$sql="SELECT idParticipation FROM participerQuestion WHERE idUser = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				$idsParticipations=$query->fetchAll();
				
				foreach ($idsParticipations as $tuple) {
					$sql="DELETE FROM participerAvecReponses WHERE idParticipation = " . $tuple['idParticipation'];
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
				
				$sql="DELETE FROM participerQuestion WHERE idUser = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$sql="DELETE FROM userTenteQcm WHERE idUser = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
				
			// suppression des tuples des tables "eleve", "moniteur", "estEleve", "estMoniteur"
			
			$sql="DELETE FROM $nomTableGetId WHERE $nomId = $idEleveOuMoniteur; DELETE FROM $nomRole WHERE $nomId = $idEleveOuMoniteur";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			// articles et commentaires
			
			// Si l'utilisateur souhaite qu'ils restent, alors leur nouveau propriétaire devient l'utilisateur fantôme. Ca servira à afficher
			// les commentaires et articles mais au nom d'un certain "Compte supprimé".
			
			// Si il souhaite les supprimer, on delete partout.
			
			$sql="SELECT idArt FROM posterArt WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$idArts=$query->fetchAll();
			
			$sql="SELECT idCom FROM posterCom WHERE idPosteur = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$idComs=$query->fetchAll();
			
			if ($garderArticlesCom==0) {
			
				$sql="DELETE FROM posterArt WHERE idUser = $idUser; DELETE FROM posterCom WHERE idPosteur = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				foreach ($idComs as $idCom) {
					$sql="DELETE FROM concerner WHERE idCom = " . $idCom['idCom'] . "; DELETE FROM commentaire WHERE idCom = " . $idCom['idCom'];
					$query=parent::$bdd->prepare($sql);
					$query->execute();

				}
				
				foreach ($idArts as $idArt) {
					$sql="DELETE FROM modifierArticle WHERE idArt = " . $idArt['idArt'] . "; DELETE FROM droitsArticleRole WHERE idArt = " . $idArt['idArt'] . "; DELETE FROM article WHERE idArt = " . $idArt['idArt'];
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}

			}
			else {
				
				$sql="UPDATE posterArt SET idUser = $idFantome WHERE idUser = $idUser; UPDATE posterCom SET idPosteur = $idFantome WHERE idPosteur = $idUser";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			
			$sql="UPDATE modifierArticle SET idUser = $idFantome WHERE idUser = $idUser"; 
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$sql="DELETE FROM users WHERE idUser = $idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			return "OK";
			
		}

		
	}

?>
