<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	header ('Content-type: text/html; charset=utf-8');
	
	class ModeleAccueil extends ConnectDB {
		
		function __construct() {
			parent::connect();
		}
		
		
		function verifierLoginMdp() {
		
			if (!isset($_POST['login']) || !isset($_POST['password']))
				return "Erreur";
		
			if ( (empty($_POST['login'])) || (empty($_POST['password'])) ) {
				return "Au moins l'un des deux champs est vide.";
			}
		
			$login=isset($_POST['login']) ? trim($_POST['login']) : "";
			$mdp=isset($_POST['password']) ? hash("sha256", $_POST['password']) : "";
			
			// vérifier que le login existe
			$query=parent::$bdd->prepare('SELECT login FROM users WHERE login=:login');
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			$query->execute();
			
			$donnees = $query->fetchAll();
			if (count($donnees) == 0) {
				return "Ce nom d'utilisateur n'existe pas dans notre base de données.";
			}
			
			// vérifier que le compte n'est pas un compte "robot" ou "supprimé"
			
			$query=parent::$bdd->prepare("SELECT nomRole FROM roles INNER JOIN detientRole USING (idRole) INNER JOIN users USING (idUser) WHERE login=:login");
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes->nomRole=="robot"||$objRes->nomRole=="supprime")
				die();
			
			// si il existe, vérifier que le mot de passe saisi correspond
			
			$query = parent::$bdd->prepare('SELECT login, password FROM users WHERE login=:login AND password=:password');
			
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			$query->bindValue(':password', $mdp, PDO::PARAM_STR);
			
			$query->execute();
			
			$donnees = $query->fetchAll();
			
			if (count($donnees) == 1)
				return 1;
			else
				return "Le mot de passe ne correspond pas à celui du nom d'utilisateur saisi.";
				
			
		}
		
		function validerConnexion() {
			$tenterConnexion = $this->verifierLoginMdp();
			
			if ($tenterConnexion == 1) {
				$_SESSION['login'] = array();
				$_SESSION['login']['login'] = $_POST['login'];

			}
			
			return $tenterConnexion;
			
		}
		
		public function nouveauCompte($login, $mdp, $infos) {
			
			// infos persos
			
			$neph=($infos["type"]=="eleve") ? $infos["neph"] : "NULL";
			
			$query=parent::$bdd->prepare("INSERT INTO users (login, password) VALUES (:login, :password)");
			
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->bindValue(":password", $mdp, PDO::PARAM_STR);
			
			$query->execute();
			
			$idUser = Utilitaires::getIdUser($login);
			$idRole = Utilitaires::getIdRoleByName($infos["type"]);
			
			$sql="INSERT INTO detientRole VALUES ($idUser, $idRole)";
			$query=parent::$bdd->prepare($sql);
			
			$query->execute();
			
			$sql="INSERT INTO infosPerso (nom, prenom, numTel, ville, codePostal, NEPH) VALUES (:nom, :prenom, :numTel, :ville, :cp, :neph)";
			
			$query=parent::$bdd->prepare($sql);
			
			$query->bindValue(":nom", $infos["nom"], PDO::PARAM_STR);
			$query->bindValue(":prenom", $infos["prenom"], PDO::PARAM_STR);
			$query->bindValue(":numTel", $infos["phone"], PDO::PARAM_STR);
			$query->bindValue(":ville", $infos["ville"], PDO::PARAM_STR);
			$query->bindValue(":cp", $infos["cp"], PDO::PARAM_STR);
			$query->bindValue(":neph", $neph, PDO::PARAM_STR);
			
			$query->execute();
			
			$idInfo = parent::$bdd->lastInsertId();
			
			$sql = "INSERT INTO correspondre (idInfo, idUser) VALUES ($idInfo, $idUser)";
			$query = parent::$bdd->prepare($sql);
			
			$query->execute();
			return "1";
			
		}
		
		public function inscrireCompte() {
		
			$errors = array();
			
			if (Tokens::checkCSRF()) {
				$errors[]="Le formulaire a expiré, veuillez réessayer.";
				return $errors;
			}
			
			// vérifications concernant le login et le MDP
		
			if ( (strlen($_POST['login']) == 0) || (strlen($_POST['password']) == 0)) {
				$errors[] = "Vous n'avez pas saisi votre nom d'utilisateur et/ou votre mot de passe.";
				return $errors;
			}
			
			$validateur = new Validateur();
			
			$errors = $validateur->verifierUsername();
			
			$errorsPwd = $validateur->verifierPwd();
			
			if (count($errorsPwd) > 0) {
				for ($i=0; $i<count($errorsPwd); $i++) {
					$errors[]=$errorsPwd[$i];
				}
			}
			
			if (count($errors) > 0)
				return $errors;
			
			$errors = Utilitaires::verifierConfirmationMDP();
			
			if (count($errors) > 0)
				return $errors;
			
			$login = isset($_POST['login']) ? $_POST['login'] : "";
			$mdp = isset($_POST['password']) ? hash("sha256", $_POST['password']) : "";
			
			// vérifications concernant les infos perso
		
			$errors = $validateur->verifierInfosPerso(1);
			
			if (count($errors) == 0) {
				// une fois que les vérfications sont complètes, insertion des lignes correspondant au nouveau compte
				
				$infos = array();
				
				$infos['nom'] = isset($_POST['nom']) ? strtolower(trim($_POST['nom'])) : "";
				$infos['prenom'] = isset($_POST['prenom']) ? strtolower(trim($_POST['prenom'])) : "";
				$infos['ville'] = isset($_POST['ville']) ? strtolower(trim($_POST['ville'])) : "";
				$infos['cp'] = isset($_POST['cp']) ? $_POST['cp'] : "";
				$infos['phone'] = isset($_POST['phone']) ? Utilitaires::virerEspaces($_POST['phone']) : "";
				$infos['type'] = isset($_POST['type']) ? $_POST['type'] : "";
				$infos['neph'] = isset($_POST['neph']) ? Utilitaires::virerEspaces($_POST['neph']) : "";
			
				$creerCompte = $this->nouveauCompte($login, $mdp, $infos);
				
				if ($creerCompte != "1")
					$errors[] = $creerCompte;
			}
		
			return $errors;
			
		}
		
		public function getFormules() {
			$sql="SELECT * FROM formules";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function lastPublishedArticles() {
			$sql="SELECT idArt, idUser, nomArt, datePub, login FROM article INNER JOIN posterArt USING (idArt) INNER JOIN users USING (idUser) ORDER BY datePub DESC LIMIT 5";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getAnnonce() {
			$sql="SELECT * FROM annonce";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetch(PDO::FETCH_OBJ);
		}
	}
	


?>
