<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	require_once "ModeleArticles.php";
	require_once "VueArticles.php";

	class ContArticles {
	
		public $modele;
		public $vue;
	
		public function __construct() {
			$this->modele = new ModeleArticles();
			$this->vue = new VueArticles();
		}
		
		public function showFormWrite() {
		
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&Utilitaires::possedePermission($_SESSION['login']['login'], "ecrire article")) {
				$this->vue->formWriteNewArticle($this->modele->getCategories(),array(),0,0);
			}
		}
		
		public function editArticle() {
		
			if (!isset($_GET['idArt'])&&!isset($_POST['idArt']))
				return;
			
			if (isset($_POST['idArt']))
				$idArt=$_POST['idArt'];
			elseif (isset($_GET['idArt']))
				$idArt=$_GET['idArt'];
		
			$peutModifier=0;
			
			if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
				if (Utilitaires::getRoleCurrentUser()=="admin"||$this->modele->articleAppartientA($idArt, $_SESSION['login']['login'])) {
					$peutModifier=1;
				}
				else {
					if (Utilitaires::possedePermission($_SESSION['login']['login'], "gerer articles")) {
						$droitsArticle=$this->modele->getDroitsArticle($idArt, Utilitaires::getRoleCurrentUser());
						
						if ($droitsArticle->modifier==1)
							$peutModifier=1;
					}
						
				}
				
				if ($peutModifier==1) {
					
					$msgRetour=array();
					
					if (isset($_POST['idArt'])) {
					
						if (!$this->modele->getArticleById($_POST['idArt'])) {
							$this->vue->articleInexistant();
							return;
						}
						else {
							$msgRetour=$this->modele->insererArticle(1);
							$art=$this->modele->getArticleById($_POST['idArt']);
							$droits=$this->modele->getDroitsArticle($_POST['idArt'], "");
						}
						
						
					}
					else {
						$art=$this->modele->getArticleById($_GET['idArt']);
						$droits=$this->modele->getDroitsArticle($_GET['idArt'], "");
					}
					
					if (!$art) {
						$this->vue->articleInexistant();
						return;
					}
					
					$this->vue->formWriteNewArticle($this->modele->getCategories(),$msgRetour,$art,$droits);
				}
			}			
		}	
		
		
		public function writeArticle() {
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&Utilitaires::possedePermission($_SESSION['login']['login'], "ecrire article")) {	
				$msgRetour=$this->modele->insererArticle(0);
				$this->vue->formWriteNewArticle($this->modele->getCategories(), $msgRetour,0,0);
			}
		}
	
		
		public function listeCategories() {
		
			$msgRetour="";
		
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&Utilitaires::possedePermission($_SESSION['login']['login'], "gerer categories")) {
				if (isset($_POST['addCat'])||isset($_POST['deleteCat'])||isset($_POST['majCategories']))
					$msgRetour=$this->modele->gererCategories();
			}
			
			$peutGerer=(isset($_SESSION['login'])) ? Utilitaires::possedePermission($_SESSION['login']['login'], "gerer categories") : 0;
		
			$this->vue->voirCategories($this->modele->getListCategories(), $msgRetour, $peutGerer);
		}
		
		public function listeArticles() {
		
			$msgRetour="";
			
			if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
				if (isset($_POST['deleteArticle'])) {
					$msgRetour=$this->modele->deleteArticle();
				}
			}
		
			$this->vue->listeArticles($this->modele->getArticles(), $this->modele->getCategories(), $msgRetour);
		}
	
		public function voirArticle() {
		
		
			$peutLire=0;
			if (isset($_GET['idArt'])) {
				$articleAppartient=0;
				$msgRetourCom="";
			
				// vérification que l'article existe
				$article=$this->modele->getArticleById($_GET['idArt']);
				
				if (!$article) {
					$this->vue->articleInexistant();
					return;
				}
			
				
				if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
					
					if (isset($_POST['deleteCom'])) {
						$msgRetourCom=$this->modele->deleteComment();
					}
					elseif (isset($_POST['posterCom'])) {
						$msgRetourCom=$this->modele->insererCommentaire();
					}
				}
					
				// commentaires de l'article
				$commentaires=$this->modele->getCommentsArticle($_GET['idArt']);
			
				// vérification des droits
				
				// l'article appartient-il a l'utilisateur connecté?
				
				if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
					if ($this->modele->articleAppartientA($_GET['idArt'], $_SESSION['login']['login']))
						$articleAppartient=1;
				}
				
				if ($articleAppartient==0&&Utilitaires::getRoleCurrentUser()!="admin") {
					if (isset($_SESSION['login']['login'])&&Utilitaires::estActive()) {
						if ($this->modele->userPossedeDroitArticle($_GET['idArt'], $_SESSION['login']['login'], "lire")) {
							$peutLire=1;
						}
					}
					else {
						$tableauDroits=$this->modele->getDroitsArticle($_GET['idArt'], "visiteur");
						if ($tableauDroits->lire==1) {
							$peutLire=1;
						}
					}
				}
				else {
					$peutLire=1;
				}
			
				
				if ($peutLire==1) {
				
					if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
						$peutSupprimerCom=Utilitaires::possedePermission($_SESSION['login']['login'], "supprimer commentaires");
					}
					else
						$peutSupprimerCom=0;				
					
					if (Utilitaires::getRoleCurrentUser()=="admin"||$articleAppartient==1) {
							$peutCommenter=1;
							$peutGerer=1;
							$tableauDroits=array();
					}
					else {
						if (isset($_SESSION['login'])&&Utilitaires::estActive())
							$peutGerer=Utilitaires::possedePermission($_SESSION['login']['login'], "gerer articles");
						else
							$peutGerer=0;
							
						$tableauDroits=$this->modele->getDroitsArticle($_GET['idArt'], Utilitaires::getRoleCurrentUser());
					}
					
					$lastModif=$this->modele->getLastModif($_GET['idArt']);
					
					$this->vue->voirArticle($article, $commentaires, $peutGerer, $tableauDroits, $peutSupprimerCom, $msgRetourCom, $lastModif);
				}
				else {
					$this->vue->interditVoirArticle();
				}
			}
		}
		
		public function showSignalForm() {
			if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
			
				if (isset($_GET['idArt']) xor (isset($_GET['idCom']))) {
					if (isset($_GET['idArt'])) {
						$article=$this->modele->getArticleById($_GET['idArt']);
						
						if (!$article) {
							$this->vue->articleInexistant();
							return;
						}
						
						if ($this->modele->dejaSignale($_GET['idArt'], "article")) {
							$this->vue->dejaSignale();
							return;
						}
						
						$this->vue->signalForm($article, "article");
					}
					else {
						$comment=$this->modele->getCommentById($_GET['idCom']);
						
						if (!$comment) {
							$this->vue->articleInexistant();
							return;
						}
						
						if ($this->modele->dejaSignale($_GET['idCom'], "comment")) {
							$this->vue->dejaSignale();
							return;
						}
						
						$this->vue->signalForm($comment, "comment");
					}
				}
			
			}
		}
		
		public function transmettreSignalement() {
			if (isset($_SESSION['login'])&&Utilitaires::estActive()) {
				$this->vue->messageSignalement($this->modele->transmettreSignalement());
			}
		}
		
		public function gererSignalements() {
			if (isset($_SESSION['login'])&&Utilitaires::estActive()&&(Utilitaires::getRoleCurrentUser()=="moniteur"||Utilitaires::getRoleCurrentUser()=="admin")) {
				$msgRetour="";
				if (isset($_GET['action'])&&$_GET['action']=="supprimerSignalements") {
					$msgRetour=$this->modele->deleteSignalements();
				}
				
				$this->vue->afficherSignalements($this->modele->getSignalements(), $msgRetour);
			}
		}
		
		
	
	}

?>
