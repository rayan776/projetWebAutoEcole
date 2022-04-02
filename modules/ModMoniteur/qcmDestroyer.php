<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	class QcmDestroyer extends ConnectDB {
		public function __construct() {
			parent::connect();
		}
		
		public function destroyQcm($idQcm) {
		
			// étape 1 : récupérer toutes les tentatives pour ensuite les supprimer
			
			$sql="SELECT idTentative FROM userTenteQcm WHERE idQcm = :idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
			$query->execute();
			
			$idsTentatives=$query->fetchAll();
			
			// étape 2 : aller dans la table participerQuestion, et récupérer tous les idParticipation correspondant à ces idTentative
			
			foreach ($idsTentatives as $idstents) {
				$idTentative=$idstents['idTentative'];
				$sql="SELECT idParticipation FROM participerQuestion WHERE idTentative = $idTentative";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$idsParticipations=$query->fetchAll();
				
				// étape 3 : aller dans la table participerAvecReponse, et supprimer toutes les lignes correspondant à ces idParticipation
				
				foreach($idsParticipations as $idspar) {
					$idpar=$idspar['idParticipation'];
					$sql="DELETE FROM participerAvecReponses WHERE idParticipation = $idpar";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
					
					// étape 4 : une fois que ces idParticipation sont supprimés, il faut les supprimer dans participerQuestion
					
					$sql="DELETE FROM participerQuestion WHERE idParticipation = $idpar";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
				
				
				// étape 5 : maintenant que les tables participerQuestion et participerAvecReponse sont nettoyées, il faut s'attaquer à la table userTenteQcm
				
				$sql="DELETE FROM userTenteQcm WHERE idTentative = $idTentative";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
			}
			
			// étape 6 : récupérer les id de questions du QCM
			$sql="SELECT idQuestion FROM qcmPossedeQuestions WHERE idQcm = :idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
			$query->execute();
			
			$idsQuestions=$query->fetchAll();
			
			// étape 7 : supprimer les lignes de cette table, puis de la table questionPossedeReponses, puis de la table questions
			
			foreach ($idsQuestions as $idsqsts) {
				$idQst=$idsqsts['idQuestion'];
				
				
				// étape 8 : récupérer les réponses qui étaient associées à cette question. Si elles ne sont plus liées à aucune autre question, on les supprime aussi
				$sql="SELECT idReponse FROM questionPossedeReponses WHERE idQuestion = $idQst";

				$query=parent::$bdd->prepare($sql);
				$query->execute();
				$idsReponses=$query->fetchAll();
				
				$reponsesInutiles=array();
				
				foreach ($idsReponses as $idsrps) {
					$idReponse=$idsrps['idReponse'];
					$sql="SELECT idQuestion FROM questionPossedeReponses INNER JOIN qcmPossedeQuestions USING (idQuestion) WHERE idQcm <> :idQcm AND idReponse = :idReponse";

					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idReponse", $idReponse, PDO::PARAM_INT);
					$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
					$query->execute();
					
					$resIdRep=$query->fetchAll();
					
					if (count($resIdRep)==0) {
						$reponsesInutiles[]=$idReponse;
					}
				}
				
				$sql="DELETE FROM qcmPossedeQuestions WHERE idQuestion = $idQst";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$sql="DELETE FROM questionPossedeReponses WHERE idQuestion = $idQst";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				foreach ($reponsesInutiles as $idReponse) {
					$sql="DELETE FROM reponses WHERE idReponse = :idReponse";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idReponse", $idReponse, PDO::PARAM_INT);
					$query->execute();
				}
				
				$sql="DELETE FROM questions WHERE idQuestion = $idQst";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			
			}
			
			
			// étape 9 : supprimer la ligne dans qcm
			
			$sql="DELETE FROM qcm WHERE idQcm = :idQcm";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idQcm", $idQcm, PDO::PARAM_INT);
			$query->execute();
			
			// fini.
		
		}
		
		
	}
?>
