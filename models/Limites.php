<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");

	class Limites extends ConnectDB {
	
		/*
			Quelques méthodes relatives aux limites.
			
			On assigne des limites à certaines actions, comme l'envoi de messages privés, le post de commentaires, etc. 
			Comme les droits, elle s'appliquent d'abord sur un rôle, mais peuvent être imposées exceptionnellement à un utilisateur précis.
			Elles sont caractérisées par un titre, une valeur et une période (en minutes, car ça facilite la conversion en heures/jours avec les formulaires).
			Par exemple, si l'utilisateur n, de rôle "moniteur" dépasse la limite de messages privés en 24 heures glissantes pour un moniteur
			(supposons 100 messages privés sur les dernières 24 heures), on doit l'empêcher d'envoyer de nouveaux messages. 
			Si n a une limite spécifique de -1 (peu importe la période), ça signifie qu'il n'a aucune limite d'envoi de messages privés. 
			
			L'admin a tout loisir de changer ces valeurs depuis son menu.
			
		*/
		
		public static function getLimitesRole($nomRole) {
			$query = parent::$bdd->prepare("SELECT nomLimite, CASE WHEN val = -1 THEN 'infini' ELSE val END as val, period FROM rolePossedeLimite INNER JOIN limites USING (idLimite) INNER JOIN roles USING (idRole) WHERE nomRole = :nomRole");
			$query->bindValue(":nomRole", $nomRole, PDO::PARAM_STR);
			$query->execute();
			return $query->fetchAll();
		}
		
		public static function getLimitesUser($login) {
			$query = parent::$bdd->prepare("SELECT nomLimite, CASE WHEN val = -1 THEN 'infini' ELSE val END as val, period FROM userPossedeLimite INNER JOIN limites USING (idLimite) INNER JOIN users USING (idUser) WHERE login = :login");
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			return $query->fetchAll();
		}
		
		public static function getValLimitesUser($login) {
			parent::connect();
			$limites=array();
			
			$resultats=array();
			
			$queryLimites="SELECT nomLimite FROM limites INNER JOIN rolePossedeLimite USING (idLimite) INNER JOIN roles USING (idRole) INNER JOIN detientRole USING (idRole) INNER JOIN users USING (idUser) WHERE login = :login";
			$query=parent::$bdd->prepare($queryLimites);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			$nomsLimites=$query->fetchAll();
			
			$requetes=array();
			
			foreach ($nomsLimites as $tuple) {
				$limites[$tuple['nomLimite']]=self::getLimiteUser($login, $tuple['nomLimite']);
				$requetes[$tuple['nomLimite']]=self::getQueryLimite($tuple['nomLimite']);			
			}
			
			foreach ($nomsLimites as $tuple) {
				$period=$limites[$tuple['nomLimite']][1];
				$req=parent::$bdd->prepare($requetes[$tuple['nomLimite']]);
				$req->bindValue(":login", $login, PDO::PARAM_STR);
				$req->bindValue(":period", $period, PDO::PARAM_INT);
				$req->execute();
				$objRes=$req->fetch(PDO::FETCH_OBJ);
				$resultats[$tuple['nomLimite']]=$objRes->count;
			}
			
			return $resultats;
			
		}
	
		
		public static function getLimiteUser($login, $nomLimite) {
			parent::connect();
			
			$limit = array();
			
			// on vérifie si l'utilisateur est contraint par une limite exceptionnelle
			$sql = "SELECT val, period FROM userPossedeLimite INNER JOIN users USING (idUser) INNER JOIN limites USING (idLimite) WHERE login = :login AND nomLimite = :nomLimite";
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":nomLimite", $nomLimite, PDO::PARAM_STR);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			if (!$objRes) {
				// il n'a pas de limite exceptionnelle, on regarde donc la limite attribuée à son rôle.
				
				$sql = "SELECT val, period FROM rolePossedeLimite INNER JOIN roles USING (idRole) INNER JOIN detientRole USING (idRole) INNER JOIN users USING (idUser) INNER JOIN limites USING (idLimite) WHERE login = :login AND nomLimite = :nomLimite";
				$query = parent::$bdd->prepare($sql);
				$query->bindValue(":nomLimite", $nomLimite, PDO::PARAM_STR);
				$query->bindValue(":login", $login, PDO::PARAM_STR);
				$query->execute();
				
				$objRes = $query->fetch(PDO::FETCH_OBJ);
		
			}
			
			if (!$objRes) { // si aucune limite n'a été trouvée dans la BD. cette situation n'est pas censée se produire. toutes les limites doivent être renseignées pour chaque rôle.
				$limit[0]=-2;
			}else{
				$limit[0] = $objRes->val;
				$limit[1] = $objRes->period;
			}	
	
			return $limit;
			
		}
		
		public static function getQueryLimite($limite) {
		
			$sql="";
					
			if ($limite=="limite messages") {
				$sql = "SELECT count(*) AS count FROM posterMsgPrv INNER JOIN users USING (idUser) WHERE login = :login AND datePost <= CURRENT_TIMESTAMP AND datePost >= CURRENT_TIMESTAMP - INTERVAL :period MINUTE";
			}
			elseif ($limite=="limite reservations") {
				$sql = "SELECT count(*) AS count FROM reserver INNER JOIN estEleve USING (idEleve) INNER JOIN users USING (idUser) WHERE login = :login AND dateRes >= CURRENT_TIMESTAMP - INTERVAL :period MINUTE";
			}
			elseif ($limite=="limite commentaires") {
				$sql = "SELECT count(*) AS count FROM posterComHistorique INNER JOIN users USING (idUser) WHERE login = :login AND dateCom <= CURRENT_TIMESTAMP AND dateCom >= CURRENT_TIMESTAMP - INTERVAL :period MINUTE";
			}
			elseif ($limite=="limite signalements") {
				$sql="SELECT count(*) AS count FROM signalerHistorique INNER JOIN users USING (iduser) WHERE login = :login AND dateSignal <= CURRENT_TIMESTAMP AND dateSignal >= CURRENT_TIMESTAMP - INTERVAL :period MINUTE";
			}
			elseif ($limite=="limite articles") {
				$sql="SELECT count(*) AS count FROM creerArticle INNER JOIN users USING (idUser) WHERE login = :login AND dateCr <= CURRENT_TIMESTAMP and dateCr >= CURRENT_TIMESTAMP - INTERVAL :period MINUTE";
			}
			
			return $sql;
		}
		
		public static function aDepasseLimite($login, $limite) {
			parent::connect();
		
			$limit = self::getLimiteUser($login, $limite);
			
			if ($limit[0]==-2) {
				die();
			}
			else {
				$val = $limit[0];
				$period = $limit[1];
			}
			
			if ($val == -1)
				return 0;
			elseif ($val == 0)
				return 1;
			
			$sql = self::getQueryLimite($limite);
			
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->bindValue(":period", $period, PDO::PARAM_INT);
			$query->execute();
			
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			return $objRes->count >= $val;
		}
		
		
	}
	
?>
