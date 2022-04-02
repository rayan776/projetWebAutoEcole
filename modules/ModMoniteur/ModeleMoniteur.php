<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	require_once "qcmDestroyer.php";
	
	class ModeleMoniteur extends ConnectDB {
	
		public $qcmDestroyer;
	
		public function __construct() {
			$this->qcmDestroyer=new QcmDestroyer();
			parent::connect();
		}
		
		public function getListeEleves() {
		
			$sql = "SELECT login, nom, prenom FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) INNER JOIN estEleve USING (idUser) WHERE idUser NOT IN (SELECT idUser FROM estBanni INNER JOIN bannir USING (idBan) WHERE dateFin > CURRENT_TIMESTAMP)";
		
		
			if (isset($_POST['chercherEleveParLogin'])||isset($_POST['chercherEleveParNom'])||isset($_POST['chercherEleveParPrenom'])) {
							
				if (isset($_POST['chercherEleveParLogin']))
					$sql .= "AND login LIKE :login ";
				
				if (isset($_POST['chercherEleveParNom']))
					$sql .= "AND nom LIKE :nom ";
				
				if (isset($_POST['chercherEleveParPrenom']))
					$sql .= "AND prenom LIKE :prenom ";
				
			}
		
			
			$query = parent::$bdd->prepare($sql);
			
			if (isset($_POST['chercherEleveParLogin']))
				$query->bindValue(":login", "%" . $_POST['loginEleve'] . "%", PDO::PARAM_STR);
			
			if (isset($_POST['chercherEleveParNom']))
				$query->bindValue(":nom", "%" . $_POST['nomEleve'] . "%", PDO::PARAM_STR);
				
			if (isset($_POST['chercherEleveParPrenom']))
				$query->bindValue(":prenom", "%" . $_POST['prenomEleve'] . "%", PDO::PARAM_STR);
				
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getCompEleve() {
			if (!isset($_GET['loginEleve']))
				return;
			
			$idUser = Utilitaires::getIdUser($_GET['loginEleve']);
			
			if (!$idUser)
				return array(-1);
			
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
		
		public function updateCompEleve() {
	
			
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['loginEleve'])||!isset($_POST['remarques'])||!is_array($_POST['remarques'])||!isset($_POST['eval'])||!is_array($_POST['eval']))
				return "Erreur";
			
			if (!isset($_POST['ids'])||!is_array($_POST['ids']))
				return "Vous n'avez rien coché.";
			else {
				if(count($_POST['ids'])==0)
					return "Vous n'avez rien coché.";
			}
				
			foreach ($_POST['eval'] as $idComp => $eval) {
				switch ($eval) {
					case "Non acquis":
					case "En cours d'acquisition":
					case "Acquis":
					case "Non evalue":
						break;
					default:
						return "Erreur";
				}
			}
				
			$idEleve = Utilitaires::getIdUser($_POST['loginEleve']);
			$idMon = Utilitaires::getIdUser($_SESSION['login']['login']);
			
			if (!$idEleve)
				return "Erreur";
				
			$remarques = array();
			foreach ($_POST['remarques'] as $idComp => $rem) {
				$remarques[$idComp] = $rem;
			}
		
		
			foreach ($_POST['eval'] as $idComp => $eval) {
				if (in_array($idComp, $_POST['ids'])) {
					$sql = "UPDATE avoirCompetences SET eval = :eval, remarques = :remarques, idMoniteurLastUpdate = $idMon WHERE idComp = :idComp AND idUser = $idEleve";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":eval", $eval, PDO::PARAM_STR);
					$query->bindValue(":remarques", $remarques[$idComp], PDO::PARAM_STR);
					$query->bindValue(":idComp", $idComp, PDO::PARAM_INT);
					$query->execute();
				}
			}
			
			return "Mise à jour effectuée avec succès.";
			
		}
		
		public function getQcmEleve() {
		
			if (!isset($_GET['loginEleve']))
				return array(-1);
		
			$idEleve = Utilitaires::getIdUser($_GET['loginEleve']);
			
			if (!$idEleve)
				return array(-1);
				
			$valRetour = array();
			
			$triParNote = "";
			$triParDate = "";
			$trierNoteMin = "";
			$noteMin = "";
			$trierNoteMax = "";
			$noteMax = "";
			
			// gestion du tri
			if (isset($_POST['trierQcm'])&&is_array($_POST['trierQcm'])) {
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
			
			$sql = "SELECT idTentative FROM userTenteQcm INNER JOIN qcm USING (idQcm) WHERE idUser = $idEleve $triParDate $trierNoteMin $trierNoteMax AND termine=1 $triParNote";
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
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return array(); // si l'élève en question n'a aucun QCM
		
			$sql = "SELECT idQcm, idTentative, nomQcm, note, pourcentageReussite, dateTentative FROM userTenteQcm INNER JOIN qcm USING (idQcm) WHERE idUser = $idEleve $triParDate $trierNoteMin $trierNoteMax AND termine=1 $triParNote ";
			
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
			$query->execute();
			return $query->fetchAll();
		
			
			
		}
		
		public function getResultatsQcm() {
			require_once "modules/ModEleve/FonctionsQcm.php";
			require_once "modules/ModEleve/GetBilanQcm.php";
			
			$getBilanQcm = new GetBilanQcm();
			
			return $getBilanQcm->getBilanParTentative();	
		}
		
		public function destroyQcm() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
		
			if (isset($_POST['ids'])&&is_array($_POST['ids'])) {
			
				foreach ($_POST['ids'] as $idQcm) {
					$this->qcmDestroyer->destroyQcm($idQcm);
				}
				
				return "QCM détruit(s) avec succès.";
			}
			else
				return "Vous n'avez rien coché.";
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
		
		public function getAllQcm() {
			$sql = "SELECT * FROM qcm";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getQuestionsReponsesQcm() {
			
			$idQcm=$_GET['idQcm'];
		
			$sql="SELECT idQuestion, nomQuestion, idReponse, nomReponse, correct, illustration FROM qcmPossedeQuestions INNER JOIN questionPossedeReponses USING (idQuestion) INNER JOIN reponses USING (idReponse) INNER JOIN questions USING (idQuestion) INNER JOIN illustrations USING (idIllustration) WHERE idQcm = :idQcm ORDER BY numeroQuestion ASC, idReponse ASC";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
			$query->execute();
			$qcm=$query->fetchAll();
			
			$sql="SELECT nomQcm FROM qcm WHERE idQcm = :idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $_GET['idQcm'], PDO::PARAM_INT);
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
				
			$nomQcm=$objRes->nomQcm;
			
			$resultats=array($nomQcm, $qcm);
			
			return $resultats;
		}
		
		public function updateQcm() {
			
		
			$tabAutoriser=1;
			
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['ids']))
				return "Vous n'avez rien coché.";
			
			if (!isset($_POST['nomQcm'])||!is_array($_POST['nomQcm']))
				die();
			
			if (!isset($_POST['autoriserQcm'])||!is_array($_POST['autoriserQcm']))
				$tabAutoriser=0;
				
			$messages = array();
			
			$msgLengthQcm = "RAPPEL: Un nom de QCM ne doit pas dépasser 50 caractères, et doit être unique.";
			
			foreach ($_POST['nomQcm'] as $idQcm => $nomQcm) {
				$autoriser=0;
				if (in_array($idQcm, $_POST['ids'])) {
				
					if ($tabAutoriser==1)
						$autoriser = array_key_exists($idQcm, $_POST['autoriserQcm']) ? 1 : 0;
				
					$sql = "UPDATE qcm SET nomQcm = :nomQcm, autoriser = :autoriser WHERE idQcm = :idQcm";
					$query = parent::$bdd->prepare($sql);
					$query->bindValue(":nomQcm", $nomQcm, PDO::PARAM_STR);
					$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
					$query->bindValue(":autoriser", $autoriser, PDO::PARAM_INT);
					
					if (!$query->execute()) {
					
						if (!in_array($msgLengthQcm, $messages))
							$messages[]=$msgLengthQcm;
						
						$messages[] = "ERREUR: Le nom " . htmlspecialchars($nomQcm, ENT_QUOTES) . " n'a pas pu être attribué.";
					}
				}
			}
			
			return $messages;
					
		}
		
		private function deleteQcm($idQcm, $idsQuestions, $idsReponses) {
			// supprime de la BD toutes les lignes correspondant à un ID de QCM, des ID de questions et des ID de réponses.
			// fonction utilisée uniquement par la fonction addQcm() en cas d'échec de création d'un qcm dans la BD
			
			// suppression du qcm
			$sql="DELETE FROM qcmPossedeQuestions WHERE idQcm = $idQcm; DELETE FROM qcm WHERE idQcm=$idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			// suppression des questions
			foreach($idsQuestions as $id) {
				$sql="DELETE FROM questionPossedeReponses WHERE idQuestion=$id; DELETE FROM questions WHERE idQuestion=$id";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			// suppression des réponses
			foreach ($idsReponses as $id) {
				$sql="DELETE FROM reponses WHERE idReponse=$id";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
		}
		
		public function getIllustrations() {
			$sql="SELECT * FROM illustrations";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function addQcm() {
		
			if (Tokens::checkCSRF())
				return array("Formulaire expiré.");
		
			// fonction de création de nouveau QCM
			
			// il faut commencer par vérifier si toutes les données nécessaires ont été transmises par $_POST, et les valider
			
			/*
				Voila à quoi doit ressembler le ableau $_POST pour que cette fonction marche correctement
				
				" ["etapes"]=> array(3) { [0]=> string(1) "1" [1]=> string(1) "2" [2]=> string(1) "3" } 
				["nomQcm"]=> string(4) "code" 
				["nbQuestionsQcm"]=> string(1) "2" 
				["questions"]=> array(2) { ["q1"]=> string(5) "test1" ["q2"]=> string(5) "test2" } 
				["limiteTemps"]=> array(2) { ["q1"]=> string(2) "30" ["q2"]=> string(2) "30" }
				["illustrations"]=>array(2) { ["q1"]=> string(17) "resources/qcmImgDefault.jpg" ["q2"]=> string(17) "resources/qcmImgDefault.jpg" }
				["nbReponses"]=> array(2) { ["q1"]=> string(1) "2" ["q2"]=> string(1) "2" } 
				["reponsesQ1"]=> array(2) { ["r1"]=> string(3) "oui" ["r2"]=> string(3) "non" } 
				["correctQ1"]=> array(1) { [0]=> string(2) "r1" } 
				["reponsesQ2"]=> array(2) { ["r1"]=> string(2) "40" ["r2"]=> string(2) "50" } 
				["correctQ2"]=> array(1) { [0]=> string(2) "r1" } } 
				
			*/
			

			
			$messageRetour = array();
			
			// vérification du tableau étapes
			if (isset($_POST['etapes'])&&is_array($_POST['etapes'])) {
				for ($i=1; $i<=3; $i++) {
					if (!in_array("$i", $_POST['etapes']))
						die();
				}
			}
			else
				die("etapes");
				
				
			// nomQcm ne doit pas être vide et doit être inférieur ou égal à 50 caractères.
			if (!isset($_POST['nomQcm']))
				die("nomQcm");
			else {
				$_POST['nomQcm']=trim($_POST['nomQcm']);
				if(strlen($_POST['nomQcm'])==0||strlen($_POST['nomQcm'])>50)
					$messageRetour[]="ERREUR: Le QCM doit avoir un nom, qui ne doit pas dépasser 50 caractères.";
			}
			
			// le nombre de questions du QCM doit être compris entre 1 et 40
			if (!isset($_POST['nbQuestionsQcm']))
				die("nbQuestionsQcm");
			else {
				$nbQuestionsQcm = intval($_POST['nbQuestionsQcm']);
				if ($nbQuestionsQcm>40||$nbQuestionsQcm<1)
					die("nbQuestionsQcm");
			}
			
			// vérification du tableau questions: il doit exister, et contenir au moins une valeur
			if (!isset($_POST['questions'])||!is_array($_POST['questions'])||count($_POST['questions'])<1)
				die("questions");
			else {
				$j=1;
				// les noms de question ne doivent pas être vides, ni dépasser 100 caractères
				foreach ($_POST['questions'] as $key => $val) {
					$question = trim($val);
					if (strlen($question)<1||strlen($question)>100) {
						$messageRetour[]="ERREUR: la question $j doit être comprise entre 1 et 100 caractères.";
					}
					$j++;
				}
			}
			
			// vérification du tableau limiteTemps: il doit exister, et contenir le même nombre de valeurs qu'il y a de questions.
			// par défaut, si une valeur est invalide (inférieure à 10 ou supérieure à 60), alors on a 30 secondes pour répondre.
			
			if (!isset($_POST['limiteTemps'])||!is_array($_POST['limiteTemps'])||count($_POST['limiteTemps'])!=count($_POST['questions']))
				die();
			else {
				$j=1;
				$limiteTemps=array();
				foreach ($_POST['limiteTemps'] as $qst => $tps) {
					$temps=intval($tps);
					if ($tps<10||$tps>60) {
						$messageRetour[]="ERREUR: le temps pour répondre à la question $j doit être compris entre 10 et 60 secondes.";
					}
					else {
						$limiteTemps[$qst]=$temps;
					}
					
					$j++;
				}
			}
			
			// vérification du tableau illustrations: il doit exister, et contenir autant de valeurs qu'il y a de questions.
			// il faut vérifier si les fichiers existent.
			
			if (!isset($_POST['illustrations'])||!is_array($_POST['illustrations'])||count($_POST['illustrations'])!=count($_POST['questions']))
				die();
			else {
				$j=1;
				$illustrations=array();
				foreach ($_POST['illustrations'] as $qst => $src) {
					if (!file_exists($src)) {
						$messageRetour[]="ERREUR: illustration de la question $j, fichier inexistant";
					}
					else {
						$illustrations[$qst]=$src;
					}
					$j++;
				}
			}
			
			// vérification du tableau nbReponses: chaque question doit avoir au moins 2 réponses et au plus 4 réponses.
			
			if (!isset($_POST['nbReponses'])||!is_array($_POST['nbReponses']))
				die("nbReponses");
			else {
				foreach ($_POST['nbReponses'] as $qst => $nbRep) {
						if (intval($nbRep)<2||intval($nbRep)>4)
							die("nbReponses");
				}
			}
			
			// pour chaque question de numéro n, il faut désormais vérifier les tableaux "reponsesQn" et les tableaux "correctQn".
			// normalement, l'ordre est respecté, pas de souci à se faire au niveau des numéros. si le nombre de questions est réglé à 3,
			// on doit avoir reponsesQ1, reponsesQ2, reponsesQ3, etc. donc si on ne trouve pas un tableau, on appelle die.
			// chaque tableau reponsesQn doit avoir 2-4 éléments, et correctQn au moins 1-4 éléments.
			
			for ($i=1; $i<=$nbQuestionsQcm; $i++) {
				if (!isset($_POST['reponsesQ' . $i])||!is_array($_POST['reponsesQ' . $i])||count($_POST['reponsesQ' . $i])<2||count($_POST['reponsesQ' . $i])>4)
					die("reponsesQ$i");
				else {
					foreach ($_POST['reponsesQ' . $i] as $rep => $val) {
						if (strlen($val)<1||strlen($val)>100) {
							$messageRetour[]="ERREUR: la réponse \"$val\" de la question $i doit être comprise entre 1 et 100 caractères.";
						}
					}
				}
				
				if (!isset($_POST['correctQ' . $i])||!is_array($_POST['correctQ' . $i])||count($_POST['correctQ' . $i])<1||count($_POST['correctQ' . $i])>4)
					$messageRetour[]="ERREUR: la question $i doit avoir au moins une réponse correcte.";
				else {
					foreach ($_POST['correctQ' . $i] as $key => $rep) {
						if (!isset($_POST['reponsesQ' . $i][$rep]))
							die("reponsesQ$i et $rep");
					}
				}
			}
			
			// si il n'y a pas d'erreurs, on commence la préparation des requêtes SQL. Si une requête échoue, il faut faire en sorte
			// d'annuler les précédentes, et de retourner un message d'erreur
			
			if (count($messageRetour)>0)
				return $messageRetour;
			
			$deleteAll=0;
			
			// insertion du QCM
			$sql = "INSERT INTO qcm (nomQcm) VALUES (:nomQcm)";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":nomQcm", strtolower($_POST['nomQcm']), PDO::PARAM_STR);
			
			if (!$query->execute())
				return "ERREUR: nom QCM invalide";
				
			$idQcm=parent::$bdd->lastInsertId();
			
			// insertion des questions
			
			$idsQuestions=array();
			$idsReponses=array();
			
			$idsQuestionsInserees=array();
			$idsReponsesInserees=array();
			
			$i=1;
			
			foreach($_POST['questions'] as $key => $val) {
			
				$sql = "INSERT INTO questions (nomQuestion) VALUES (:nomQuestion)";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":nomQuestion", $val, PDO::PARAM_STR);
				
				if (!$query->execute()) {
					$messageRetour[]="ERREUR: nom de la question $i invalide";
					
					$deleteAll=1;
					break;
				}
				else {
					$idsQuestionsInserees[]=parent::$bdd->lastInsertId();
					$idsQuestions[$i]=parent::$bdd->lastInsertId();
				}
				
				$i++;
			}
			
			if ($deleteAll==1) {
				$this->deleteQcm($idQcm, $idsQuestionsInserees, $idsReponsesInserees);
				return $messageRetour;
			}
			
			// insertion des réponses, si elles n'existent pas déjà dans la BD. si elles existent déjà, il faut récupérer leur ID.
		
			
			for ($i=1; $i<=$nbQuestionsQcm; $i++) {
				$j=1;
				foreach($_POST["reponsesQ$i"] as $key => $val) {
				
					
					$sql = "SELECT idReponse, nomReponse FROM reponses WHERE nomReponse = :nomReponse";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":nomReponse", $val, PDO::PARAM_STR);
					$query->execute();
					
					$res=$query->fetchAll();
					
					if (count($res)==0) {
						$sql="INSERT INTO reponses (nomReponse) VALUES (:nomReponse)";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":nomReponse", $val, PDO::PARAM_STR);
						if (!$query->execute()) {
							$messageRetour[]="ERREUR: nom de la réponse \"$val\" à la question $i invalide";
							$deleteAll=1;
							break;
						}
						else {
							$idsReponsesInserees[]=parent::$bdd->lastInsertId();
							$idsReponses["Q$i.R$j"]=parent::$bdd->lastInsertId();
						}
						
					}
					else {
						foreach($res as $tuple) {
							
							$idsReponses["Q$i.R$j"]=$tuple['idReponse'];
						}
					}
					
					$j++;
				}
				
			}
			
			if ($deleteAll==1) {
				$this->deleteQcm($idQcm, $idsQuestionsInserees, $idsReponsesInserees);
				return $messageRetour;
			}
			
			// insertion des lignes dans la table "qcmPossedeQuestions"
			
			$i=1;
			foreach ($idsQuestions as $id) {
				$tps=$limiteTemps["q$i"];
				
				$sql="SELECT idIllustration FROM illustrations WHERE illustration = :illustration";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":illustration", $illustrations["q$i"], PDO::PARAM_STR);
				$query->execute();
				$objRes=$query->fetch(PDO::FETCH_OBJ);
				$idIllustration=$objRes->idIllustration;
				
				$sql="INSERT INTO qcmPossedeQuestions VALUES ($idQcm, $id, $i, $tps, $idIllustration)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				$i++;
			}
			
			// insertion des lignes dans la table "questionPossedeReponses"
			
			for ($i=1; $i<=$nbQuestionsQcm; $i++) {
				$j=1;
				foreach ($_POST["reponsesQ$i"] as $key => $val) {
					$correct=in_array($key, $_POST["correctQ$i"]) ? 1 : 0;
					
					$idQuestion=$idsQuestions[$i];
					$idReponse=$idsReponses["Q$i.R$j"];
					
					$sql="INSERT INTO questionPossedeReponses VALUES ($idQuestion, $idReponse, $correct)";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					
					$j++;
				}
			}
			
			$messageRetour[]="QCM ajouté";
			
			return $messageRetour;
			
			
		}
	
		
		public function getReservations() {
		
			$tabRetour = array();
			$lundi = "";
			$lundiJPlus6 = "";
			$idUser;
			$trierPar="moniteur";
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
			
			
			// remplissage du tableau de séances
			
			$tabSeances = array();
			
			$ind=0;
			$heureDeb=8;
			
			$date->sub(new DateInterval('P6D')); // on remet date au lundi de la semaine choisie
			$dateS=$date->format("Y-m-d");
			
			// le tableau contient 77 cases, comme celui qui est fait en HTML pour afficher l'emploi du temps pour une semaine. Il y a 7 jours, et 11 créneaux horaires
			// à chaque fois. la case i contient 0 si il n'y a pas de séance, et un tuple
			
			$compteurSeances=0;
			
			while ($ind<77) {
			
				$tabSeances[$ind]=0;
				
				$sqlSeances = "SELECT idSeance, idMoniteur, idEleve, dateSeance, heureDeb, heureFin, login, prenom, confirmer FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estEleve USING (idEleve) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE dateSeance = '$dateS' AND heureDeb = $heureDeb AND $sqlIdMoniteur $sqlIdEleve";
			
				$query=parent::$bdd->prepare($sqlSeances);
				$query->execute();
				$res=$query->fetch(PDO::FETCH_OBJ);
				
				if ($res) {
					$tabSeances[$ind]=$res;
					$compteurSeances++;
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
			$idMoniteur=Utilitaires::getIdMoniteur(Utilitaires::getIdUser($_SESSION['login']['login']));
			
			$resultats=array();
			
			// demandes
			$sql="SELECT dateSeance, heureDeb, heureFin, login, prenom FROM reserver INNER JOIN seance USING (idSeance) INNER JOIN estEleve USING (idEleve) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE idMoniteur = $idMoniteur AND dateSeance > CURRENT_DATE AND confirmer=0 ORDER BY dateSeance ASC, heureDeb ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$resultats[0]=$query->fetchAll();
			
			// séances confirmées
			$sql="SELECT dateSeance, heureDeb, heureFin, login, prenom FROM reserver INNER JOIN seance USING (idSeance) INNER JOIN estEleve USING (idEleve) INNER JOIN users USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE idMoniteur = $idMoniteur AND dateSeance > CURRENT_DATE AND confirmer=1 ORDER BY dateSeance ASC, heureDeb ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$resultats[1]=$query->fetchAll();
			
			return $resultats;
		}
		
		public function reporterSeance() {
			
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['idSeance'])||!isset($_POST['dateReport'])||!isset($_POST['heureReport']))
				return "";
				
			$idSeance=intval($_POST['idSeance']);
			
			if (!$idSeance)
				return "";
			
			// le moniteur connecté souhaite reporter une séance.
			
			$idMoniteur=Utilitaires::getIdMoniteur(Utilitaires::getIdUser($_SESSION['login']['login']));
			
			// étape 1: vérifier que la séance existe ET qu'elle est associée à ce moniteur.
			
			$sql="SELECT idEleve, idUser, dateSeance, heureDeb, heureFin FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estEleve USING (idEleve) WHERE idSeance = $idSeance AND idMoniteur = $idMoniteur";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return "";
			
			// étape 2: vérifier la validité de la date et de l'heure à laquelle il souhaite reporter.
			
			// pour rappel: la date doit se trouver > 24 heures par rapport à l'heure de la séance.
		
			$dateReport="";
			
			try {
				$dateReport=new DateTime($_POST['dateReport']);
				
			}
			catch (Exception $e) {
				return "";
			}
			
			$dateActu=new DateTime(date("r"));
			
			$ecart=$dateActu->diff($dateReport);
			$ecart=$ecart->format("%R%a");
			
			if ($ecart[0]=="-")
				return "Date invalide. Vous ne pouvez reporter une séance que sur les créneaux horaires se trouvant 24 heures après l'instant actuel.";
			
			$heureDebReport=intval($_POST['heureReport']);
			
			if ($heureDebReport<8||$heureDebReport>18)
				return "Heure invalide.";
			
			if ($ecart=="+0") {
				// si on se trouve à J+1, alors il faut que l'heure de report soit supérieure à l'ancienne heure
				
				if ($heureDebReport<=$objRes->heureDeb)
					return "Heure invalide. Vous ne pouvez reporter une séance que sur les créneaux horaires se trouvant plus de 24 heures après celle-ci.";
				
			}
			
			// si on arrive ici, c'est que la date et l'heure proposés pour le report sont valides.
			// il reste à vérifier si, à ce moment la, le moniteur actuel ET l'élève concerné sont libres.
			
			$dateReport=$dateReport->format("Y-m-d");
			
			$idEleve=$objRes->idEleve;
			
			$sql="SELECT idSeance FROM seance INNER JOIN reserver USING (idSeance) WHERE idEleve = $idEleve AND dateSeance = '$dateReport' AND heureDeb = $heureDebReport";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$eleveOccupe=$query->fetch(PDO::FETCH_OBJ);
			
			if ($eleveOccupe)
				return "Report impossible: cet élève n'est pas disponible à ce créneau horaire.";
			
			// de même pour le moniteur
			$sql="SELECT idSeance FROM seance INNER JOIN reserver USING (idSeance) WHERE idMoniteur = $idMoniteur AND dateSeance = '$dateReport' AND heureDeb = $heureDebReport";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$moniteurOccupe=$query->fetch(PDO::FETCH_OBJ);
			
			if ($moniteurOccupe)
				return "Report impossible: vous n'êtes pas disponible à ce créneau horaire.";
			
			// si on arrive ici, c'est que toutes les conditions sont réunies pour que le report ait lieu. On lance un update et on sort.
			$heureFin=$heureDebReport+1;
			
			$sql="UPDATE seance SET dateSeance = '$dateReport', heureDeb = $heureDebReport, heureFin = $heureFin WHERE idSeance = $idSeance";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			// envoi d'un message privé automatique à l'élève pour l'informer du report
			
			$titreMsg="Séance reportée";
			$contenuMsg="Le moniteur " . $_SESSION['login']['login'] . " a reporté votre séance prévue au " . Utilitaires::remplacerDateSansHeure($objRes->dateSeance) . " de " . $objRes->heureDeb . "h à " . $objRes->heureFin . "h. Elle est désormais prévue le " . Utilitaires::remplacerDateSansHeure($dateReport) . " de $heureDebReport" . "h à $heureFin" . "h. Ceci est un message automatique, merci de ne pas répondre.";
			
			$idDest=$objRes->idUser;
			
			$sql="INSERT INTO message (titreMsg, contenu) VALUES ('$titreMsg', '$contenuMsg')";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$msgId = parent::$bdd->lastInsertId();
			
			$idRobot=Utilitaires::getIdRobot();
			
			$sql = "INSERT INTO recevoir VALUES ($msgId, $idDest, $idRobot, 0)";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$_POST['sem']=$dateReport;
			
			return "Séance reportée avec succès.";
			
			
		}
		
		public function gererSeance() {
			if (isset($_POST['accepter'])||isset($_POST['refuser'])||isset($_POST['annuler'])) {
				if (Tokens::checkCSRF()) {
					return;
				}
				
				if (!isset($_POST['idSeance']))
					return;
				
				$idMoniteur = Utilitaires::getIdMoniteur(Utilitaires::getIdUser($_SESSION['login']['login']));
				
				// on vérifie que la séance existe et qu'elle est associée au moniteur connecté
				
				$sql = "SELECT idSeance FROM reserver WHERE idMoniteur = $idMoniteur AND idSeance = :idSeance";
				$query = parent::$bdd->prepare($sql);
				$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
				$query->execute();
				$objRes = $query->fetch(PDO::FETCH_OBJ);
				
				if (!$objRes)
					return;
			
				$idRobot=Utilitaires::getIdRobot();
				
				if (isset($_POST['accepter'])) {
					$sql = "UPDATE reserver SET confirmer=1 WHERE idSeance = :idSeance";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					
					$sql = "SELECT idUser, dateSeance, heureDeb FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estEleve USING (idEleve) INNER JOIN users USING (idUser) WHERE idSeance = :idSeance";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);					
					$query->execute();
					$objRes=$query->fetch(PDO::FETCH_OBJ);
					
					$contenu = "Le moniteur " . $_SESSION['login']['login'] . " a bien accepté votre demande de réservation pour le " . Utilitaires::remplacerDateSansHeure($objRes->dateSeance) . " à " . $objRes->heureDeb . "h. Ceci est un message automatique, merci de ne pas répondre.";
					$titreMsg = "Séance acceptée";
					
					$sql = "INSERT INTO message (contenu, titreMsg) VALUES ('$contenu', '$titreMsg')";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					$msgId = parent::$bdd->lastInsertId();
					
					$idDest = $objRes->idUser;
					$sql = "INSERT INTO recevoir VALUES ($msgId, $idDest, $idRobot, 0)";
					$query = parent::$bdd->prepare($sql);
					$query->execute();
				}
				elseif (isset($_POST['annuler'])||isset($_POST['refuser'])) {
				
					// annulation d'une séance déjà réservée ou refus d'une demande de réservation
					
					$query = parent::$bdd->prepare("SELECT idUser, dateSeance, heureDeb, heureFin FROM seance INNER JOIN reserver USING (idSeance) INNER JOIN estEleve USING (idEleve) WHERE idSeance = :idSeance");
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					
					$detailsSeance = $query->fetch(PDO::FETCH_OBJ);

				
					$sql = "DELETE FROM reserver WHERE idSeance = :idSeance; DELETE FROM seance WHERE idSeance = :idSeance";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idSeance", $_POST['idSeance'], PDO::PARAM_INT);
					$query->execute();
					
					$loginMoniteur=$_SESSION['login']['login'];
					
					$verbe = isset($_POST['annuler']) ? "annulé" : "refusé";
					
					$titreMsg = isset($_POST['annuler']) ? "Annulation de séance" : "Refus de séance";
					
					$prevue = ($verbe=="annulé") ? "prévue" : "demandée";
					
					$contenu = "Le moniteur $loginMoniteur a $verbe la séance $prevue le " . Utilitaires::remplacerDateSansHeure($detailsSeance->dateSeance) . " à " . $detailsSeance->heureDeb . "h. Ceci est un message automatique, merci de ne pas répondre.";

					
					$sql = "INSERT INTO message (contenu, titreMsg) VALUES ('$contenu', '$titreMsg')";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					$msgId = parent::$bdd->lastInsertId();
					
					
					$idDest = $detailsSeance->idUser;
					$sql = "INSERT INTO recevoir VALUES ($msgId, $idDest, $idRobot, 0)";
					$query = parent::$bdd->prepare($sql);
					$query->execute();
				}			
			}
			else
				return;
		}
		
		public function getAllComps() {
			$sql="SELECT * FROM competences";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function addNewComp() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
		
			if (isset($_POST['nameComp'])) {
			
				$sql="SELECT FROM competences WHERE titreCompetence = :titre";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":titre", htmlspecialchars($_POST['nameComp'], ENT_QUOTES), PDO::PARAM_STR);
				$query->execute();
				
				$objRes=$query->fetch(PDO::FETCH_OBJ);
				if ($objRes)
					return "Cette compétence existe déjà.";
				
				$_POST['nameComp']=trim($_POST['nameComp']);
				
				if (strlen($_POST['nameComp'])<1||strlen($_POST['nameComp'])>60)
					return "Le titre doit être compris entre 1 et 60 caractères.";
			
				$sql="INSERT INTO competences (titreCompetence) VALUES (:titre)";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":titre", htmlspecialchars($_POST['nameComp'], ENT_QUOTES), PDO::PARAM_STR);
				$query->execute();
				
				// ajout de la compétence pour tous les élèves
				
				$idComp=parent::$bdd->lastInsertId();
				
				$sql="SELECT idUser FROM estEleve";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$idsEleves=$query->fetchAll();
				
				foreach ($idsEleves as $tuple) {
					$idUser=$tuple['idUser'];
					$sql="INSERT INTO avoirCompetences (idUser, idComp) VALUES ($idUser, $idComp)";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
				
				return "Compétence ajoutée.";
			}
			else
				return "Erreur.";
		}
		
		public function updateListComps($action) {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
		
			$nbUpdate=0;
			
			if (!isset($_POST['idComp']))
				return "Vous n'avez rien coché.";
			
			foreach ($_POST['idComp'] as $idComp) {
				if ($action=="delete") {
					// suppression de la compétence pour tous les élèves
					$sql="DELETE FROM avoirCompetences WHERE idComp = $idComp";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
				
				if ($action=="update"&&isset($_POST['titre'][$idComp])) {
					$titre=$_POST['titre'][$idComp];
					$sql="UPDATE competences SET titreCompetence = :titre WHERE idComp = :idComp";
				}
				elseif ($action=="delete") {
					$sql="DELETE FROM competences WHERE idComp = :idComp";
				}
				
				$query=parent::$bdd->prepare($sql);
				
				if ($action=="update")
					$query->bindValue(":titre", htmlspecialchars($titre, ENT_QUOTES), PDO::PARAM_STR);
					
				$query->bindValue(":idComp", $idComp, PDO::PARAM_INT);
				$query->execute();
				$nbUpdate++;
			}
		
			
			return ($action=="update")?"$nbUpdate compétence(s) mise(s) à jour.":"$nbUpdate compétence(s) supprimée(s).";
			
		}
		
		
	
	
	}

?>
