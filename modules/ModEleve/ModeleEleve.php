<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

require_once "FonctionsQcm.php";
	
	class ModeleEleve extends ConnectDB {
	
		public function __construct() {
			parent::connect();
		}
		
		public function getMoniteurs() {
			$sql="SELECT login, prenom, nom FROM users INNER JOIN estMoniteur USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo)";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getListeQcm() {
			$sql="SELECT idQcm, nomQcm FROM qcm WHERE autoriser=1";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function qcmAutorise($idQcm) {
			$sql="SELECT idQcm FROM qcm WHERE autoriser=1 AND idQcm = :idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			
			return 1;
		}
		
		public function preparerNouveauQcm() {
			
			$idQcm=intval($_POST['idQcm']);
			$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
			$newQcm=new FonctionsQcm($idQcm, $idUser);
			
			return $newQcm->insererTentative();
			
		}
		
		public function questionSuivante($idQcm, $idTentative) {
		
			if(isset($_POST['idTentative'])){
				$idTentative = intval($_POST['idTentative']);
				if (!$idTentative)
					die();
			}
			else{
				die();
			}
			
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
			
			$newQcm = new FonctionsQcm($idQcm, $idUser);
			
			// on regarde si le QCM est déjà fini (les résultats ont déjà été affichés) au moment où cette méthode est appelée
			
			if ($newQcm->qcmTermine($idTentative))
				return array(2);
			
			// ce n'est pas encore fini, donc on vérifie, si une réponse a été envoyée, est ce qu'elle est juste ou pas
			
			$codeRetour=$newQcm->verifierReponse();
			
			if ($codeRetour==-1)
				return array(-1);
			
			// on prépare la prochaine question (ou les résultats du QCM si la dernière question vient d'être complétée) et on envoie à la vue
			return $newQcm->preparerProchaineQuestion();
			
			
		}
		
		public function getListeQcmEffectues() {
		
			$idEleve = Utilitaires::getIdUser($_SESSION['login']['login']);
			$triParNom = "";
			$triParNote = "";
			$triParDate = "";
			$trierNoteMin = "";
			$noteMin = "";
			$trierNoteMax = "";
			$noteMax = "";
			
			// gestion du tri
			if (isset($_POST['trierQcm'])&&is_array($_POST['trierQcm'])) {
			
				if (in_array("trierParNom", $_POST['trierQcm'])) {
					$triParNom = " AND idQcm = :idQcm";
				}
			
				if (in_array("trierParNote", $_POST['trierQcm'])) {
					switch ($_POST['trierParNote']) {
						case "ASC":
						case "DESC":
							$triParNote = " ORDER BY pourcentageReussite " . $_POST['trierParNote'] . " ";
							break;
						default:
							die();
					}
					
					if (isset($_POST['trierNoteMin'])) {
						$noteMin = intval($_POST['trierNoteMin']);
						if ($noteMin < 0 || $noteMin > 100) // un qcm ne peut pas contenir plus de 40 questions
							die();
						$trierNoteMin = " AND pourcentageReussite >= :noteMin ";
					}
					if (isset($_POST['trierNoteMax'])) {
						$noteMax = intval($_POST['trierNoteMax']);
						if ($noteMax < 0 || $noteMax > 100)
							die();
						$trierNoteMax = " AND pourcentageReussite <= :noteMax ";
					}
				}
				
				if (in_array("trierParDate", $_POST['trierQcm'])) {
					$triParDate = " AND dateTentative >= :dateDeb AND dateTentative <= :dateFin ";
				}
			}
			
			$sql = "SELECT idTentative FROM userTenteQcm INNER JOIN qcm USING (idQcm) WHERE idUser = $idEleve $triParDate $trierNoteMin $trierNoteMax $triParNom AND termine=1 $triParNote";
			
			$query=parent::$bdd->prepare($sql);
			if (!empty($triParDate)) {
				$query->bindValue(":dateDeb", $_POST['trierQcmDateDeb'], PDO::PARAM_STR);
				$query->bindValue(":dateFin", $_POST['trierQcmDateFin'], PDO::PARAM_STR);
			}
			if (!empty($trierNoteMin)) {
				$query->bindValue(":noteMin", $noteMin, PDO::PARAM_INT);
			}
			if (!empty($trierNoteMax)) {
				$query->bindValue(":noteMax", $noteMax, PDO::PARAM_INT);
			}
			if (!empty($triParNom)) {
				$query->bindValue(":idQcm", $_POST['trierParNom'], PDO::PARAM_INT);
			}
		
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return array(); // si l'élève en question n'a aucun QCM
		
			$sql = "SELECT idQcm, idTentative, nomQcm, note, pourcentageReussite, dateTentative FROM userTenteQcm INNER JOIN qcm USING (idQcm) WHERE idUser = $idEleve $triParDate $trierNoteMin $trierNoteMax $triParNom AND termine=1 $triParNote";
			
			$query = parent::$bdd->prepare($sql);
			if (!empty($triParDate)) {
				$query->bindValue(":dateDeb", $_POST['trierQcmDateDeb'], PDO::PARAM_STR);
				$query->bindValue(":dateFin", $_POST['trierQcmDateFin'], PDO::PARAM_STR);
			}
			if (!empty($trierNoteMin)) {
				$query->bindValue(":noteMin", $noteMin, PDO::PARAM_INT);
			}
			if (!empty($trierNoteMax)) {
				$query->bindValue(":noteMax", $noteMax, PDO::PARAM_INT);
			}
			if (!empty($triParNom)) {
				$query->bindValue(":idQcm", $_POST['trierParNom'], PDO::PARAM_INT);
			}
			
			
			
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getBilanParTentative() {
		
			if (isset($_GET['idTentative'])) {
		
				require_once "GetBilanQcm.php";
				
				$getBilanQcm = new GetBilanQcm();
				
				return $getBilanQcm->getBilanParTentative();
			}
			else
				die();
		}
		
		public function getCompetences() {
		
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
			$trierParProg = "";
			$trierParMoniteur = "";
			$trierParMaj = "";
			
			// faut-il trier ?
			if (isset($_POST['trierPar'])&&is_array($_POST['trierPar'])) {
					if (in_array("trierParProg", $_POST['trierPar'])) {
						// trier par niveau de progression
						$trierParProg = " AND eval = :eval ";
					}
					
					if (in_array("trierParMoniteur", $_POST['trierPar'])) {
						// trier par moniteur
						$trierParMoniteur = " AND users.login = :loginMoniteur ";
					}
					
					if (in_array("trierParMaj", $_POST['trierPar'])) {
						// trier par date de mise à jour
						switch ($_POST['trierParMaj']) {
							case "asc":
							case "desc":
								break;
							default:
								die();
						}
						$trierParMaj = " ORDER BY lastUpdateDate " . $_POST['trierParMaj'];
					}
			}
			
			$sql = "SELECT idComp, titreCompetence, eval, remarques, prenom, login, lastUpdateDate FROM avoirCompetences INNER JOIN competences USING (idComp) LEFT OUTER JOIN users ON (avoirCompetences.idMoniteurLastUpdate = users.idUser) LEFT OUTER JOIN correspondre ON (users.idUser = correspondre.idUser) LEFT OUTER JOIN infosPerso USING (idInfo) WHERE avoirCompetences.idUser = $idUser $trierParProg $trierParMoniteur $trierParMaj";
			
			$query = parent::$bdd->prepare($sql);
			
			if(!empty($trierParProg)) {
				$query->bindValue(":eval", $_POST['trierParProg'], PDO::PARAM_STR);
			}
			
			if (!empty($trierParMoniteur)) {
				$query->bindValue(":loginMoniteur", $_POST['trierParMoniteur'], PDO::PARAM_STR);
			}
			
									
			$query->execute();
			return $query->fetchAll();
			
		
		}
		
		public function getIdsNomsQcm() {
			$sql = "SELECT * FROM qcm";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$res = $query->fetchAll();
			
			$tabFinal = array();
			
			foreach ($res as $tuple) {
				$tabFinal[$tuple['idQcm']] = $tuple['nomQcm'];
			}
			
			return $tabFinal;
		}
		
		public function getReservations() {
		
			$tabRetour = array();
			$lundi = "";
			$lundiJPlus6 = "";
			$idUser;
			$trierPar="eleve";
			$login=$_SESSION['login']['login'];
			
			// on doit vérifier si le $_POST est bien configuré: il nous faut l'élève/le moniteur, une date correspondant à un lundi
			
			if (isset($_POST['trierPar'])) {
			
				if ($_POST['trierPar']!="eleve"&&$_POST['trierPar']!="moniteur")
					return array(0, "Erreur trierPar");
			
			
				if ($_POST['trierPar']=="eleve") {
					if (!isset($_POST['loginEleve'])) {
						return array(0, "Erreur loginEleve");
					}
					else {
						$trierPar="eleve";
						$login=$_POST['loginEleve'];
					}
				}
				
				if ($_POST['trierPar']=="moniteur") {
					if (!isset($_POST['loginMoniteur'])) {
						return array(0, "Erreur loginMoniteur");
					}
					else {
						$trierPar="moniteur";
						$login=$_POST['loginMoniteur'];
					}
				}
			}
				
			
			
			
			$currentDate = date("Y-m-d");
				
			$sem = (isset($_POST['sem'])) ? $_POST['sem'] : $currentDate;
			
			try {
				$date = new DateTime($sem);
				
				if ($date->format('w')!="1")
					$date = new DateTime(Utilitaires::getDateLundi(""));
				
				$lundi = $date->format("Y-m-d");
				$date->add(new DateInterval('P6D'));
				$lundiJPlus6 = $date->format("Y-m-d");
			}
			catch (Exception $e) {
				return array(0, "Erreur date");
			}

			
			
			$idUser=Utilitaires::getIdUser($login);
			
			if (!$idUser)
				return array(0, "Cet utilisateur n'existe pas");
				
			$sqlIdMoniteur="";
			$sqlIdEleve="";
			
			
			if ($trierPar=="moniteur") {
				$idMoniteur=Utilitaires::getIdMoniteur($idUser);
				
				if (!$idMoniteur)
					return array(0, "Ce moniteur n'existe pas dans notre base de données, ou son compte n'a pas encore été activé.");
					
				$sqlIdMoniteur="idMoniteur = $idMoniteur";
			}
			else if ($trierPar=="eleve") {
				$idEleve=Utilitaires::getIdEleve($idUser);
				
				if (!$idEleve)
					return array(0, "Cet élève n'existe pas dans notre base de données, ou son compte n'a pas encore été activé.");
				
				$sqlIdEleve="idEleve = $idEleve";
			
			}
			
			if (isset($_POST['trierPar'])&&$trierPar=="eleve") {
				if ($login!=$_SESSION['login']['login']) {
					// si l'élève en question ne veut pas que l'on voit son emploi du temps, on ne l'affiche pas
					
					if (!Utilitaires::edtVisibleParEleves($login))
						return array(0, "Cet élève ne souhaite pas que les autres élèves puissent voir son emploi du temps.");
				}
			}
			
			
			// remplissage du tableau de séances
			
			$tabSeances = array();
			
			$ind=0;
			$heureDeb=8;
			
			$date->sub(new DateInterval('P6D')); // on remet date au lundi de la semaine choisie
			$dateS=$date->format("Y-m-d");
			
			// le tableau contient 77 cases, comme celui qui est fait en HTML pour afficher l'emploi du temps pour une semaine. Il y a 7 jours, et 11 créneaux horaires
			// à chaque fois. la case i contient 0 si il n'y a pas de séance, et un tuple sous forme d'objet si elle en contient
			
			$compteurSeances=0;
			
			while ($ind<77) {
				
				$tabSeances[$ind]=0;
				
				$sqlSeances = "SELECT idSeance, idMoniteur, idEleve, dateSeance, heureDeb, heureFin, login, prenom, confirmer FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estMoniteur USING (idMoniteur) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE dateSeance = '$dateS' AND heureDeb = $heureDeb AND $sqlIdMoniteur $sqlIdEleve";
			
				$query=parent::$bdd->prepare($sqlSeances);
				$query->execute();
				$res=$query->fetch(PDO::FETCH_OBJ);
				
				if ($res) {
					$compteurSeances++;
					$tabSeances[$ind]=$res;
				}
			
				$ind++;
				
				if ($dateS==$lundiJPlus6) {
					$date->sub(new DateInterval('P6D'));
					$dateS=$date->format("Y-m-d");
					$heureDeb++;
				}
				else {
					$date->add(new DateInterval("P1D"));
					$dateS=$date->format("Y-m-d");
				}
				
			}
			
			if ($compteurSeances==0)
				return array(-1);
			
			return $tabSeances;
			
		}
		
		public function getAllReservations() {
			$idEleve=Utilitaires::getIdEleve(Utilitaires::getIdUser($_SESSION['login']['login']));
			
			$resultats=array();
			
			// demandes
			$sql="SELECT dateSeance, heureDeb, heureFin, login, prenom FROM reserver INNER JOIN seance USING (idSeance) INNER JOIN estMoniteur USING (idMoniteur) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE idEleve = $idEleve AND dateSeance > CURRENT_DATE AND confirmer=0 ORDER BY dateSeance ASC, heureDeb ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$resultats[0]=$query->fetchAll();
			
			// séances confirmées
			$sql="SELECT dateSeance, heureDeb, heureFin, login, prenom FROM reserver INNER JOIN seance USING (idSeance) INNER JOIN estMoniteur USING (idMoniteur) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE idEleve = $idEleve AND dateSeance > CURRENT_DATE AND confirmer=1 ORDER BY dateSeance ASC, heureDeb ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$resultats[1]=$query->fetchAll();
			
			return $resultats;
		}	
		
		public function gererSeance() {
			$msgError="Erreur. Vous avez peut-être mal saisi quelque chose.";
			
			if (isset($_POST['reserver'])||isset($_POST['annuler'])) {
				if (Tokens::checkCSRF()) {
					return "Formulaire expiré.";
				}
				
				if (isset($_POST['annuler'])&&!isset($_POST['idSeance']))
					return $msgError;
				
				$idEleve = Utilitaires::getIdEleve(Utilitaires::getIdUser($_SESSION['login']['login']));
				
				if (isset($_POST['annuler'])) {
					// on vérifie que la séance existe et qu'elle est associée au moniteur connecté
					
					$sql = "SELECT idSeance FROM reserver WHERE idEleve = $idEleve AND idSeance = :idSeance";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					$objRes = $query->fetch(PDO::FETCH_OBJ);
					
					if (!$objRes)
						return $msgError;
				}
					
				if (isset($_POST['reserver'])) {
				
					// vérification des limites. l'élève actuel a-t-il atteint sa limite de réservations? si oui, on ne le laisse pas continuer
					
					$msgLimiteDepasse = "Vous avez atteint votre limite de réservations, veuillez réessayer plus tard.";
					
					if (Limites::aDepasseLimite($_SESSION['login']['login'], "limite reservations"))
						return $msgLimiteDepasse;
				
					if (intval($_POST['numeroCase'])==0)
						return $msgError;
					
					$numeroReserver = $_POST['numeroCase'];
					
					if (!isset($_POST['heureDeb'])||!isset($_POST['jour'])||!isset($_POST['loginMoniteur'])) {
						return $msgError;
					}
					
					$heureDeb = intval($_POST['heureDeb'][$numeroReserver]);
					
					if ($heureDeb<8||$heureDeb>18)
						return $msgError;
					
					$heureFin = $heureDeb+1;
					$dateSeance = "";
					
					try {
						$dateSeance = new DateTime($_POST['jour'][$numeroReserver]);
						
						// vérifier que la date n'est pas antérieure à la date actuelle
					
						$dateActu = new DateTime(date("r"));
						
						$diff = $dateActu->diff($dateSeance);
						
						$signe = $diff->format("%R%");
						
						if ($signe != "+")
							throw new Exception();
					}
					catch (Exception $e) {
						return $msgError;
					}
					
					$idMoniteur = Utilitaires::getIdMoniteur(Utilitaires::getIdUser($_POST['loginMoniteur']));
					
					if (!$idMoniteur)
						return $msgError;
					
					$idEleve = Utilitaires::getIdEleve(Utilitaires::getIdUser($_SESSION['login']['login']));
					

					
					$dateSeance = $dateSeance->format("Y-m-j");
					
					// vérifier si une séance a la même date et a la même heure existe déjà pour le moniteur en question, ou pour l'élève qui veut réserver
					$sql = "SELECT * FROM seance INNER JOIN reserver USING (idSeance) WHERE heureDeb = $heureDeb AND heureFin = $heureFin AND dateSeance = '$dateSeance' AND (idEleve = $idEleve OR idMoniteur = $idMoniteur)";
					
					$query = parent::$bdd->prepare($sql);
					$query->execute();
					$res = $query->fetchAll();
					
					if (count($res)>0)
						return "Ce moniteur et/ou vous avez déjà programmé une séance à ce moment.";
					
					$sql = "INSERT INTO seance (dateSeance, heureDeb, heureFin) VALUES ('$dateSeance', $heureDeb, $heureFin)";
					$query=parent::$bdd->prepare($sql);
					
					$query->execute();
					$idSeance = parent::$bdd->lastInsertId();
					
					$sql = "INSERT INTO reserver (idSeance, idEleve, idMoniteur, confirmer) VALUES ($idSeance, $idEleve, $idMoniteur, 0)";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					
					
					
					
				}
				elseif (isset($_POST['annuler'])) {
				
					// annulation d'une séance réservée ou pré-réservée
					
					$query = parent::$bdd->prepare("SELECT idUser, confirmer, dateSeance, heureDeb, heureFin FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estMoniteur USING (idMoniteur) WHERE idSeance = :idSeance");
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					
					$detailsSeance = $query->fetch(PDO::FETCH_OBJ);
				
					$sql = "DELETE FROM reserver WHERE idSeance = :idSeance; DELETE FROM seance WHERE idSeance = :idSeance";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					
					$loginEleve=$_SESSION['login']['login'];
					
					$titreMsg="Annulation de séance";
					
					$contenu = "L'élève $loginEleve a annulé la séance prévue le " . Utilitaires::remplacerDateSansHeure($detailsSeance->dateSeance) . " à " . $detailsSeance->heureDeb . "h. Ceci est un message automatique, merci de ne pas répondre.";
					
					if ($detailsSeance->confirmer==1) {
						
						$sql = "INSERT INTO message (contenu, titreMsg) VALUES (:contenu, '$titreMsg')";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":contenu", $contenu, PDO::PARAM_STR);
						$query->execute();
						$msgId = parent::$bdd->lastInsertId();
						
						if (!$msgId)
							return $msgError;
						
						$idRobot=Utilitaires::getIdRobot();
						
						$idDest = $detailsSeance->idUser;
						$sql = "INSERT INTO recevoir VALUES ($msgId, $idDest, $idRobot, 0)";
						$query = parent::$bdd->prepare($sql);
						$query->execute();
					}
				}			
			}
			else
				return;
		}
		
	
	
	}

?>
