<?php if(!defined('CONSTANTE'))
	die("Accès interdit");
		
	class VueArticles extends VueGenerique {

		public function __construct() {
			parent::__construct();
		}
		
		public function formWriteNewArticle($categories, $msgRetour, $art, $droits) {
		
			$edit=is_array($droits)?1:0;
		
			$valSubmit=($edit==0)?"Valider le nouvel article":"Mettre à jour l'article";

			$msgEnTete=($edit==0)?"Rédigez un nouvel article":"Mettez à jour l'article";
			
			$action=($edit==0)?"index.php?module=ModArticles&action=writeArticle":"index.php?module=ModArticles&action=editArticle";
			
			$csrfToken=Tokens::insererTokenForm();
			
			if ($edit==0) {
				$titreArt=isset($_POST['titreArt'])?$_POST['titreArt']:"";
				$catArticle=isset($_POST['catArticle'])?$_POST['catArticle']:1;
				$contenu="";
			
				if (isset($_POST['contenuArticle'])) {
					$parser=new JBBCode\Parser();
					$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
					$parser->parse($_POST['contenuArticle']);
					$contenu=htmlspecialchars($parser->getAsHtml());
				}
			}
			else {
				$titreArt=$art->nomArt;
				$catArticle=$art->idCat;
				$contenu=$art->contenu;
				$idArt=$art->idArt;
				
				$parser=new JBBCode\Parser();
				$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
				$parser->parse($contenu);
				$contenu=$parser->getAsHtml();
			}
		
			
			require_once "formWriteNewArticle.php";
		}
	
		
		public function voirCategories($categories, $msgRetour, $peutGerer) {
			
			$readonly = ($peutGerer) ? "" : "readonly='readonly'";
			require_once "categories.php";
		}
		
		public function listeArticles($liste, $categories, $msgRetour) {
		
			$checkedDate=isset($_POST['trierPar'])&&in_array("date",$_POST['trierPar']) ? "checked" : "";
			$checkedAut=isset($_POST['trierPar'])&&in_array("aut",$_POST['trierPar']) ? "checked" : "";
			$checkedTitre=isset($_POST['trierPar'])&&in_array("titre",$_POST['trierPar']) ? "checked" : "";
			$checkedContenu=isset($_POST['trierPar'])&&in_array("cont",$_POST['trierPar']) ? "checked" : "";
			$checkedCat=isset($_POST['trierPar'])&&in_array("cat",$_POST['trierPar']) ? "checked" : "";
			
			$dateDeb=isset($_POST['dateDeb'])?htmlspecialchars($_POST['dateDeb'],ENT_QUOTES):"";
			$dateFin=isset($_POST['dateFin'])?htmlspecialchars($_POST['dateFin'],ENT_QUOTES):"";
			$trierParAut=isset($_POST['trierParAut'])?htmlspecialchars($_POST['trierParAut'],ENT_QUOTES):"";
			$trierParTitre=isset($_POST['titreArt'])?htmlspecialchars($_POST['titreArt'],ENT_QUOTES):"";
			$contenuArt=isset($_POST['contenuArt'])?htmlspecialchars($_POST['contenuArt'],ENT_QUOTES):"";
			$category=isset($_POST['trierParCat'])?$_POST['trierParCat']:1;	
			$ordreDate=isset($_POST['ordreDate'])?$_POST['ordreDate']:"DESC";				
			
			require_once "recherche.php";
		}
		
		public function voirArticle($article, $commentaires, $peutGerer, $tableauDroits, $peutSupprimerCom, $msgRetourCom, $lastModif) {
		
			$csrfToken=Tokens::insererTokenForm();
		
			$parser=new JBBCode\Parser();
			$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
			$parser->parse($article->contenu);
			
			$corpsArticle=$parser->getAsHtml();
			
			if (!is_array($tableauDroits)) {
				$peutCommenter=$tableauDroits->commenter;
				$peutModifier=$tableauDroits->modifier;
				
				if (!isset($_SESSION['login'])||!Utilitaires::estActive()||!Utilitaires::possedePermission($_SESSION['login']['login'], "poster commentaires"))
					$peutCommenter=0;
					
				$peutSupprimer=$tableauDroits->supprimer;

			}
			else {
				$peutCommenter=1;
				$peutSupprimer=1;
				$peutModifier=1;
			}
			
			$fantome=0;
			
			if (Utilitaires::getIdUser($article->login)==Utilitaires::getIdFantome()) {
				$fantome=1;
			}
			
			require_once "voirArticle.php";
			
		}
		
		public function interditVoirArticle() {
			require_once "interdiction.php";
		}
		
		public function articleInexistant() {
			require_once "inexistant.php";
		}
		
		public function formSignalement() {
			require_once "formSignalement.php";
		}
	
		public function signalForm($tuple, $type) {
		
			$csrfToken=Tokens::insererTokenForm();
			
			$fantome=0;
			
			if ($type=="comment") {
				if (Utilitaires::roleCommentaire($tuple->idCom)=="supprime")
					$fantome=1;
			}
			
		
			$msgEnTete=($type=="comment") ? "Signalez un commentaire" : "Signalez un article";
				
			require_once "signalForm.php";
		}
		
		public function dejaSignale() {
			require_once "dejaSignale.php";
		}
		
		public function messageSignalement($msg) {
			require_once "msgSignalement.php";
		}
		
		public function afficherSignalements($signalements, $msgRetour) {
			
			$csrfToken = Tokens::insererTokenForm();
			
			require_once "gererSignalements.php";
		}
		
	}

?>
