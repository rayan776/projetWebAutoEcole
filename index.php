<?php
	define('CONSTANTE', NULL);

	session_start();
	require_once("models/connectDB.php");
	require_once("models/tokens.php");
	require_once("vues/VueGenerique.php");
	require_once("models/Validateur.php");
	require_once("models/Utilitaires.php");
	require_once("models/Limites.php");
	require_once("JBBCode/Parser.php");
	require_once("composants/ComposantNav/ComposantNav.php");
	require_once("composants/ComposantDesactive/CompDes.php");
	require_once("composants/ComposantHeaderFooter/ComposantHeaderFooter.php");
	require_once "composants/ComposantUserActu/ComposantUserActu.php";	
	
	$titrePage = "Auto-école du PHP";
	
	if (!array_key_exists('token', $_SESSION)) // distribution d'un token de session, si il n'y en a pas déjà un
		$_SESSION['token']=Tokens::GenerateToken();
	
	if (isset($_GET['module'])) {
		$module=$_GET['module'];
		
		switch ($module) {
			case "ModAccueil":
				break;
			case "ModAdmin":
				$titrePage = "Menu administrateur";
				break;
			case "ModEleve":
				$titrePage = "Espace élève";
				break;
			case "ModMoniteur":
				$titrePage = "Espace moniteur";
				break;
			case "ModMessagerie":
				$titrePage = "Messagerie";
				break;
			case "ModEspaceMembre":
				$titrePage = "Espace membre";
				break;
			case "ModArticles":
				$titrePage = "Articles";
				break;
			default:
				die("Accès interdit");
		}
	}
	else {
		$module = "ModAccueil";
		$titrePage = "Auto-école du PHP";
	}
	
	if (isset($_SESSION['login']['login'])&&Utilitaires::estBanni($_SESSION['login']['login'])) {
		
		if ($module!="ModEspaceMembre") {
			
			if (!isset($_GET['action'])) {
				$module = "ModBanni";
				$titrePage = "Auto-école du PHP";
			}
			else {
				if ($_GET['action']!="deconnexion") {
					$module="ModBanni";
					$titrePage = "Auto-école du PHP";
				}
			}
		}
	}
	
	if ($module != "ModAccueil" && $module != "ModArticles") {
		if (!isset($_SESSION['login'])) {
			$module = "ModAccueil";
			$titrePage = "Auto-école du PHP";
		}
	}

	$urlMod="modules/" . $module . "/" . $module . ".php";
	
	require_once($urlMod);
	$mod=new $module();
	
	$affichage=$mod->controleur->vue->getAffichage();
	
	$compNav = new ComposantNav();
	
	$liensMenuPrincipal = $compNav->getMenu("ModAccueil");
	
	if ($module != "ModAccueil" && $module != "ModBanni") {
		$compNav2 = new ComposantNav();
		$liensMenuSecondaire = $compNav2->getMenu($module);
	}
	
	$compDesac = new CompDes();
	$msgDesac = isset($_SESSION['login'])&&!Utilitaires::estActive() ? $compDesac->messageDesac() : "";
	
	$compHeader = new ComposantHeaderFooter();
	$header = $compHeader->getHeader();
	$compFooter = new ComposantHeaderFooter();
	$footer = $compFooter->getFooter();
	
	$compActu="";
	
	if (isset($_SESSION['login'])) {
		$compUserActu = new ComposantUserActu();
		$compActu=$compUserActu->getUserActuBox();
	}

	$templateSecondaire = "templates/template" . $module . ".php";
	
	require_once "templates/templatePrincipal.php";
	
?>
