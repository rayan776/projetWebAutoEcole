<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class ModeleMessagerie extends ConnectDB {
		public function __construct() {
			parent::connect();
		}
		
		public function marquerGroupeCommeLu() {
			if (isset($_POST['idMessages'])) {
				if (is_array($_POST['idMessages'])) {
					foreach ($_POST['idMessages'] as $id) {
						self::marquerCommeLu($id);
					}
				}
			}
		}
		
		public function marquerCommeLu($idMsg) {

			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
			
			$sql = "SELECT dejaLu FROM recevoir WHERE idMessage = :idMsg AND idDest = :idUser";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);			
			$query->execute();
			$obj = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$obj)
				return;
			
			$dejaLu = $obj->dejaLu;
			
			if ($dejaLu == 1)
				return;
			
			$sql = "UPDATE recevoir SET dejaLu = 1 WHERE idMessage = :idMsg AND idDest = :idUser";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			$query->bindValue(":idUser", $idUser, PDO::PARAM_INT);
			$query->execute();
		}
		
		public function getMessage($idMsg) {
		
			$recu = $_GET['recu'];
			
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
		
			if ($recu == "true") {
				$querystr = "SELECT idMessage, login, dateMsg, titreMsg, contenu FROM message INNER JOIN recevoir USING (idMessage) INNER JOIN users ON (recevoir.idExp = users.idUser) WHERE idDest = :idUserActu AND idMessage = :idMsg";
			}
			else {
				
				$querystr = "SELECT idMessage, login, dateMsg, titreMsg, contenu FROM message INNER JOIN envoyer USING (idMessage) INNER JOIN users ON (envoyer.idDest = users.idUser) WHERE idExp = :idUserActu AND idMessage = :idMsg";
			}
			
			
			$query = parent::$bdd->prepare($querystr);
			$query->bindValue(":idUserActu", $idUser, PDO::PARAM_INT);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			$query->execute();
			
			return $query->fetch(PDO::FETCH_OBJ);
		}
		
		public function getListeMessages($recu) {
			$loginUserTrier = "";
		
			$newMsg = 0;
		
			// ordonner par ancienneté
			if (isset($_POST['orderByDate'])) {
				$orderByDate = $_POST['orderByDate'];
				$orderByDate = trim($orderByDate);
				switch ($orderByDate) {
					case "ASC":
					case "DESC":
						$orderByDate = $_POST['orderByDate'];
						break;
					default:
						$orderByDate = "DESC";
				}
			}
			else {
				$orderByDate = "DESC";
			}
			
			// ordonner par expéditeur/destinataire
			if (isset($_POST['orderByUser'])) {
				if (empty($_POST['orderByUser'])) {
					$orderByUser = "";
				}
				else {
					//$idUserTrier = Utilitaires::getIdUser(trim($_POST['orderByUser']));
					$loginUserTrier = "%" . trim($_POST['orderByUser']) . "%";
					/*if (!$loginUserUserTrier) {
						$orderByUser = "";
					}
					else {*/
						if ($recu == "true")
							$orderByUser = "AND login LIKE :loginUserTrier";
						else
							$orderByUser = "AND login LIKE :loginUserTrier";
					//}
				}
			}
			else {
				$orderByUser = "";
			}
		
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
		
			if ($recu == "true") {
				$queryCountMsg = "SELECT count(*) AS count FROM recevoir INNER JOIN users ON (recevoir.idExp = users.idUser) WHERE idDest = $idUser $orderByUser";
				
				$queryCountNewMsg = "SELECT count(*) AS count FROM recevoir WHERE dejaLu = 0 AND idDest = $idUser";
				
				$querystr = "SELECT dejaLu, idMessage, titreMsg, login, dateMsg, contenu FROM message INNER JOIN recevoir using (idMessage) INNER JOIN users ON (recevoir.idExp = users.idUser) WHERE idDest = $idUser $orderByUser ORDER BY dateMsg $orderByDate";
				
			}
			else {
				$queryCountMsg = "SELECT count(*) AS count FROM envoyer INNER JOIN users ON (envoyer.idDest = users.idUser) WHERE idExp = $idUser $orderByUser";
				
				$querystr = "SELECT idMessage, titreMsg, login, dateMsg, contenu FROM message INNER JOIN envoyer using (idMessage) INNER JOIN users ON (envoyer.idDest = users.idUser) WHERE idExp = $idUser $orderByUser ORDER BY dateMsg $orderByDate";
				
			}
			
			$countMsg = self::getCountMsg($queryCountMsg, $orderByUser, $loginUserTrier); // pour la vue
			
			$limit = 10; // nombre de messages par page
			$offset = 0;
			
			if (isset($_POST['page'])) {
				$offset = (intval($_POST['page']) != 0) ? (intval($_POST['page'])-1)*$limit : 1;
				if ($offset < 0)
					$offset = 0;
			}
			
			$querystr .= " LIMIT $limit OFFSET $offset";

			
			$query = parent::$bdd->prepare($querystr);
			if (!empty($orderByUser))
				$query->bindValue(":loginUserTrier", $loginUserTrier, PDO::PARAM_STR);
			$query->execute();
			
			$valRetour = array();
			
			$valRetour[] = $query->fetchAll();
			$valRetour[] = $countMsg;
			
			if ($recu == "true") {
				$valRetour[] = self::getCountMsg($queryCountNewMsg, "", "");
			}
			
			return $valRetour;
		}
		
		public function getCountMsg($queryCountMsg, $orderByUser, $loginUserTrier) {
			$query = parent::$bdd->prepare($queryCountMsg);
			if (!empty($orderByUser))
				$query->bindValue(":loginUserTrier", $loginUserTrier, PDO::PARAM_STR);			
			$query->execute();
			$countMsg = $query->fetch(PDO::FETCH_OBJ);
			return $countMsg->count;
		}
		
		public function getUsers() {
			$query = parent::$bdd->prepare('SELECT login FROM users WHERE login <> :userActu');
			
			$query->bindValue(":userActu", $_SESSION['login']['login'], PDO::PARAM_STR);
			$query->execute();
			
			return $query->fetchAll();
		}
		
		public function sendMessage() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré";
		
			if (isset($_POST['dest']) && isset($_POST['message']) && isset($_POST['titreMsg'])) {
				if ($_POST['dest'] == $_SESSION['login']['login']) {
					return "Vous ne pouvez pas vous envoyer un message à vous même.";
				}
				if (empty($_POST['dest']) || empty(trim($_POST['message'])) || empty($_POST['titreMsg']))
					return "L'un des champs est vide.";
			}
			else {
				?> <script> window.location = "index.php?module=ModMessagerie"; </script> <?php
				return;
			}
			
			if (Limites::aDepasseLimite($_SESSION['login']['login'], "limite messages"))
				return "Vous avez dépassé votre limite de messages privés, veuillez réessayer plus tard.";
					
					
			$idExp = Utilitaires::getIdUser($_SESSION['login']['login']);
			$idDest = Utilitaires::getIdUser($_POST['dest']);
				
			$msg = htmlspecialchars($_POST['message'], ENT_QUOTES);
			$msg = trim($msg);
			
			$titreMsg = trim($_POST['titreMsg']);
			$titreMsg = htmlspecialchars($titreMsg, ENT_QUOTES);			
			
			if (strlen($titreMsg) > 100)
				return "Le titreMsg ne doit pas dépasser 100 caractères.";
			
			$query = parent::$bdd->prepare('SELECT login FROM users WHERE login=:dest');
			$query->bindValue(":dest", $_POST['dest'], PDO::PARAM_STR);
			$query->execute();
			
			$resultat = $query->fetchAll();
			
			if (count($resultat) == 0)
				return "Cet utilisateur n'existe pas";

			$query = parent::$bdd->prepare('INSERT INTO message (titreMsg, contenu) VALUES (:titreMsg, :contenu)');
			$query->bindValue(":titreMsg", $titreMsg, PDO::PARAM_STR);
			$query->bindValue(":contenu", $msg, PDO::PARAM_STR);
			
			if (!$query->execute())
				return "Erreur, veuillez contactez l'administration.";
			
			$idMsg = parent::$bdd->lastInsertId();

			$query = parent::$bdd->prepare('INSERT INTO envoyer VALUES (:id, :idExp, :idDest)');
			$query->bindValue(":id", $idMsg, PDO::PARAM_INT);
			$query->bindValue(":idExp", $idExp, PDO::PARAM_INT);
			$query->bindValue(":idDest", $idDest, PDO::PARAM_INT);
			$query->execute();
				
			$query = parent::$bdd->prepare('INSERT INTO recevoir (idMessage, idDest, idExp) VALUES (:id, :idDest, :idExp)');
			$query->bindValue(":id", $idMsg, PDO::PARAM_INT);
			$query->bindValue(":idDest", $idDest, PDO::PARAM_INT);
			$query->bindValue(":idExp", $idExp, PDO::PARAM_INT);
			$query->execute();
			
			$query=parent::$bdd->prepare("INSERT INTO posterMsgPrv VALUES ($idExp, CURRENT_TIMESTAMP)");
			$query->execute();

			return "Message envoyé.";
		
		}
		
		public function supprimerGroupeDeMessages() {
		
			$counter=0;
		
			if (Tokens::checkCSRF()) {
				return "Formulaire expiré";
			}
			
			if (!isset($_POST['confirm']))
				return "Vous n'avez pas confirmé";
		
			if (isset($_POST['idMessagesASupprimer']) && is_array($_POST['idMessagesASupprimer'])) {
				if (isset($_POST['recu'])) {
					if ($_POST['recu'] == "true" || $_POST['recu'] == "false") {
						$table = ($_POST['recu'] == "true") ? "recevoir" : "envoyer";
						$tableOpposee = ($table=="recevoir") ? "envoyer" : "recevoir";
						
						$idDestOuExp = ($_POST['recu'] == "true") ? "idDest" : "idExp";
						$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);
						
						foreach ($_POST['idMessagesASupprimer'] as $idMsg) {
							$sql = "DELETE FROM $table WHERE idMessage = :idMsg AND $idDestOuExp = $idUser";
							$query = parent::$bdd->prepare($sql);
							$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
							$query->execute();
							
							// si le message supprimé n'existe pas non plus dans la table opposée, alors on le supprime complètement de la BD
							
							$sql = "SELECT * FROM $tableOpposee WHERE idMessage = :idMsg";
							$query=parent::$bdd->prepare($sql);
							$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
							$query->execute();
							$objRes=$query->fetch(PDO::FETCH_OBJ);
							
							if (!$objRes) {
								$sql="DELETE FROM message WHERE idMessage = :idMsg";
								$query=parent::$bdd->prepare($sql);
								$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
								$query->execute();
							}
							
							$counter++;
						}
						
						return "$counter messages supprimés avec succès.";
					}
				}	
			}
			
			return "Erreur";
		}
		
		public function supprimerMessage() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré";
		
			if (!isset($_POST['confirm']))
				return "Vous n'avez pas confirmé";
		
			$idMsg;
			
			if (isset($_GET['idMsg']))
				$idMsg = $_GET['idMsg'];
			else
				return "Erreur";
		
			$table;
		
			if (isset($_POST['recu']))
				if ($_POST['recu'] == "true" || $_POST['recu'] == "false") {
					$table = ($_POST['recu'] == "true") ? "recevoir" : "envoyer";
					$idDestOuExp = ($_POST['recu'] == "true") ? "idDest" : "idExp";
				}
				else
					return "Erreur";
			else
				return "Erreur";
			
			$tableOpposee = ($table=="recevoir") ? "envoyer" : "recevoir";
				
			$idUser = Utilitaires::getIdUser($_SESSION['login']['login']);

			$sql = "SELECT * FROM $table WHERE idMessage = :idMsg AND $idDestOuExp = $idUser";
				
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			
			if (!$query->execute())
				return "La requête de vérification d'existence du message a échoué";
			
			$msgExiste = $query->fetchAll();
			
			if (count($msgExiste) == 0)
				return "Le message n'existe pas";
		
			$sql="DELETE FROM $table WHERE idMessage = :idMsg AND $idDestOuExp = $idUser";
		
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			$query->execute();
			
			// si le message supprimé n'existe pas non plus dans la table opposée, alors on le supprime complètement de la BD
							
			$sql = "SELECT * FROM $tableOpposee WHERE idMessage = :idMsg";
			
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes) {
				$sql="DELETE FROM message WHERE idMessage = :idMsg";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":idMsg", $idMsg, PDO::PARAM_INT);
				$query->execute();
			}
			
			if ($query->execute())
				return "Message supprimé avec succès";
			else
				return "La requête finale n'a pas fonctionné";
		}
	}
	
?>
