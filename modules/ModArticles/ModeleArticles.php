<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
		
	class ModeleArticles extends ConnectDB {

		public function __construct() {
			parent::connect();
		}
		
		public function getCategories() {
			$sql="SELECT * FROM articleCategories";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function insererArticle($update) {
			
			// vérification de la validité du tableau POST
			
			if (Tokens::checkCSRF())
				return array ("Formulaire expiré.");
				
			// vérification de la limite
			
			
			if ($update==0) {
				if (Limites::aDepasseLimite($_SESSION['login']['login'], "limite articles"))
					return array("Vous avez dépassé votre limite de nouveaux articles publiés.");
			}
			else {
				$idArt=$_POST['idArt'];
			}
		
		
			if (!isset($_POST['titreArt'])||!isset($_POST['catArticle'])||!isset($_POST['contenuArticle']))
				return array("Erreur.");
			
			$msgRetour=array();
			
			$titre=htmlspecialchars(trim($_POST['titreArt']), ENT_QUOTES);
			$categorie="";
			$contenuArticle="";
			
			if (strlen($titre)<1||strlen($titre)>100)
				$msgRetour[]="Le titre doit être compris entre 1 et 100 caractères.";
			
			// vérifier que la valeur de catArticle, correspond à un ID d'article existant
			
			$idCat=intval($_POST['catArticle']);
			if ($idCat==0)
				$msgRetour[]="Catégorie invalide.";
			
			$sql="SELECT * FROM articleCategories WHERE idCat = :idCat";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
			$query->execute();
			
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			if (!$objRes)
				$msgRetour[]="Catégorie invalide.";
			
			if (count($msgRetour)>0)
				return $msgRetour;
			
			$contenuArticle=htmlspecialchars(trim($_POST['contenuArticle']), ENT_QUOTES);
			
			// pour les droits
			
			$droitsArticle=array();
			
			$droits=array("lire", "supprimer", "modifier", "commenter");
			$roles=array("moniteur", "eleve", "visiteur");
			$idRoles=array("moniteur" => 4, "eleve" => 3, "visiteur" => 8);
			
			$droitsArticle=array();
			
			
			// initialisation des droits (tout à 0)
			foreach ($roles as $role) {
				$droitsArticle[$role]=array();			
				foreach ($droits as $droit) {
					$droitsArticle[$role][$droit]=0;
				}
			}
			
			if (Utilitaires::getRoleCurrentUser()=="eleve") {
				// si un élève écrit exceptionnellement un article, les droits par défaut s'appliquent
				foreach ($droits as $droit) {
					$droitsArticle["moniteur"][$droit]=1;
					
					if ($droit=="commenter") {
						$droitsArticle["eleve"][$droit]=1;
					}
					if ($droit=="lire") {
						$droitsArticle["eleve"][$droit]=1;
						$droitsArticle["visiteur"][$droit]=1;
					}
				}
			}
				
			// application des droits définis par celui qui ajoute/modifie l'article
			
			// un élève, si il est autorisé exceptionnellement à écrire des articles (ce qui est possible), ne doit pas modifier les droits d'article
			if (Utilitaires::getRoleCurrentUser()!="eleve") {
				
				if (isset($_POST['droits'])) {
					foreach ($roles as $role) {
						if (isset($_POST['droits'][$role])&&is_array($_POST['droits'][$role])) {
							foreach ($_POST['droits'][$role] as $droit => $val) {
								$droitsArticle[$role][$droit]=$val;
							}
						}
					}
				}
				else {
					// si aucune case n'est cochée, c'est que l'article est réservé aux moniteurs et à l'admin
					
				}
			}
			
			// un moniteur ne doit pas modifier les droits de ses collègues
			if (Utilitaires::getRoleCurrentUser()=="moniteur") {
				foreach ($droits as $droit) {
					$droitsArticle["moniteur"][$droit]=1;
				}
			}
			
			// si l'admin désactive le droit de lecture des moniteurs, tous leurs autres droits seront forcément désactivés
			if (Utilitaires::getRoleCurrentUser()=="admin") {
				if ($droitsArticle["moniteur"]["lire"]==0) {
					foreach ($droits as $droit) {
						$droitsArticle["moniteur"][$droit]=0;
					}
				}
			}
			
			// préparation des requêtes SQL
			
			

			if ($update==0)
				$sqlInsertArticle="INSERT INTO article (nomArt, contenu, idCat) VALUES (:nomArt, :contenu, :idCat)";
			else
				$sqlInsertArticle="UPDATE article SET nomArt = :nomArt, contenu = :contenu, idCat = :idCat WHERE idArt = :idArt";
			
			$query=parent::$bdd->prepare($sqlInsertArticle);
			$query->bindValue(":nomArt", $titre, PDO::PARAM_STR);
			$query->bindValue(":contenu", $contenuArticle, PDO::PARAM_STR);
			$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
			
			if ($update==1)
				$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			
			
			if (!$query->execute())
				return array("Erreur.");
			
	
			if ($update==0)
				$idArt=parent::$bdd->lastInsertId();
		
			foreach ($roles as $role) {
				$idRole=$idRoles[$role];
				
				$lire=$droitsArticle[$role]["lire"];
				$supprimer=$droitsArticle[$role]["supprimer"];
				$commenter=$droitsArticle[$role]["commenter"];
				$modifier=$droitsArticle[$role]["modifier"];

				if ($update==0)
					$sqlDroitsArt="INSERT INTO droitsArticleRole VALUES ($idArt, $idRole, $lire, $supprimer, $commenter, $modifier)";
				else
					$sqlDroitsArt="UPDATE droitsArticleRole SET lire = $lire, supprimer = $supprimer, commenter = $commenter, modifier = $modifier WHERE idRole = $idRole AND idArt = $idArt";
			
				$query=parent::$bdd->prepare($sqlDroitsArt);
				$query->execute();
			}
			
			// auteur de l'article
			

			$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
			
			if ($update==0) {
				$sql="INSERT INTO posterArt VALUES ($idArt, $idUser)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			
				$sql="INSERT INTO creerArticle (idUser) VALUES ($idUser)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
			}
			
			// mise à jour de modifierArticle
			
			if ($update==1) {
				$sql="SELECT * FROM modifierArticle WHERE idArt = $idArt";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				$objRes=$query->fetch(PDO::FETCH_OBJ);
				
				if (!$objRes) {
					// l'article n'a jamais été modifié
					$sql="INSERT INTO modifierArticle (idArt, idUser) VALUES ($idArt, $idUser)";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
				else {
					// l'article a déjà été modifié
					$sql="UPDATE modifierArticle SET idUser = $idUser, dateModif = CURRENT_TIMESTAMP WHERE idArt = $idArt";
					$query=parent::$bdd->prepare($sql);
					$query->execute();
				}
			}
		
			if ($update==0)
				return array("Article inséré avec succès. <a href='index.php?module=ModArticles&action=voirArticle&idArt=$idArt'> Voir nouvel article </a>");
			else
				return array("Article mis à jour avec succès. <a href='index.php?module=ModArticles&action=voirArticle&idArt=$idArt'> Voir article mis à jour </a>");
			
		}
		
		public function getLastModif($idArt) {
			
			$sql="SELECT idUser, login, dateModif FROM modifierArticle INNER JOIN users USING (idUser) WHERE idArt = :idArt";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_OBJ);
			
		}
		
		public function getListCategories() {
			$sql="SELECT count(idArt) AS nbArticles, idCat, category FROM articleCategories LEFT OUTER JOIN article USING (idCat) GROUP BY (idCat)";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			return $query->fetchAll();
			
		}
		
		public function gererCategories() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
		
			if (isset($_POST['majCategories'])&&isset($_POST['categories'])&&is_array($_POST['categories'])) {
				$nbCat=0;
				if (isset($_POST['majCat'])&&is_array($_POST['majCat'])) {
					foreach ($_POST['majCat'] as $idCat) {
						$sql="UPDATE articleCategories SET category = :category WHERE idCat = :idCat";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":category", $_POST['categories'][$idCat], PDO::PARAM_STR);
						$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
						if ($query->execute())
							$nbCat++;
						
					}
					
					return "$nbCat catégories mises à jour avec succès.";
				}
			}
			elseif (isset($_POST['deleteCat'])&&isset($_POST['categories'])&&is_array($_POST['categories'])) {
			
				$nbCat=0;
				if (isset($_POST['majCat'])&&is_array($_POST['majCat'])) {
					foreach ($_POST['majCat'] as $idCat) {
					
						// les articles faisant partie de la catégorie qu'on veut supprimer passent dans la catégorie "Autres"
						$sql="UPDATE article SET idCat=1 WHERE idCat=:idCat";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
						$query->execute();
					
						$sql="DELETE FROM articleCategories WHERE idCat = :idCat";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
						if ($query->execute())
							$nbCat++;
						
					}
					
					return "$nbCat catégories supprimées.";
				}

			}
			elseif (isset($_POST['addCat'])) {
			
				if (isset($_POST['nomNewCat'])&&strlen($_POST['nomNewCat'])>0&&strlen($_POST['nomNewCat'])<50) {
				
					// on vérifie si le nom existe déjà dans la BD
					
					$sql="SELECT category FROM articleCategories WHERE category = :category";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":category", $_POST['nomNewCat'], PDO::PARAM_STR);
					$query->execute();
					$objRes=$query->fetch(PDO::FETCH_OBJ);
					if ($objRes)
						return "Cette catégorie existe déjà.";
					
					$sql="INSERT INTO articleCategories (category) VALUES (:category)";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":category", $_POST['nomNewCat'], PDO::PARAM_STR);
					
					if ($query->execute())
						return "Catégorie ajoutée.";
					else
						return "Erreur.";
				
				}
			
				
			}
			
			
		}
		
		public function getArticles() {
			
			// gestion du tri
			
			
			$trierParTitre="";
			$trierParAut="";
			$trierParCat="";
			$trierParContenu="";
			$trierParDate="";
			$trierParDateOrdre="ORDER BY datePub DESC";
			
			if (isset($_POST['trierPar'])&&is_array($_POST['trierPar'])) {

				// trier par titre
				
				if (in_array("titre", $_POST['trierPar'])) {
					if (!isset($_POST['titreArt']))
						return array();
					$trierParTitre=" AND nomArt LIKE :nomArt ";
					$nomArt="%".htmlspecialchars($_POST['titreArt'], ENT_QUOTES)."%";
				}
				
				if (in_array("cat", $_POST['trierPar'])) {
					if (!isset($_POST['trierParCat']))
						return array();
					$trierParCat=" AND idCat = :idCat ";
					$idCat=$_POST['trierParCat'];
				}
				
				if (in_array("aut", $_POST['trierPar'])) {
					if (!isset($_POST['trierParAut']))
						return array();
					$loginAut="%".$_POST['trierParAut']."%";
					$trierParAut=" AND login LIKE :loginAut ";
				}
				
				if (in_array("cont", $_POST['trierPar'])) {
					if (!isset($_POST['contenuArt']))
						return array();
					$trierParContenu=" AND contenu LIKE :contenu ";
					$contenu="%".htmlspecialchars($_POST['contenuArt'], ENT_QUOTES)."%";
				}
				
				if (in_array("date", $_POST['trierPar'])) {
					if (!isset($_POST['dateDeb'])||!isset($_POST['dateFin'])||!isset($_POST['ordreDate']))
						return array();
					$trierParDate=" AND datePub >= :dateDeb AND datePub <= :dateFin ";
					
					switch ($_POST['ordreDate']) {
						case "ASC":
							$ordreDate="ASC";
							break;
						case "DESC":
						default:
							$ordreDate="DESC";
					}
					
					$trierParDateOrdre=" ORDER BY datePub $ordreDate";
				}
				
				
			}
		
			$sql="SELECT idUser, idArt, nomArt, datePub, login, contenu, category FROM articleCategories INNER JOIN article USING (idCat) INNER JOIN posterArt USING (idArt) INNER JOIN users USING (idUser) WHERE datePub IS NOT NULL $trierParTitre $trierParAut $trierParCat $trierParContenu $trierParDate $trierParDateOrdre";
			

			
			$query=parent::$bdd->prepare($sql);
			
			if (!empty($trierParTitre))
				$query->bindValue(":nomArt", $nomArt, PDO::PARAM_STR);
			if (!empty($trierParAut))
				$query->bindValue(":loginAut", $loginAut, PDO::PARAM_STR);
			if (!empty($trierParCat))
				$query->bindValue(":idCat", $idCat, PDO::PARAM_INT);
			if (!empty($trierParContenu))
				$query->bindValue(":contenu", $contenu, PDO::PARAM_STR);
			if (!empty($trierParDate)) {
				$query->bindValue(":dateDeb", $_POST['dateDeb'], PDO::PARAM_STR);
				$query->bindValue(":dateFin", $_POST['dateFin'], PDO::PARAM_STR);
			}
			
			$query->execute();
			return $query->fetchAll();
		}
		
		public function getArticleById($idArt) {
			$sql="SELECT idArt, idCat, nomArt, datePub, login, contenu, category FROM articleCategories INNER JOIN article USING (idCat) INNER JOIN posterArt USING (idArt) INNER JOIN users USING (idUser) WHERE idArt = :idArt";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_OBJ);
		}
		
		public function getCommentById($idCom) {
			$sql="SELECT idCom, nomArt, commentaire.contenu, dateCom, login FROM commentaire INNER JOIN posterCom USING (idCom) INNER JOIN concerner USING (idCom) INNER JOIN article USING (idArt) INNER JOIN users ON (posterCom.idPosteur=users.idUser) WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $idCom, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_OBJ);
		}
		
		public function articleAppartientA($idArt, $login) {
			$sql="SELECT * FROM posterArt INNER JOIN users USING (idUser) WHERE idArt = :idArt AND login = :login";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			$query->bindValue(":login", $login, PDO::PARAM_STR);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				return 0;
			else
				return 1;
		}
		
		public function getDroitsArticle($idArt, $role) {
		
			$sql="SELECT idRole, lire, supprimer, commenter, modifier FROM droitsArticleRole INNER JOIN roles USING (idRole) WHERE idArt = :idArt";
			
			if (!empty($role))
				$sql.=" AND nomRole = :nomRole";
				
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			
			if (!empty($role))
				$query->bindValue(":nomRole", $role, PDO::PARAM_STR);
				
			$query->execute();
			
			if (!empty($role))
				return $query->fetch(PDO::FETCH_OBJ);
			
			return $query->fetchAll();
		}
		
		public function userPossedeDroitArticle($idArt, $login, $droit) {
			// retourne vrai si un utilisateur possède un certain droit sur un certain article, faux sinon
			
			// le droit doit être soit lire, supprimer, ou commenter
			switch ($droit) {
				case "lire":
				case "supprimer":
				case "commenter":
				case "modifier":
					break;
				default:
					return 0;
			}
			
			
			$droitsArt=$this->getDroitsArticle($idArt, Utilitaires::getRoleCurrentUser());
			
			return $droitsArt->$droit==1;
			
		}
		
		public function getCommentsArticle($idArt) {
			$sql="SELECT idCom, login, contenu, dateCom FROM commentaire INNER JOIN concerner USING (idCom) INNER JOIN posterCom USING (idCom) INNER JOIN users ON (posterCom.idPosteur=users.idUser) WHERE idArt = :idArt ORDER BY dateCom DESC";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $idArt, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchAll();
		}
		
		public function insererCommentaire() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!isset($_POST['posterCom']))
				return "Erreur.";
			
			if (!isset($_GET['idArt']))
				return "Erreur.";
			
			if (!isset($_POST['comment']))
				return "Erreur.";
			
			// vérification des droits, puis de la limite
			
			if (!Utilitaires::possedePermission($_SESSION['login']['login'], "poster commentaires"))
				return "Vous n'avez pas le droit de poster des commentaires.";
			
			if (Utilitaires::getRoleCurrentUser()!="admin") {
				$droitsArticle=$this->getDroitsArticle($_GET['idArt'], Utilitaires::getRoleCurrentUser());
				
				if ($droitsArticle->commenter==0)
					return "Vous n'avez pas le droit de poster des commentaires.";
			}
			
			if (Limites::aDepasseLimite($_SESSION['login']['login'], "limite commentaires"))
				return "Vous avez dépassé votre limite de commentaires, veuillez réessayer plus tard.";
			
			// vérification du commentaire
			
			if (strlen(trim($_POST['comment']))<1||strlen(trim($_POST['comment']))>250)
				return "La taille du commentaire doit être comprise entre 1 et 250 caractères.";
			
			// préparation de la requête SQL
			
			$contenu=htmlspecialchars(trim($_POST['comment']), ENT_QUOTES);
			$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
			
			$sql="INSERT INTO commentaire (contenu) VALUES (:contenu)";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":contenu", $contenu, PDO::PARAM_STR);
			$query->execute();
			
			$idCom=parent::$bdd->lastInsertId();
			
			$sql="INSERT INTO concerner VALUES ($idCom, :idArt)";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idArt", $_GET['idArt'], PDO::PARAM_INT);
			$query->execute();
			
			$sql="INSERT INTO posterCom VALUES ($idCom, $idUser)";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$sql="INSERT INTO posterComHistorique (idUser) VALUES ($idUser)";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			return "Commentaire inséré avec succès.";
			
			
		}
		
		public function deleteComment() {
		
		
			if (Utilitaires::getRoleCurrentUser()!="admin"&&Utilitaires::getRoleCurrentUser()!="moniteur")
				return "";
			
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
			
			if (!Utilitaires::possedePermission($_SESSION['login']['login'], "supprimer commentaires"))
				return "Vous n'avez pas le droit de supprimer un commentaire.";
			
			if (!isset($_POST['idCom']))
				return "Erreur.";
			
			// l'admin peut supprimer tous les commentaires
			// les moniteurs ne peuvent aps supprimer les commentaires de l'admin
			
			$peutSupprimer=0;
			
			if (Utilitaires::getRoleCurrentUser()=="admin")
				$peutSupprimer=1;
			else {
				if (Utilitaires::roleCommentaire($_POST['idCom'])=="eleve"||Utilitaires::roleCommentaire($_POST['idCom'])=="moniteur") {
					$peutSupprimer=1;
				}
			}
			
			if ($peutSupprimer==0)
				return "Vous n'avez pas le droit de supprimer ce commentaire.";
			
			// préparation des requêtes SQL
			
			$sql="DELETE FROM posterCom WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $_POST['idCom'], PDO::PARAM_INT);
			$query->execute();
			
			$sql="DELETE FROM signalerCom WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $_POST['idCom'], PDO::PARAM_INT);
			$query->execute();
			
			$sql="DELETE FROM concerner WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $_POST['idCom'], PDO::PARAM_INT);
			$query->execute();
			
			$sql="DELETE FROM commentaire WHERE idCom = :idCom";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":idCom", $_POST['idCom'], PDO::PARAM_INT);
			$query->execute();
			
			return "Commentaire supprimé.";
			
		}
		
		public function deleteArticle() {
			if (Tokens::checkCSRF()) {
				return "Formulaire expiré.";
			}
			
			if (!isset($_POST['idArt']))
				return "";
			
			$peutSupprimer=0;
			
			// si on est admin ou que l'article nous appartient, on peut forcément le supprimer
			if (Utilitaires::getRoleCurrentUser()=="admin"||$this->articleAppartientA($_POST['idArt'], $_SESSION['login']['login'])) {
				$peutSupprimer=1;
			}
			else {
				// sinon il faut vérifier les droits de notre rôle sur cet article
				
				if (!Utilitaires::possedePermission($_SESSION['login']['login'], "gerer articles"))
					return "Vous ne pouvez pas supprimer d'articles.";
				
				$droitsArt=$this->getDroitsArticle($_POST['idArt'], Utilitaires::getRoleCurrentUser());
				
				if ($droitsArt->supprimer==1)
					$peutSupprimer=1;
				
			}
			
			if ($peutSupprimer==1) {
			
				$comments=$this->getCommentsArticle($_POST['idArt']);
				
				// suppression des tuples de la base de données liés à l'article
				
				$sql="DELETE FROM modifierArticle WHERE idArt = :idArt; DELETE FROM signalerArt WHERE idArt = :idArt; DELETE FROM posterArt WHERE idArt = :idArt; DELETE FROM droitsArticleRole WHERE idArt = :idArt; DELETE FROM concerner WHERE idArt = :idArt; DELETE FROM article WHERE idArt = :idArt";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":idArt", $_POST['idArt'], PDO::PARAM_INT);
				$query->execute();
				
				// suppression des commentaires postés pour cet article
				
				foreach ($comments as $com) {
					$sql="DELETE FROM posterCom WHERE idCom = :idCom; DELETE FROM signalerCom WHERE idCom = :idCom; DELETE FROM concerner WHERE idCom = :idCom; DELETE FROM commentaire WHERE idCom = :idCom";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idCom", $com['idCom'], PDO::PARAM_INT);
					$query->execute();
				}
				
				return "Article supprimé.";
			}
			else {
				return "Vous ne pouvez pas supprimer cet article.";
			}
	
		}
		
		public function dejaSignale($id, $type) {
			if ($type=="comment") {
				$nomTable="signalerCom";
				$nomId="idCom";
			}
			else {
				$nomTable="signalerArt";
				$nomId="idArt";
			}
			
			$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
			
			$sql="SELECT * FROM $nomTable WHERE idUser = $idUser AND $nomId = :$nomId";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":$nomId", $id, PDO::PARAM_INT);
			$query->execute();
			$objRes=$query->fetch(PDO::FETCH_OBJ);
			
			if ($objRes)
				return 1;
			else
				return 0;
			
		}
		
		public function transmettreSignalement() {
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
				
			$type="";
			
			if (isset($_POST['type'])) {
				switch ($_POST['type']) {
					case "comment":
					case "article":
						$type=$_POST['type'];
						break;
					default:
						return "Erreur.";
				}
			}
			else
				return "Erreur.";
				
			
			// vérification des limites
			
			if (Limites::aDepasseLimite($_SESSION['login']['login'], "limite signalements"))
				return "Vous avez dépassé votre limite de signalements.";
				
			
			if ($type=="comment") {
				if (!isset($_POST['idCom']))
					return "Erreur.";
				
				if (!$this->getCommentById($_POST['idCom']))
					return "Commentaire inexistant.";
				
				$idCom=$_POST['idCom'];
				$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
				
				// un utilisateur ne doit pas pouvoir signaler ses propres commentaires
				$sql="SELECT idCom FROM posterCom WHERE idUser = $idUser AND idCom = $idCom";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				$objRes=$query->fetch(PDO::FETCH_OBJ);
				
				if ($objRes)
					return "Vous ne pouvez pas signaler vos propres commentaires.";
				
				$motif="";
				
				if (isset($_POST['motif']))
					$motif=htmlspecialchars($_POST['motif'], ENT_QUOTES);
				
				$sql="INSERT INTO signalerCom (idUser, idCom, motif) VALUES ($idUser, $idCom, :motif)";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":motif", $motif, PDO::PARAM_STR);
				
				if (!$query->execute())
					return "Erreur.";
					
				
				return "Merci d'avoir signalé ce commentaire, l'administration examinera votre signalement dès que possible.";
				
			}
			else {
				if (!isset($_POST['idArt']))
					return "Erreur.";
					
				if (!$this->getArticleById($_POST['idArt']))
					return "Article inexistant.";
				
				$motif="";
				$idArt=$_POST['idArt'];
				
				if (isset($_POST['motif']))
					$motif=htmlspecialchars($_POST['motif'], ENT_QUOTES);
				
				$idUser=Utilitaires::getIdUser($_SESSION['login']['login']);
				$idArt=$_POST['idArt']; // pas de risque d'injection SQL car on sait que le commentaire existe dans la BD
			
				// un utilisateur ne doit pas pouvoir signaler ses propres articles
				$sql="SELECT idArt FROM posterArt WHERE idUser = $idUser AND idArt = $idArt";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				$objRes=$query->fetch(PDO::FETCH_OBJ);
				
				if ($objRes)
					return "Vous ne pouvez pas signaler vos propres articles.";
				
				$sql="INSERT INTO signalerArt (idUser, idArt, motif) VALUES ($idUser, $idArt, :motif)";
				$query=parent::$bdd->prepare($sql);
				$query->bindValue(":motif", $motif, PDO::PARAM_STR);
				
				if (!$query->execute())
					return "Erreur.";
				
				$sql="INSERT INTO signalerHistorique (idUser) VALUES ($idUser)";
				$query=parent::$bdd->prepare($sql);
				$query->execute();
				
				return "Merci d'avoir signalé cet article, l'administration examinera votre signalement dès que possible.";
			}
		}
		
		public function getSignalements() {
			$signalements=array();
			
			// articles signalés
			$sql="SELECT idSignal, idUser, login, motif, idArt, nomArt FROM signalerArt INNER JOIN users USING (idUser) INNER JOIN article USING (idArt)";
			
			if (Utilitaires::getRoleCurrentUser()=="moniteur") {
				$sql="SELECT DISTINCT idSignal, idUser, login, motif, idArt, nomArt FROM signalerArt INNER JOIN users USING (idUser) INNER JOIN article USING (idArt) INNER JOIN droitsArticleRole USING (idArt) INNER JOIN roles USING (idRole) WHERE nomRole = 'moniteur' AND lire=1";
			}
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$signalements[0]=$query->fetchAll();
			
			// commentaires signalés
			$sql="SELECT idSignal, idArt, nomArt, idPosteur, idUser, login, motif, commentaire.idCom, commentaire.contenu, dateCom FROM signalerCom INNER JOIN users USING (idUser) INNER JOIN commentaire USING (idCom) INNER JOIN concerner USING (idCom) INNER JOIN article USING (idArt) INNER JOIN posterCom ON (posterCom.idCom=commentaire.idCom)";
			
			if (Utilitaires::getRoleCurrentUser()=="moniteur") {
				$sql="SELECT idSignal, idPosteur, idUser, login, motif, commentaire.idCom, commentaire.contenu, dateCom, idArt, nomArt FROM signalerCom INNER JOIN users USING (idUser) INNER JOIN commentaire USING (idCom) INNER JOIN concerner USING (idCom) INNER JOIN droitsArticleRole USING (idArt) INNER JOIN roles USING (idRole) INNER JOIN article USING (idArt) INNER JOIN posterCom ON (posterCom.idCom=commentaire.idCom) WHERE nomRole = 'moniteur' AND lire=1";
			}
			
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			
			$signalements[1]=$query->fetchAll();
			
			return $signalements;
			
		}
		
		public function deleteSignalements() {
		
			if (Tokens::checkCSRF())
				return "Formulaire expiré.";
					
			// vérifier le type: article ou commentaire
			
			if (!isset($_POST['type']))
				return "Erreur.";
			
			switch ($_POST['type']) {
				case "comment":
				case "article":
					$type=$_POST['type'];
					break;
				default:
					return "Erreur";
			}

			
			if (!isset($_POST['idsSignalement'])||!is_array($_POST['idsSignalement']))
				return "Vous n'avez rien coché.";
			
			if (!isset($_POST['deleteAll'])&&!isset($_POST['deleteSignOnly']))
				return "Erreur";
			
			$deleteAll=(isset($_POST['deleteAll']))?1:0;
			
			$nomTable = ($type=="comment") ? "signalerCom" : "signalerArt";
			
			foreach($_POST['idsSignalement'] as $idSignal) {
				
				// l'utilisateur (un moniteur) ne peut pas supprimer des signalements qu'il n'est pas censé voir
				
				if (Utilitaires::getRoleCurrentUser()=="moniteur") {
					if ($nomTable=="signalerCom") {
						$sql="SELECT idSignal FROM signalerCom INNER JOIN concerner USING (idCom) INNER JOIN droitsArticleRole USING (idArt) INNER JOIN roles USING (idRole) WHERE idSignal = :idSignal AND nomRole = 'moniteur' AND lire=1";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idSignal", $idSignal, PDO::PARAM_INT);
						$query->execute();
						$objRes=$query->fetch(PDO::FETCH_OBJ);
						
						if (!$objRes)
							return "";
						
					}
					else {
						$sql="SELECT idSignal FROM signalerArt INNER JOIN droitsArticleRole USING (idArt) INNER JOIN roles USING (idRole) WHERE idSignal = :idSignal AND nomRole = 'moniteur' AND lire=1";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idSignal", $idSignal, PDO::PARAM_INT);
						$query->execute();
						$objRes=$query->fetch(PDO::FETCH_OBJ);
						
						if (!$objRes)
							return "";
							
					}
					
					
				}
			
				if ($deleteAll==1) {
					if ($type=="comment") {
						$sql="SELECT idCom FROM signalerCom WHERE idSignal = :idSignal";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idSignal", $idSignal, PDO::PARAM_INT);
						$query->execute();
						$objRes=$query->fetch(PDO::FETCH_OBJ);
						
						if($objRes) {
							$idComSignale=$objRes->idCom;
							$_POST['idCom']=$idComSignale;
							$this->deleteComment();	
						}
					}
					else {
						$sql="SELECT idArt FROM signalerArt WHERE idSignal = :idSignal";
						$query=parent::$bdd->prepare($sql);
						$query->bindValue(":idSignal", $idSignal, PDO::PARAM_INT);
						$query->execute();
						$objRes=$query->fetch(PDO::FETCH_OBJ);
						
						if($objRes) {
							$idArtSignale=$objRes->idArt;
							$_POST['idArt']=$idArtSignale;
							
							$this->deleteArticle();
								
						}
					}
				}
				else {
				
					$sql="DELETE FROM $nomTable WHERE idSignal = :idSignal";
					$query=parent::$bdd->prepare($sql);
					$query->bindValue(":idSignal", $idSignal, PDO::PARAM_INT);
					$query->execute();
				}
			
			}
			
			return "Opérations terminées.";
			
		}
	
	}

?>
