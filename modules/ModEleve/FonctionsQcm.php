<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	
	class FonctionsQcm extends ConnectDB {
	
		public $idUser;
		public $idQcm;
		public $idTentative;
	
		public function __construct($idQcm, $idUser) {
			parent::connect();
			$this->idUser = $idUser;
			$this->idQcm = $idQcm;
		
			if (isset($_POST['idTentative'])) {
				$this->idTentative=intval($_POST['idTentative']);
				if (!$this->verifierIdTentative())
					die();
			}
			elseif (isset($_GET['idTentative'])) {
				$this->idTentative=intval($_GET['idTentative']);
				if (!$this->verifierIdTentative())
					die();
			}
		
		}
		
		public function verifierIdTentative() {
			// pour s'assurer qu'il s'agisse bien d'un vrai ID de tentative, et qu'il est associé à l'élève actuel
			
			$idTentative = $this->idTentative;
			
			if (Utilitaires::getRoleCurrentUser()=="eleve")
				$idEleve = Utilitaires::getIdUser($_SESSION['login']['login']);
			else
				$idEleve=$this->idUser;
						
			$sql = "SELECT idTentative FROM userTenteQcm INNER JOIN users USING (idUser) INNER JOIN estEleve USING (idUser) WHERE idTentative = $idTentative AND idUser = $idEleve";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			if (!$objRes)
				return 0;
		
			return 1;
		}
		
		// nouvelle tentative, quand l'élève commence son QCM
		
		public function insererTentative() {
			$idUser = $this->idUser;
			$idQcm = $this->idQcm;
			$sql = "INSERT INTO userTenteQcm (idUser, idQcm) VALUES ($idUser, $idQcm)";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			// récupérer l'ID de la nouvelle tentative
			return parent::$bdd->lastInsertId();
		}
		
		public function getIdQuestionByNumeroAndIdQcm($idQcm, $numeroQuestion) {
			$sql = "SELECT idQuestion FROM qcm INNER JOIN qcmPossedeQuestions USING (idQcm) WHERE idQcm = $idQcm AND numeroQuestion = $numeroQuestion";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
				
			return $objRes->idQuestion;
		}
		
		// vérifier si le QCM est fini
		
		public function qcmTermine($idTentative) {
			$sql = "SELECT termine FROM userTenteQcm WHERE idTentative=$idTentative";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				die();
			
			return $objRes->termine;
		}
		
		// valide la réponse de l'utilisateur à la dernière question
		
		
		public function verifierReponse() {
		
			if (isset($_POST['answer'])) {
			
				if (Tokens::checkCSRF())
					return -1;
			
				if (!isset($_POST['idQuestion'])||!isset($_POST['numeroQuestion']))
					die();
			
				$idUser = $this->idUser;
				$idQcm = $this->idQcm;
				$idTentative = $this->idTentative;
	
				// si l'élève n'a pas répondu à la question précédente, on die parce qu'il tente de tricher.
				// ceci seulement si la question est la question numéro 2 du QCM, ou plus.
			
				
				$idLastQuestion = intval($_POST['idQuestion']);
				$numeroLastQuestion = intval($_POST['numeroQuestion']);
				
				// si l'élève a déjà répondu à la question, on sort de la fonction
				if ($this->questionDejaRepondue($idLastQuestion))
					return 0;
			
				
				if (intval($_POST['numeroQuestion'])>1) {
				
					$idQuestionDavant = $this->getIdQuestionByNumeroAndIdQcm($idQcm, $numeroLastQuestion-1);
					
					$verifierDernierQuestion = "SELECT timeSubmit FROM participerQuestion WHERE idUser = $idUser AND idQuestion = :idLastQuestion AND idTentative = $idTentative AND timeSubmit IS NOT NULL";
					$query = parent::$bdd->prepare($verifierDernierQuestion);
					$query->bindValue(":idLastQuestion", $idQuestionDavant, PDO::PARAM_INT);
					$query->execute();
					$objRes = $query->fetch(PDO::FETCH_OBJ);
					
					if (!$objRes)
						die("Tricheur");
				}
				
				// pas de triche, on vérifie son heure de réponse.
			
				// mettre l'heure où il répond
				$sql = "UPDATE participerQuestion SET timeSubmit = CURRENT_TIMESTAMP WHERE idUser = $idUser AND idQuestion = :idLastQuestion AND idTentative = $idTentative";
				$query = parent::$bdd->prepare($sql);
				$query->bindValue(":idLastQuestion", $idLastQuestion, PDO::PARAM_INT);
				$query->execute();
				
				// si cette heure est supérieure à la limite, on compte faux
				$sql = "SELECT count(*) AS count FROM participerQuestion WHERE idUser = $idUser AND idQuestion = :idLastQuestion AND timeSubmit < timeLimit AND idTentative = $idTentative";
				$query = parent::$bdd->prepare($sql);
				$query->bindValue(":idLastQuestion", $idLastQuestion, PDO::PARAM_INT);
				$query->execute();
				
				$objRes = $query->fetch(PDO::FETCH_OBJ);
				$avantLimite=1;
				
				if (!$objRes)
					$avantLimite=0;
				else {
					if ($objRes->count<1)
						$avantLimite=0;
				}
				
				if ($avantLimite) {
					// il a répondu avant la fin... donc on regarde si il a bon ou pas
					if (!isset($_POST['reponses'])||!is_array($_POST['reponses'])) {
						// aucune réponse cochée donc faux
					}
					else {

						$sql = "SELECT idParticipation FROM participerQuestion WHERE idQuestion = :idLastQuestion AND idTentative = $idTentative";
						$query = parent::$bdd->prepare($sql);
						$query->bindValue(":idLastQuestion", $idLastQuestion, PDO::PARAM_INT);	
						$query->execute();
						$objRes = $query->fetch(PDO::FETCH_OBJ);
						$idParticipation = $objRes->idParticipation;
						
						// insérer les réponses choisies dans la BD
						
						foreach ($_POST['reponses'] as $idRep) {
							$sql = "INSERT INTO participerAvecReponses VALUES ($idParticipation, $idRep)";
							$query = parent::$bdd->prepare($sql);
							$query->execute();
							
						}
					
						// vérifier la validité des réponses cochées
						
						$lesMauvaisesReponses = "SELECT reponses.idReponse as idReponse FROM reponses INNER JOIN questionPossedeReponses USING (idReponse) INNER JOIN questions USING (idQuestion) WHERE idQuestion = :idLastQuestion AND questionPossedeReponses.correct = 0";
						$query = parent::$bdd->prepare($lesMauvaisesReponses);
						$query->bindValue(":idLastQuestion", $idLastQuestion, PDO::PARAM_INT);				
						$query->execute();
						
						$listeIdsMauvaisesReponses = $query->fetchAll();
						
						$lesBonnesReponses = "SELECT reponses.idReponse as idReponse FROM reponses INNER JOIN questionPossedeReponses USING (idReponse) INNER JOIN questions USING (idQuestion) WHERE idQuestion = :idLastQuestion AND questionPossedeReponses.correct = 1";
						$query = parent::$bdd->prepare($lesBonnesReponses);
						$query->bindValue(":idLastQuestion", $idLastQuestion, PDO::PARAM_INT);				
						$query->execute();
						
						$listeIdsBonnesReponses = $query->fetchAll();
						
						$toutBon = 1;
						
						foreach ($listeIdsMauvaisesReponses as $tuple) {
							
							if (in_array(strval($tuple['idReponse']), $_POST['reponses'])) {
								$toutBon = 0;
								break;
							}
						}
						
						foreach ($listeIdsBonnesReponses as $tuple) {
							
							if (!in_array(strval($tuple['idReponse']), $_POST['reponses'])) {
								$toutBon = 0;
								break;
							}
						}
						
						if ($toutBon) {
							// question validée: on augmente la note de 1 point
							
							$sql = "UPDATE userTenteQcm SET note = note + 1 WHERE idTentative = $idTentative";
							$query = parent::$bdd->prepare($sql);
							$query->execute();
						}
					}
				}
				
		
			
			}
		
		}
		
		public function getNextQuestion() {
			$idTentative = $this->idTentative;
			$idQcm = $this->idQcm;
			$idUser = $this->idUser;
			
			// préparation de la prochaine question
			
			$numeroQuestion = (isset($_POST['numeroQuestion'])) ? intval($_POST['numeroQuestion']) : 0;
			
			// pour la sécurité, il faut faire en sorte que l'exécution s'arrête si:
			// - le numéro de question dans $_POST est invalide (c'est à dire qu'il est strictement supérieur au numéro de question le plus grand pour le QCM correspondant)
			// - la question précédente n'a pas été répondue (sauf pour la question 1)
		
			$verif1 = "SELECT max(numeroQuestion) AS maxNumeroQuestion FROM qcmPossedeQuestions WHERE idQcm = $idQcm";
			$query = parent::$bdd->prepare($verif1);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes->maxNumeroQuestion < $numeroQuestion)
				die("Triche détéctée: numeroQuestion trop grand");
			
			if (!$objRes)
				die("Triche détectée: idQcm obsolète");
			
			if ($numeroQuestion>=1) {
				$verif2 = "SELECT timeSubmit FROM participerQuestion INNER JOIN qcmPossedeQuestions USING (idQuestion) WHERE idTentative = $idTentative AND numeroQuestion = $numeroQuestion AND idUser = $idUser AND timeSubmit IS NOT NULL";
				$query = parent::$bdd->prepare($verif2);
				$query->execute();
				$objRes = $query->fetch(PDO::FETCH_OBJ);
				if (!$objRes)
					die("Triche détéctée: vous n'avez pas répondu à la question d'avant");
			}
			
			// récupérer la prochaine question
			
			$getQuestion = "SELECT nomQcm, questions.idQuestion, nomQuestion, limiteTemps, illustration FROM questions INNER JOIN qcmPossedeQuestions USING (idQuestion) INNER JOIN qcm USING (idQcm) INNER JOIN illustrations USING (idIllustration) WHERE idQcm = $idQcm ORDER BY numeroQuestion ASC LIMIT 1 OFFSET $numeroQuestion";
			
			$query = parent::$bdd->prepare($getQuestion);
			$query->execute();
			
			return $query->fetch(PDO::FETCH_OBJ);

		}
		
		public function cloturerQcm() {
			$idTentative = $this->idTentative;
			$sql = "UPDATE userTenteQcm SET termine = 1 WHERE idTentative = $idTentative";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
		}
		
		
		public function getNbQuestionsEtNote() {
		
			$idTentative = $this->idTentative;
			$queryNote = "SELECT idQcm, note FROM userTenteQcm INNER JOIN qcm USING (idQcm) INNER JOIN qcmPossedeQuestions USING (idQcm) INNER JOIN questions USING (idQuestion) WHERE idTentative=$idTentative";
			$query = parent::$bdd->prepare($queryNote);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			$pourcentageReussite = ($objRes->note/Utilitaires::getNbQuestionsParIdQcm($objRes->idQcm))*100;
			
			$queryPourcentage = "UPDATE userTenteQcm SET pourcentageReussite = $pourcentageReussite WHERE idTentative=$idTentative";
			$query = parent::$bdd->prepare($queryPourcentage);
			$query->execute();
			 
			return $objRes;
			
		}
	
		public function reponsesPossiblesQcmEtChoixUser() {
			$idQcm = $this->idQcm;
			$idTentative = $this->idTentative;
			$tabFinal = array();
			
			$sql = "SELECT idQuestion, nomQuestion, idReponse, nomReponse, correct, illustration FROM qcmPossedeQuestions INNER JOIN questionPossedeReponses USING (idQuestion) INNER JOIN reponses USING (idReponse) INNER JOIN questions USING (idQuestion) INNER JOIN illustrations USING (idIllustration) WHERE idQcm = $idQcm ORDER BY numeroQuestion ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$tabFinal[0] = $query->fetchAll();
			
			$sql = "SELECT idQuestion, idReponse FROM participerAvecReponses RIGHT JOIN participerQuestion USING (idParticipation) INNER JOIN qcmPossedeQuestions USING (idQuestion) WHERE idTentative = $idTentative ORDER BY numeroQuestion ASC";
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$tabFinal[1] = $query->fetchAll();
			
			
			return $tabFinal;
		}
		
		public function getNomQcmEtPourcentageReussite() {

			$idTentative = $this->idTentative;
			
			$sql = "SELECT nomQcm, pourcentageReussite FROM userTenteQcm INNER JOIN qcm USING (idQcm) WHERE idTentative = $idTentative";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				die();
			
			return $objRes;
		}
		
		public function getResultatsQCM() {
			
			$resultlats = array();
			
			$resultats[0] = 1;
			$resultats[1] = $this->getNbQuestionsEtNote();
			$resultats[2] = $this->reponsesPossiblesQcmEtChoixUser();
			$resultats[3] = $this->getNomQcmEtPourcentageReussite();
		
			return $resultats;
		}
		
		public function preparerProchaineQuestion() {
		
			$idUser = $this->idUser;
			$idQcm = $this->idQcm;
			$idTentative = intval($_POST['idTentative']);
		
			$question = $this->getNextQuestion();
			
			if (!$question) {
				// la dernière question vient de se terminer, on prépare l'affichage des résultats par la vue.
				
				// pour dire que le qcm est fini
				$this->cloturerQcm();
			
				// envoyer le tableau qui contient les infos sur les résultats à la vue.
				
				return $this->getResultatsQCM();
				
			}
			else {
				$getReponsesDejaMises = array();
				
				// il reste encore au moins une question avant la fin du QCM
				
				$idNextQuestion = $question->idQuestion;
				
				$limiteTemps = $question->limiteTemps;
				
				// on vérifie si l'élève a déjà participé a cette question et qu'il y a déjà répondu. si c'est le cas, il ne doit pas pouvoir changer ses réponses
				if ($this->questionDejaRepondue($idNextQuestion)) {
					
					$sql = "SELECT idReponse FROM participerAvecReponses INNER JOIN participerQuestion USING (idParticipation) WHERE idTentative = $idTentative AND idQuestion = $idNextQuestion";
					$query = parent::$bdd->prepare($sql);
					$query->execute();
					
					$getQuery = $query->fetchAll();
					
					foreach ($getQuery as $tuple) {
						$getReponsesDejaMises[] = $tuple['idReponse'];
					}
	
				}
				else {
					
					// l'élève a participé à cette question (c'est à dire qu'il y est arrivé) mais n'a pas encore répondu. il dispose de 30 secondes max pour répondre.
					
					$participer = "INSERT INTO participerQuestion (idTentative, idUser, timeLimit, idQuestion) VALUES ($idTentative, $idUser, CURRENT_TIMESTAMP + INTERVAL $limiteTemps SECOND, $idNextQuestion)";
					$query = parent::$bdd->prepare($participer);
					$query->execute();
				}
			
				
				// préparation des réponses
				
				$getReponses = "SELECT reponses.idReponse AS idReponse, nomReponse FROM reponses INNER JOIN questionPossedeReponses USING (idReponse) INNER JOIN questions USING (idQuestion) WHERE questions.idQuestion = " . $question->idQuestion;
				
				$query = parent::$bdd->prepare($getReponses);
				$query->execute();
				$reponses = $query->fetchAll();
				
				// temps restant pour répondre (30 par défaut; sinon limite - temps actuel si la question a déjà été chargée précédemment)
				
				$sql = "SELECT TIMESTAMPDIFF(SECOND, timeLimit, CURRENT_TIMESTAMP) AS ecart FROM participerQuestion WHERE idTentative = $idTentative AND idQuestion = " . $question->idQuestion;
				$query = parent::$bdd->prepare($sql);
				$query->execute();
				$objRes = $query->fetch(PDO::FETCH_OBJ);
				$tempsRestant = $objRes->ecart;
				
				// pour la vue
				
				$tabVue = array();
				
				$tabVue[0]=0;
				$tabVue[1]=$reponses;
				$tabVue[2]=$question;
				$tabVue[3]=$idTentative;
				$tabVue[4]=$getReponsesDejaMises;
				$tabVue[5]=intval($tempsRestant);
				
				return $tabVue;
		
			}
		}
		
		public function questionDejaRepondue($idQuestion) {
			$idTentative = $this->idTentative;
			
			$sql = "SELECT timeSubmit FROM participerQuestion WHERE idTentative = $idTentative AND idQuestion = $idQuestion AND timeSubmit IS NOT NULL";
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes)
				return 1;
			else
				return 0;
		}
		
		
		
		
}
		
	
?>

