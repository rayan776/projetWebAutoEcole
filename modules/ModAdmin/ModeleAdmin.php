<?php

if (!defined('CONSTANTE'))
	die("Accès interdit");

	class ModeleAdmin extends ConnectDB {
	
		function __construct() {
			parent::connect();
		}
		
		function recupererListeUsers() {
				$query = parent::$bdd->prepare("SELECT idUser, login, nomRole, active FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE nomRole NOT IN (SELECT nomRole FROM roles WHERE nomRole = 'robot' OR nomRole = 'supprime') ORDER BY login ASC");
				$query->execute();
				$resultats = $query->fetchAll();
				return $resultats;
		}
		
		function activerCompte() {
		
			if (!isset($_POST['idUser'])||!isset($_POST['nomRole']))
				return;
			
			$idUser=$_POST['idUser'];
			$nomRole=$_POST['nomRole'];
		
			// si il s'agit d'un élève, il faut d'abord lui ajouter les compétences présentes dans la base de données.
			
			/* on fait ces opérations au moment de l'activation, car ça implique d'insérer pas mal de lignes dans la BD; or ce n'est pas sûr que l'admin
			 décide d'activer un compte. si on faisait ça au moment de l'inscription, la BD pourrait se retrouver surchargée par des gens qui s'amusent
			à créer plein de comptes */
			
			if ($nomRole=="eleve") {
				$sql = "SELECT idComp FROM competences";
				$query = parent::$bdd->prepare($sql);
				$query->execute();
				
				$listeIdComps = $query->fetchAll();
				
				foreach ($listeIdComps as $tuple) {
					$idComp = $tuple['idComp'];
					$sql = "INSERT INTO avoirCompetences (idUser, idComp) VALUES (:idUser, $idComp)";
					$query = parent::$bdd->prepare($sql);					
					$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
					$query->execute();
				}
				
				
				
			}
			
			// insérer un ID dans la table "eleve" ou "moniteur", puis l'associer à son user ID dans "estEleve" ou "estMoniteur" (utile pour la table reserver et le débannissement)
			$tableAssociation = ($nomRole=="eleve") ? "estEleve" : "estMoniteur";
			
			$sql = "INSERT INTO $nomRole VALUES()";
			
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$lastInsertIdEleveOuMoniteur = parent::$bdd->lastInsertId();
			
			if ($tableAssociation=="estEleve") {
				$sql = "INSERT INTO $tableAssociation VALUES ($lastInsertIdEleveOuMoniteur, :idUser, 0)";
			}
			else {
				$sql = "INSERT INTO $tableAssociation VALUES ($lastInsertIdEleveOuMoniteur, :idUser)";
			}
			
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);			
			$query->execute();

			// activation		
			$sql = "UPDATE users SET active = 1 WHERE idUser = $idUser";
			$query = parent::$bdd->prepare($sql);
			
			$query->execute();
		}
		
		public function listeRoles() {
			$sql = "SELECT nomRole FROM roles WHERE nomRole = 'eleve' OR nomRole = 'moniteur'";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}

		public function getDroits() {
		
			if (isset($_POST['modifierDroitsType'])) {
				if (isset($_POST['nomRoleOuUser'])) {
					$_POST['droitsDeQui'] = $_POST['modifierDroitsType'];
					
					if ($_POST['droitsDeQui']=="afficherParRole")
						$_POST['nomRole'] = $_POST['nomRoleOuUser'];
					elseif ($_POST['droitsDeQui']=="afficherParUser")
						$_POST['login']=$_POST['nomRoleOuUser'];
						
				}
			}
			
		
			if ($_POST['droitsDeQui']=="afficherParRole") {
				if (isset($_POST['nomRole'])) {

					if (Utilitaires::getIdRoleByName($_POST['nomRole'])==0)
						die();
						
					return self::getDroitsRole($_POST['nomRole']);
				}
			}
			else if ($_POST['droitsDeQui']=="afficherParUser") {
				if (isset($_POST['login'])) {
					if (Utilitaires::getIdUser($_POST['login']) != 0)
						return self::getDroitsUser($_POST['login']);
					else
						return array(-1);
				}
			}
			
			die();
		}
		
		public function getLimites() {
			if (isset($_POST['modifierLimitesType'])) {
				if (isset($_POST['nomRoleOuUser'])) {
					$_POST['limitesDeQui'] = $_POST['modifierLimitesType'];
					
					if ($_POST['limitesDeQui']=="afficherParRole")
						$_POST['nomRole'] = $_POST['nomRoleOuUser'];
					elseif ($_POST['limitesDeQui']=="afficherParUser")
						$_POST['login']=$_POST['nomRoleOuUser'];
				}
			}
			
			if ($_POST['limitesDeQui']=="afficherParRole") {
				if (isset($_POST['nomRole'])) {

					if (Utilitaires::getIdRoleByName($_POST['nomRole'])==0)
						die();
						
					return Limites::getLimitesRole($_POST['nomRole']);
				}
			}
			else if ($_POST['limitesDeQui']=="afficherParUser") {
				if (isset($_POST['login'])) {
					if (Utilitaires::getIdUser($_POST['login']) != 0)
						return Limites::getLimitesUser($_POST['login']);
					else
						return array(-2);
				}
			}
			
			die();
			
		}
		
			
		public function getListAllLimites() {
			$query = parent::$bdd->prepare("SELECT nomLimite FROM limites");
			$query->execute();
			return $query->fetchAll();
		}
		
		private function getDroitsRole($nomRole) {
			$query = parent::$bdd->prepare("SELECT titrePerm FROM rolePossedeDroits INNER JOIN permissions USING (idPerm) INNER JOIN roles USING (idRole) WHERE nomRole = :nomRole");
			$query->bindValue(":nomRole", $nomRole, PDO::PARAM_STR);
			$query->execute();
			return $query->fetchAll();
		}
		
		private function getDroitsUser($loginUser) {
			$query = parent::$bdd->prepare("SELECT titrePerm, CASE WHEN val = 0 THEN 'Interdiction' ELSE 'Autorisation' END AS val FROM userPossedeDroits INNER JOIN permissions USING (idPerm) INNER JOIN users USING (idUser) WHERE login = :login");
			$query->bindValue(":login", $loginUser, PDO::PARAM_STR);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getListAllDroits() {
			$query = parent::$bdd->prepare("SELECT titrePerm FROM permissions");
			$query->execute();
			return $query->fetchAll();
		}
		
		public function modifierDroits() {
		
			if (Tokens::checkCSRF()) {
				return "Formulaire expiré, veuillez réessayer.";
			}
			
			if (!isset($_POST['modifierDroitsType'])||!isset($_POST['nomRoleOuUser'])||!isset($_POST['titrePerm'])||!isset($_POST['autoriserOuInterdireDroit']))
				return "Erreur";
			
			$reussi = "Vos modifications ont bien été prises en compte.";
			
			// savoir si on doit modifier les droits d'un utilisateur ou pour un rôle
				
			// autorisations/interdictions exceptionnelles d'un utilisateur
			if ($_POST['modifierDroitsType']=="afficherParUser") {
				
				// login de l'utilisateur
				$nomUser = $_POST['nomRoleOuUser'];
				
				if (!Utilitaires::getIdUser($nomUser))
					return "Erreur";
				
				// quel droit ?
				
				$idPerm = Utilitaires::getIdPerm($_POST['titrePerm']);
				if (!$idPerm)
					return "Erreur";
				
				// veut-on autoriser ou interdire le droit?

				if ($_POST['autoriserOuInterdireDroit']=="autoriser"||$_POST['autoriserOuInterdireDroit']=="interdire") {
				
					$val = ($_POST['autoriserOuInterdireDroit']=="autoriser") ? 1 : 0;
					
					// on part du principe où l'utilisateur ne possède pas cette autorisation/interdiction exceptionnelle, on tente le insert
					$sql = "INSERT INTO userPossedeDroits VALUES (:idUser, :idPerm, $val)";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idUser", Utilitaires::getIdUser($nomUser), PDO::PARAM_INT);
					$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
					
					if ($query->execute()) {
						return $reussi;
					}
					else {
						// si ça retourne false c'est que la contrainte de clé primaire est violée: l'exception existe déjà, on fait donc un update
						$sql = "UPDATE userPossedeDroits SET val = $val WHERE idUser = :idUser AND idPerm = :idPerm";
						$query = parent::$bdd->prepare($sql);
						$query->bindValue(":idUser", Utilitaires::getIdUser($nomUser), PDO::PARAM_INT);
						$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
						$query->execute();
						
						return $reussi;
					}
					
				}
				else if ($_POST['autoriserOuInterdireDroit']=="supprimer") {
					// suppression d'une autorisation/interdiction exceptionnelle pour cet utilisateur
					$sql = "DELETE FROM userPossedeDroits WHERE idUser = :idUser AND idPerm = :idPerm";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idUser", Utilitaires::getIdUser($nomUser), PDO::PARAM_INT);
					$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
					$query->execute();
					
					return $reussi;
				}
				
			}
			else if ($_POST['modifierDroitsType']=="afficherParRole") {
				// droits d'un rôle
				
				// nom du rôle
				$nomRole = $_POST['nomRoleOuUser'];
				
				$idRole = Utilitaires::getIdRoleByName($nomRole);
				if (!$idRole)
					return "Erreur";
				
				// quel droit ?
				
				$idPerm = Utilitaires::getIdPerm($_POST['titrePerm']);
				if (!$idPerm)
					return "Erreur";
					
				// veut-on l'autoriser ou l'interdire (le supprimer) ?
				
				if ($_POST['autoriserOuInterdireDroit']=="autoriser") {
					
					// on part du principe où ce rôle ne possède pas ce droit, on tente de l'insérer
					
					$sql = "INSERT INTO rolePossedeDroits VALUES (:idRole, :idPerm)";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idRole", $idRole, PDO::PARAM_INT);
					$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
					
					if ($query->execute()) {
						return $reussi;
					}
					else {
						// contrainte de clé primaire violée : l'autorisation existe donc déjà
						return "Ce rôle possède déjà cette autorisation.";
					}
				}
				else if ($_POST['autoriserOuInterdireDroit']=="interdire") {
					// on vérifie si le droit n'est pas déjà interdit à ce rôle
					
					$sql = "SELECT * FROM rolePossedeDroits WHERE idRole = :idRole AND idPerm = :idPerm";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idRole", $idRole, PDO::PARAM_INT);
					$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
					$query->execute();
					$res = $query->fetch(PDO::FETCH_OBJ);
					
					// le droit est déjà interdit pour ce rôle
					if (!$res) {
						return "Ce rôle possède déjà cette interdiction.";
					}
					
					// le droit est autorisé pour ce rôle => suppression du droit
					
					$sql = "DELETE FROM rolePossedeDroits WHERE idRole = :idRole AND idPerm = :idPerm";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idRole", $idRole, PDO::PARAM_INT);
					$query->bindValue(":idPerm", $idPerm, PDO::PARAM_INT);
					$query->execute();
					
					return $reussi;
				}
				
			}
				
			
			return "Erreur";	
			
		}
		
		public function modifierLimites() {
			if (Tokens::checkCSRF()) {
				return "Formulaire expiré, veuillez réessayer.";
			}
		
			$reussi = "Vos modifications ont bien été prises en compte.";
			$erreurNombre = "L'un des nombres est invalide. Ils doivent être entiers, et compris entre 0 et 999 999 999.";
			$valeurLimite = 0;
			$totalMinutes = 0;
			
			// ces trois variables sont obligatoires
			if(!isset($_POST['modifierLimitesType'])||!isset($_POST['nomRoleOuUser'])||!isset($_POST['nomLimite']))
				return "Erreur";
				
			
			// ensuite reste à savoir si on cherche à supprimer/rendre infini ou non.
			if (!isset($_POST['supprimerLimite'])&&!isset($_POST['infiniLimite'])) {
				if (!isset($_POST['valeurLimite'])||!isset($_POST['joursLimite'])||!isset($_POST['heuresLimite'])||!isset($_POST['minutesLimite']))
					return "Erreur";
			}
			else {
				// nombre illimité d'actions (en cas de choix d'infinité). En cas de suppression, on ne tient même pas compte de ça
				$valeurLimite = -1;
			}
		
			// vérifier que la limite existe
			$nomLimite = $_POST['nomLimite'];
			
			$idLimite = Utilitaires::getIdLimite($nomLimite);
			
			if (!$idLimite)
				return "Erreur";
			
			// la valeur de la limite doit être un nombre compris entre 0 et 999 999 999
				
			if ($valeurLimite != -1) {
				// dans le cas d'une valeur qui n'est pas infinie.
				$valeurLimite=Utilitaires::getIntegerFromStrings($_POST['valeurLimite']);
				
				if ($valeurLimite == "ERROR")
					return $erreurNombre;
				
				// déterminer la période glissante choisie par l'admin pour imposer la limite
				
				$jours = (!empty($_POST['joursLimite'])) ? Utilitaires::getIntegerFromStrings($_POST['joursLimite']) : 0;
				
				if (strval($jours)!="0")
					if ($jours == "ERROR")
						return $erreurNombre;
					
				$heures = (!empty($_POST['heuresLimite'])) ? Utilitaires::getIntegerFromStrings($_POST['heuresLimite']) : 0;
				
				if (strval($heures)!="0")
					if ($heures == "ERROR")
						return $erreurNombre;
				
				$minutes = (!empty($_POST['minutesLimite'])) ? Utilitaires::getIntegerFromStrings($_POST['minutesLimite']) : 0;
				
				if (strval($minutes)!="0")
					if ($minutes == "ERROR")
						return $erreurNombre;
				
			
				
				$totalMinutes = $jours*1440 + $heures*60 + $minutes;
								
			}
			
			// si on veut changer la limite d'un rôle
			if ($_POST['modifierLimitesType']=="afficherParRole") {
				
				// vérifier que le rôle existe
				$nomRole = $_POST['nomRoleOuUser'];
				
				// le rôle doit être forcément eleve ou moniteur
				if ($nomRole!="eleve"&&$nomRole!="moniteur")
					return "Erreur";
				
				$idRole = Utilitaires::getIdRoleByName($nomRole);
				if (!$idRole)
					return "Erreur";
				
				// update de la limite avec les nouvelles valeurs
				
				$sql="UPDATE rolePossedeLimite SET val = :newVal, period = :newPeriod WHERE idRole = :idRole AND idLimite = :idLimite";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":newVal", $valeurLimite, PDO::PARAM_INT);
				$query->bindValue(":newPeriod", $totalMinutes, PDO::PARAM_INT);
				$query->bindValue(":idRole", $idRole, PDO::PARAM_INT);
				$query->bindValue(":idLimite", $idLimite, PDO::PARAM_INT);
				$query->execute();
				
				return $reussi;
			}
			else if ($_POST['modifierLimitesType']=="afficherParUser") {
			
				// vérifier que l'utilisateur existe
				$login=$_POST['nomRoleOuUser'];
				
				$idUser=Utilitaires::getIdUser($login);
				if (!$idUser)
					return "Erreur";
				
				// le rôle doit être forcément eleve ou moniteur
				$nomRole=Utilitaires::getRoleUser($login);
				if ($nomRole!="eleve"&&$nomRole!="moniteur")
					return "Erreur";
				
				// veut-on modifier ou supprimer la limite?
				
				// supprimer
				if (isset($_POST['supprimerLimite'])) {
					$sql="DELETE FROM userPossedeLimite WHERE idLimite = :idLimite AND idUser = :idUser";
					$query=parent::$bdd->prepare($sql);					
					$query->bindValue(":idLimite", $idLimite, PDO::PARAM_INT);
					$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
					$query->execute();
					
					return $reussi;				
				}
				
				// modifier : on part du principe où elle n'existe pas: on tente d'abord l'insert
				
				$sql="INSERT INTO userPossedeLimite VALUES ($idUser, $idLimite, $valeurLimite, $totalMinutes)";
				$query=parent::$bdd->prepare($sql);
				
				if (!$query->execute()) {
					// la limite existe déjà, donc on l'update
					$sql="UPDATE userPossedeLimite SET val = $valeurLimite, period = $totalMinutes WHERE idUser = $idUser AND idLimite = $idLimite";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					
					return $reussi;
				}
			
				return $reussi;
			}
			
			return "Erreur";
			
		}
		
		public function getListBans() {
			$res=array();
			
			$sql="SELECT idBan, idUser, login, motif, dateFin FROM bannir INNER JOIN estBanni USING (idBan) INNER JOIN users USING (idUser)";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$res[0]=$query->fetchAll();
			
			$sql="SELECT idBan, login FROM banniPar INNER JOIN bannir USING (idBan) INNER JOIN users USING (idUser);";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$requete2=$query->fetchAll();
			
			$banniPar=array();
			
			foreach ($requete2 as $tuple) {
				$banniPar[$tuple['idBan']]=$tuple['login'];
			}
			
			$res[1]=$banniPar;
			
			return $res;
		}
		
		public function supprimerBans() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
				
			if (!isset($_POST['idBan'])||!is_array($_POST['idBan']))
				return "Vous n'avez rien coché.";
			
			foreach ($_POST['idBan'] as $idBan) {
			
				$peutDebannir=0;
			
				if (Utilitaires::getRoleCurrentUser()=="moniteur") {
					// un moniteur ne peut débannir que des élèves
					
					$sql="SELECT nomRole FROM estBanni INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE idBan = :idBan";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idBan", $idBan, PDO::PARAM_INT);
					$query->execute();
					$objRes=$query->fetch(PDO::FETCH_OBJ);
					
					if ($objRes) {
						if ($objRes->nomRole=="eleve") {
							$peutDebannir=1;
						}
					}
					
				}
				else {
					$peutDebannir=1;
				}
				
				if ($peutDebannir==1) {
					$sql="DELETE FROM estBanni WHERE idBan = :idBan; DELETE FROM banniPar WHERE idBan = :idBan; DELETE FROM bannir WHERE idBan = :idBan";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idBan", $idBan, PDO::PARAM_INT);
					$query->execute();
				}
				
			
			}
			
			return "Opération terminée.";
			
			
		}
		
		public function bannir() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['loginToBan'])||!isset($_POST['dateFinBan']))
				return "Erreur.";
			
			if (empty($_POST['loginToBan']))
				return "Veuillez saisir un login.";
			
			$idUser=Utilitaires::getIdUser($_POST['loginToBan']);
			
			if (!$idUser)
				return "Login inexistant.";
			
			// on ne doit pas pouvoir se bannir soi-même
			
			if ($_SESSION['login']['login']==$_POST['loginToBan'])
				return "Vous ne pouvez pas vous bannir vous-même.";
			
			$sql="SELECT nomRole FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE login = :login";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $_POST['loginToBan'], PDO::PARAM_STR);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes->nomRole=="admin")
				return "Il est impossible de bannir un administrateur.";
			
			if (Utilitaires::getRoleCurrentUser()=="moniteur"&&$objRes->nomRole=="moniteur")
				return "Vous ne pouvez pas bannir l'un de vos collègues moniteurs.";
			
			if (Utilitaires::getRoleCurrentUser()=="moniteur"&&$objRes->nomRole=="eleve") {
				if (!Utilitaires::possedePermission($_SESSION['login']['login'], "bannir eleve"))
					return "Vous n'avez pas le droit de bannir d'élèves.";
			}
			
			// vérification de la date
			
			try {
				$dateFin = new DateTime($_POST['dateFinBan']);
				$currentDate = new DateTime(date("r"));
				
				$ecart=$dateFin->diff($currentDate);
				
				$dif=$ecart->format("%R%a");
				
				if ($dif[0]=="+") {
					throw new Exception();
				}
				
				if ($dateFin->format("Y-m-d")==$currentDate->format("Y-m-d"))
					throw new Exception();
				
				
				$dateFinBan=$dateFin->format("Y-m-d");
				
			}
			catch (Exception $e) {
				return "Date invalide. La date doit être postérieure à la date actuelle.";
			}
			
			
			// le motif est optionnel
			
			$motif=isset($_POST['motifBan'])?htmlspecialchars(trim($_POST['motifBan']), ENT_QUOTES):"";
			
			// si le bannissement existe déjà, il suffit de faire un update et d'écraser. sinon ça sera des insert.
			
			$update=0;
			
			$sql="SELECT idBan FROM estBanni INNER JOIN bannir USING (idBan) WHERE idUser = $idUser AND dateFin > CURRENT_TIMESTAMP";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes) {
				$idBan=$objRes->idBan;
				$update=1;
			}
			
			// prépararation des requêtes
			
			$idUserActu=Utilitaires::getIdUser($_SESSION['login']['login']);
			
			if ($update==1) {
						
				$sql="UPDATE bannir SET motif = :motif, dateFin = :dateFin WHERE idBan = :idBan; UPDATE banniPar SET idUser = :idUserActu WHERE idBan = :idBan";
				
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":motif", $motif, PDO::PARAM_STR);
				$query->bindValue(":dateFin", $dateFinBan, PDO::PARAM_STR);
				$query->bindValue(":idBan", $idBan, PDO::PARAM_INT);
				$query->bindValue(":idUserActu", $idUserActu, PDO::PARAM_INT);
				
				$query->execute();
				
				return "Bannissement mis à jour.";
				
			}
			else {
				$sql="INSERT INTO bannir (motif, dateFin) VALUES (:motif, :dateFin)";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":motif", $motif, PDO::PARAM_STR);
				$query->bindValue(":dateFin", $dateFinBan, PDO::PARAM_STR);
				$query->execute();
				
				$idBan=parent::$bdd->lastInsertId();
				
				$sql="INSERT INTO estBanni VALUES ($idUser, $idBan)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$sql="INSERT INTO banniPar VALUES ($idBan, $idUserActu)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				return "Utilisateur banni avec succès.";
				
			}
		}
		
		public function getFormules() {
			$sql="SELECT * FROM formules";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function addFormule() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['titreFormule'])||!isset($_POST['descFormule']))
				return "Erreur.";
			
			$titre=htmlspecialchars(trim($_POST['titreFormule']), ENT_QUOTES);
			$desc=htmlspecialchars(trim($_POST['descFormule']), ENT_QUOTES);
			
			if (strlen($titre)<1||strlen($titre)>30)
				return "Le titre doit être compris entre 1 et 30 caractères.";
			
			if (strlen($desc)<1||strlen($desc)>50)
				return "La description doit être comprise entre 1 et 50 caractères.";
			
			$sql="INSERT INTO formules (titre, description) VALUES (:titre, :description)";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":titre", $titre, PDO::PARAM_STR);
			$query->bindValue(":description", $desc, PDO::PARAM_STR);
			
			$query->execute();
			
			return "Formule ajoutée.";
			
		}
		
		public function updateFormule() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
				
			$nbUpdate=0;
			
			if (!isset($_POST['idFormule']))
				return "Vous n'avez rien coché.";
			
			foreach ($_POST['idFormule'] as $idFormule) {
				if (isset($_POST['titre'][$idFormule])) {
					$titre=htmlspecialchars(trim($_POST['titre'][$idFormule]), ENT_QUOTES);
					if (isset($_POST['desc'][$idFormule])) {
						$desc=htmlspecialchars(trim($_POST['desc'][$idFormule]), ENT_QUOTES);					
						$sql="UPDATE formules SET titre = :titre, description = :description WHERE idFormule = :idFormule";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":titre", $titre, PDO::PARAM_STR);
						$query->bindValue(":description", $desc, PDO::PARAM_STR);
						$query->bindValue(":idFormule", $idFormule, PDO::PARAM_INT);
						$query->execute();
						$nbUpdate++;
					}
					
				}
			}
			
			return "$nbUpdate formule(s) mise(s) à jour.";
		}
		
		public function deleteFormule() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			$nbDelete=0;
			
			if (!isset($_POST['idFormule']))
				return "Vous n'avez rien coché.";
				
			foreach ($_POST['idFormule'] as $idFormule) {
				$sql="DELETE FROM formules WHERE idFormule = :idFormule";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":idFormule", $idFormule, PDO::PARAM_INT);
				$query->execute();
				$nbDelete++;
			}
			
			return "$nbDelete formule(s) supprimée(s).";
		}
		
		public function getAnnonce() {
			$sql="SELECT * FROM annonce";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			return $objRes;
		}
		
		public function changerAnnonce() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['annonce'])||!isset($_POST['color'])||!isset($_POST['bgColor']))
				return "Erreur.";
				
			// valider couleurs
			
			$color=trim($_POST['color']);
			$bgColor=trim($_POST['bgColor']);
			
			if ($color[0]!="#"||$bgColor[0]!="#"||strlen($color)!=7||strlen($bgColor)!=7)
				return "Erreur.";		
			
			$hexa=array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
			
			for ($i=1; $i<7; $i++) {
				if (!in_array($color[$i], $hexa)||!in_array($bgColor[$i], $hexa))
					return "Erreur.";
			}
			
			$sql="UPDATE annonce SET annonce = :annonce, color = :color, bgColor = :bgColor";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":annonce", htmlspecialchars(trim($_POST['annonce']), ENT_QUOTES), PDO::PARAM_STR);
			$query->bindValue(":color", $color, PDO::PARAM_STR);
			$query->bindValue(":bgColor", $bgColor, PDO::PARAM_STR);	
			$query->execute();
			
			return "Mise à jour faite avec succès.";		
			
			
		}
	
	}
	
?>
