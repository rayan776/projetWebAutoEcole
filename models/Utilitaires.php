<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class Utilitaires extends ConnectDB {
	
		public static function estActive() {
			$query = parent::$bdd->prepare('SELECT (CASE active WHEN false THEN 0 ELSE 1 END) as active FROM users WHERE login = :login');
			$query->bindValue(':login', $_SESSION['login']['login'], PDO::PARAM_STR);
			$query->execute();
			
			$res = $query->fetch(PDO::FETCH_OBJ);
			
			return $res->active;
		}
		
		public static function remplacerDate($date) {
			$dateFin = new DateTime($date);
			return $dateFin->format("d/m/Y à H:i:s");
		}
		
		public static function remplacerDateSansHeure($date) {
			$dateFin = new DateTime($date);
			return $dateFin->format("d/m/Y");
		}
	
		public static function getIdUser($login) {
			
			parent::connect();
			
			$sql = "SELECT idUser FROM users WHERE login = :login";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			
			return $objRes->idUser;
		}
		
		public static function getIdRoleByName($role) {
			parent::connect();
			
			$sql = "SELECT idRole FROM roles WHERE nomRole = :role";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":role", $role, PDO::PARAM_STR);			
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			
			return $objRes->idRole;
		}
		
		public static function getIdPerm($titrePerm) {
			parent::connect();
			
			$sql = "SELECT idPerm FROM permissions WHERE titrePerm = :titrePerm";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":titrePerm", $titrePerm, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			
			return $objRes->idPerm;
		}
		
		public static function getIdLimite($nomLimite) {
			parent::connect();
			
			$sql = "SELECT idLimite FROM limites WHERE nomLimite = :nomLimite";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":nomLimite", $nomLimite, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			
			return $objRes->idLimite;
		}
		
		public static function infosVisiblesParEleves($login) {
			parent::connect();
			
			$idUser = self::getIdUser($login);
			$sql = "SELECT autoriserElevesVoirInfos FROM correspondre WHERE idUser = $idUser";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
				
			return $objRes->autoriserElevesVoirInfos;
		}
		
		public static function edtVisibleParEleves($login) {
			parent::connect();
			
			$idUser = self::getIdUser($login);
			$sql="SELECT autoriserAutresElevesVoirEDT FROM estEleve WHERE idUser=$idUser";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			if (!$objRes)
				return 0;
			return $objRes->autoriserAutresElevesVoirEDT;
		}
		
		public static function verifierConfirmationMDP() {
		
			$valRetour = array();
			if (!isset($_POST['newMdpConf'])) {
				$valRetour[] = "ERREUR";
			}
			else {
				if (empty($_POST['password'])) {
					$valRetour[] = "Vous n'avez pas saisi votre nouveau mot de passe";
					
				}
				else if ($_POST['password'] != $_POST['newMdpConf']) {
					$valRetour[]="Ce que vous avez saisi dans le champ de confirmation est différent de ce que vous avez saisi dans le champ \"nouveau mot de passe\"";
				}
			}
			return $valRetour;
		}

		public static function getRoleByIdUser($id) {
			parent::connect();
			
			$query = parent::$bdd->prepare('SELECT nomRole FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE idUser=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$role = $query->fetch(PDO::FETCH_OBJ);

			return $role->nomRole;
		}
		
		public static function getRoleCurrentUser() {

			if (!isset($_SESSION['login']))
				return "visiteur";

			parent::connect();
			
			$query = parent::$bdd->prepare('SELECT nomRole FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE login=:login');
			$query->bindValue(':login', $_SESSION['login']['login'], PDO::PARAM_STR);
			$query->execute();
			$role = $query->fetch(PDO::FETCH_OBJ);

			return $role->nomRole;
		}
		
		public static function getRoleUser($login) {
			
			parent::connect();
			
			$query = parent::$bdd->prepare('SELECT nomRole FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE login=:login');
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			$query->execute();
			$role = $query->fetch(PDO::FETCH_OBJ);
			if ($role)
				return $role->nomRole;
			else
				return "";
		}
		
		private static function getTableauPermissions($login) {
			parent::connect();
			
			$query = parent::$bdd->prepare("SELECT titrePerm FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) INNER JOIN rolePossedeDroits USING (idRole) INNER JOIN permissions USING (idPerm) WHERE login = :login");
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			$res = $query->fetchAll();
			
			$tabFinal = array();
			
			foreach ($res as $row) {
				$tabFinal[] = $row['titrePerm'];
			}
			
			return $tabFinal;
		}
		
		public static function possedePermission($login, $titrePerm) {
			parent::connect();
			// on vérifie d'abord si l'utilisateur possède une restriction/autorisation exceptionnelle
			$sql = "SELECT val FROM userPossedeDroits INNER JOIN users USING (idUser) INNER JOIN permissions USING (idPerm) WHERE login = :login AND titrePerm = :titrePerm";
			$query = parent::$bdd->prepare($sql);
			
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->bindValue(":titrePerm", $titrePerm, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes) {
				// si il n'en a pas, alors on regarde si on rôle possède ce droit ou pas.
				return in_array($titrePerm, self::getTableauPermissions($login));
			}
			else {
				return $objRes->val;
			}
		}
		
		public static function virerEspaces($chaine) {
			return implode("", explode(" ", $chaine));
		}
		
		public static function convertirMinutes($minutesAConvertir) {
			// utile pour les limites.
			// convertir des minutes en une chaine de caractères au format "J jours, H heures, M minutes"
			
			$minutes = $minutesAConvertir%60;
	
			$heures = (floor($minutesAConvertir/60))%24;
			
			$jours = floor($minutesAConvertir/1440);

			return "$jours jour(s), $heures heure(s), $minutes minute(s)";
		}
		
		public static function getIntegerFromStrings($number) {
			// vérifie si un String correspond à un entier positif ou nul, inférieur à 10^9, le retourne sous forme d'un entier si c'est le cas, sinon retourne "ERROR"
	
			$numberInt = "ERROR";
		
			$chaineNumber = "";
			for ($i=0; $i<strlen($number); $i++) {
			
				if ($number[$i] != " ") {
					$chaineNumber .= $number[$i];
				}
			}
			
			$number = $chaineNumber;
			
			if ( ($number!="0"&&!empty($number)) || $number=="0") {
				if (strlen($number)<10) {
					if (preg_match('/^[0-9]*$/', $number)) {
						if (strlen($number)==1) {
							$chaineFinale = $number;
						}
						else {
							$chaineFinale = "";
							
							$premierNonZeroTrouve = 0;
							
							for ($i=0; $i<strlen($number); $i++) {
							
								if ($number[$i]=="0" && $premierNonZeroTrouve) {
									$chaineFinale .= $number[$i];
								}
							
								if ($number[$i] != "0") {
									if (!$premierNonZeroTrouve) {
										$premierNonZeroTrouve = 1;
									}
									$chaineFinale .= $number[$i];
								}
								
							}
						}
					
						$numberInt = intval($chaineFinale);
					}
				}
			}	
			
			return $numberInt;
		}
		
		public function getElevesParPrenom() {
			parent::connect();
			$sql = "SELECT login, prenom FROM users INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) INNER JOIN estEleve USING (idEleve) WHERE idUser NOT IN (SELECT idUser FROM estBanni INNER JOIN bannir USING (idBan) WHERE dateFin > CURRENT_TIMESTAMP)";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getMoniteursParPrenom() {
			parent::connect();
			$sql = "SELECT login, prenom FROM users INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) INNER JOIN estMoniteur USING (idUser) WHERE idUser NOT IN (SELECT idUser FROM estBanni INNER JOIN bannir USING (idBan) WHERE dateFin > CURRENT_TIMESTAMP) UNION SELECT login, prenom FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE nomRole = 'admin';";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getPrenomByLogin($login) {
			parent::connect();
			$sql = "SELECT prenom FROM users INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE login = :login";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			return $objRes->prenom;
		}
		
		public function getNbQuestionsParIdQcm($idQcm) {
			parent::connect();
			$sql = "SELECT count(idQuestion) AS nbQuestions FROM qcm INNER JOIN qcmPossedeQuestions USING (idQcm) WHERE idQcm = $idQcm";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			return $objRes->nbQuestions;
		}
		
		public function arrondirPourcentage($percent) {
			
			$partieEntiere = floor(doubleval($percent));
			$partieDecimale = $percent - $partieEntiere;
			
			if ($partieDecimale > 0)
				$partieDecimaleArrondie = "," . mb_substr(strval($partieDecimale), 2, 1);
			else
				$partieDecimaleArrondie = "";
			
			return "$partieEntiere$partieDecimaleArrondie%";
			
		}
		
		public function getDateLundi($ajd) {
		
			if (empty($ajd)) {
				$wd = date("w");
		
				if ($wd==0)
					$wd=7;
				
				if ($wd==1)
					$dateFinale = date("Y-m-d");
				else {
					$joursAReculer = $wd-1;
					
					$dateFinale = date("Y-m-d", strtotime("-$joursAReculer day", strtotime(date("r"))));
				}
			}
			else {
				$date = new DateTime($ajd);
				
				$wd = $date->format("w");
				
				if ($wd==0)
					$wd=7;
				
				if ($wd==1) {
					$dateFinale = $date->format("Y-m-d");
				}
				else {
					$wd--;
					$date->sub(new DateInterval("P$wd" . "D"));
					$dateFinale = $date->format("Y-m-d");
				}
			}
			
			return $dateFinale;
		
		}
	
		
		public function listeSemaines($nbSemaines) {
			
			// retourne un tableau de taille nbSemaines dont les clés sont le lundi de la semaine actuelle (puis les autres N lundi), et les valeurs des String sous la forme "04/11 - 10/11"
			
			// par exemple: "2021-11-04" => "04/11/2021 - 10/11/2021", "2021-11-11" => "11/11/2021 - 17/11/2021", etc
			
			$finalTab = array();
		
			$lundiCetteSemaine = self::getDateLundi("");
			
			for ($i=0; $i<$nbSemaines; $i++) {
		
				$coeff = $i*7;
				
				$lundi = date("Y-m-d", strtotime("+$coeff day", strtotime($lundiCetteSemaine)));
				
				$jourLundi = date("d", strtotime($lundi));
				$moisLundi = date("m", strtotime($lundi));
				$anneeLundi = date("Y", strtotime($lundi));
				
				$jourDimanche = date("d", strtotime("+6 day", strtotime($lundi)));
				$moisDimanche = date("m", strtotime("+6 day", strtotime($lundi)));
				$anneeDimanche = date("Y", strtotime("+6 day", strtotime($lundi)));
				
				$finalTab[$lundi]="$jourLundi/$moisLundi/$anneeLundi - $jourDimanche/$moisDimanche/$anneeDimanche";
				
			}		
	
			return $finalTab;
			
		}
		
		public function listeJoursSemaineAPartirDeLundi($lundi) {
			$tab = array();
			
			$date = new DateTime($lundi);
			
			for ($i=1; $i<=7; $i++) {
				$tab[]=$date->format("Y-m-d");
				$date->add(new DateInterval('P1D'));				
			}
			
			return $tab;
		}
		
		public function getIdEleve($idUser) {
			parent::connect();
			$sql = "SELECT idEleve FROM estEleve WHERE idUser = $idUser";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			if (!$objRes)
				return 0;
			else
				return $objRes->idEleve;
		}
		
		public function getIdMoniteur($idUser) {
			parent::connect();
			$sql = "SELECT idMoniteur FROM estMoniteur WHERE idUser = $idUser";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			else
				return $objRes->idMoniteur;
		}
		
		public function dateToJour($jour) {
			$date = new DateTime($jour);
			
			return $date->format("l d M");
		}
		
		public function getLundiSemaineChoisie() {
		
			$lundi = "";
			
			try {
				if (isset($_POST['sem'])) {
					$date = new DateTime($_POST['sem']);
					$lundi=$date->format("Y-m-d");
				
					if ($date->format("w")!="1")
						$lundi = Utilitaires::getDateLundi("");
				}
				else {
					$lundi = Utilitaires::getDateLundi("");
				}
			}
			catch (Exception $e) {
				$lundi = Utilitaires::getDateLundi(""); 
			}
			
			return $lundi;
		}
		
		public function getLoginPrenomParIdEleveOuMoniteur($eleve, $id) {
		
			parent::connect();
		
			if ($eleve) {
				$sql = "SELECT login, prenom FROM users INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) INNER JOIN estEleve USING (idUser) INNER JOIN eleve USING (idEleve) WHERE idEleve = $id";
			}
			else {
				$sql = "SELECT login, prenom FROM users INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) INNER JOIN estMoniteur USING (idUser) INNER JOIN moniteur USING (idMoniteur) WHERE idMoniteur = $id";
			}
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			if (!$objRes)
				return 0;
			else
				return $objRes;
		}
		
		public function reservationPossible($jour, $heure) {
			$d1 = new DateTime($jour);
			$d2 = new DateTime(date("r"));
			
			$dif = $d2->diff($d1);
			
			$signedays = $dif->format('%R%a');
			
			$signe = $signedays[0];
			$days = intval(substr($signedays, 1));
			
			$heureActuelle=intval(date("H"));
			$reserver=0;
			
			if ($signe=="+") {

				if ($days<1) {
					if ($heure>$heureActuelle) {
						$reserver=1;
					}
				}
				else {
					$reserver=1;
				}
				
			}
			
			return $reserver;
		}
		
		public function roleCommentaire($idCom) {
			// retourne le nom du rôle de l'auteur d'un commentaire
			
			parent::connect();
			
			$sql="SELECT nomRole FROM posterCom INNER JOIN detientRole ON (posterCom.idPosteur=detientRole.idUser) INNER JOIN roles USING (idRole) WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $idCom, PDO::PARAM_INT);
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return "admin";
			else
				return $objRes->nomRole;
		}
		
		public function getLoginById($idUser) {
		
			parent::connect();
			
			$sql="SELECT login FROM users WHERE idUser = :idUser";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			return $objRes->login;
		}
		
		public function estBanni($login) {
		
			parent::connect();
		
			$sql="SELECT * FROM estBanni INNER JOIN users USING (idUser) INNER JOIN bannir USING (idBan) WHERE login = :login AND dateFin > CURRENT_TIMESTAMP";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			else
				return 1;
		}
		
		public function nouveauxMessages($login) {
		
			parent::connect();
		
			$sql="SELECT count(*) AS count FROM recevoir INNER JOIN users ON (recevoir.idDest=users.idUser) WHERE login = :login AND dejaLu=0";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			else
				return $objRes->count>0;
		}
		
		public function getIdFantome() {
			// retourne l'ID de l'utilisateur fantôme
			
			parent::connect();
			
			$sql="SELECT idUser FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE nomRole = 'supprime'";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			return $objRes->idUser;
		}
		
		public function getIdRobot() {
			// retourne l'ID de l'utilisateur robot
			
			parent::connect();
			
			$sql="SELECT idUser FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE nomRole = 'robot'";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			return $objRes->idUser;
		}
			
		
	}
	
?>
