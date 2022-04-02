<?php

if (!defined('CONSTANTE'))
		die("Accès interdit");

	class VueAdmin extends VueGenerique {
	
		public function __construct() {
			parent::__construct();
		}
		
		function activerOK() {
			?>
				<h3 class="textesEnTete"> Compte activé avec succès </h3> </br>
				<a id='lienRetour' href='index.php?module=ModAdmin&action=listeUsers'> Retour à la liste des utilisateurs </a>
			<?php
		}
		
		function recommencer() {
			?>
				<h3 class="textesEnTete"> Le formulaire a expiré, veuillez réessayer </h3>
				<a id='lienRetour' href='index.php?module=ModAdmin&action=listeUsers'> Retour à la liste des utilisateurs </a>
			<?php
		}
		
		function afficherListeUsers($tab) {
				require_once "listeUsersMenuAdmin.php";
		}
		
		public function getOptionsHtml($liste, $typeDonnee) {
			/*
				Cette méthode sert à remplir un tableau avec des balises HTML de type <option>, en fonction d'une liste de valeurs et de son type.
				En plus de cela, elle vérifie également si cette donnée est présente dans le tableau POST. Si c'est le cas, alors elle fait en
				sorte que la valeur présente dans POST, si celle-ci fait parti des record retournés par la BD, soit en première position dans les <option>.
				Il faut que le nom de la colonne de la BD soit identique à celui de la variable de POST. On s'en sert pour les <select>
				qui affichent les rôles, les droits... ça permet de les remplir dynamiquement à partir de ce que contient la BD, et en même temps
				de faire en sorte que le choix de l'utilisateur après une validation de formulaire se retrouve en première position, c'est plus agréable.
			*/
			$valeurs = array();
			
			$optionsHtml = array();
			
			foreach ($liste as $tuple) {
				$valeurs[] = $tuple[$typeDonnee];
			}
			
			for ($i=0; $i<count($valeurs); $i++) {
				$nomBoucle = $valeurs[$i];
				$optionsHtml[] = "<option value='$nomBoucle'> $nomBoucle </option>";
			}
			
			if (isset($_POST[$typeDonnee])) {
				if (in_array($_POST[$typeDonnee], $valeurs)) {
					$valChoisi = $_POST[$typeDonnee];
					$optionsValChoisi = "<option value='$valChoisi'> $valChoisi </option>";
					for ($i=0; $i<count($optionsHtml); $i++) {
						if ($optionsHtml[$i] == $optionsValChoisi) {
							$pivot = $optionsHtml[0];
							$optionsHtml[0] = $optionsValChoisi;
							$optionsHtml[$i] = $pivot;
							break;
						}
					}
				}
			}
			
			return $optionsHtml;
		}
		
		public function afficherMenuDroitsOuLimites($listeRoles, $droitsOuLimites) {
		
			$rolesOptionsHtml = self::getOptionsHtml($listeRoles, "nomRole");
			
			$valueLogin = "";
			$valueNomRole = "";
			$radioRoleChecked = "";
			$radioUserChecked = "";
			$templateMenuDroitsOuLimites = "";			
			
			if ($droitsOuLimites=="droits") {
				$droitsDeQui = isset($_POST['droitsDeQui']) ? $_POST['droitsDeQui'] : "";
				$codeJsDisable = "<script> disableSelectRoleOuUser('$droitsDeQui'); </script>";
				$templateMenuDroitsOuLimites = "MenuDroits.php";
			}
			else {
				$limitesDeQui = isset($_POST['limitesDeQui']) ? $_POST['limitesDeQui'] : "";
				$codeJsDisable = "<script> disableSelectRoleOuUser('$limitesDeQui'); </script>";
				$templateMenuDroitsOuLimites = "MenuLimites.php";				
			}
			
				
			if (!isset($_POST['droitsDeQui'])&&!isset($_POST['limitesDeQui'])) {
				$radioRoleChecked = "checked";
				$radioUserChecked = "";
			}
			elseif (isset($_POST['droitsDeQui'])) {	
				if ($_POST['droitsDeQui']=="afficherParRole") {
					$radioRoleChecked = "checked";
				} else if ($_POST['droitsDeQui']=="afficherParUser") {
						$radioUserChecked = "checked";
						if (isset($_POST['login'])) {
							$valueLogin = htmlspecialchars($_POST['login'], ENT_QUOTES);	
						}
				}
			}
			elseif (isset($_POST['limitesDeQui'])) {
				if ($_POST['limitesDeQui']=="afficherParRole") {
					$radioRoleChecked = "checked";
				} else if ($_POST['limitesDeQui']=="afficherParUser") {
						$radioUserChecked = "checked";
						if (isset($_POST['login'])) {
							$valueLogin = htmlspecialchars($_POST['login'], ENT_QUOTES);	
						}
				}
			}
			
			
			require_once $templateMenuDroitsOuLimites;
		}
		
		public function afficherDroitsOuLimites($listeDroitsOuLimites, $listePermissionsOuLimites, $valRetourApresModif) {
			
			$nomRoleOuUser = "";
			$droitsOuExceptions = "";
			
			
			if (isset($_POST['droitsDeQui'])) {
				$roleOuUser = ($_POST['droitsDeQui']=="afficherParRole") ? "du rôle" : "de l'utilisateur";
				
				if ($_POST['droitsDeQui'] == "afficherParRole") {
					$nomRoleOuUser = $_POST['nomRole'];
					$droitsOuExceptions = "droits";
				}
				else if ($_POST['droitsDeQui'] == "afficherParUser") {
					$nomRoleOuUser = $_POST['login'];
					$droitsOuExceptions = "autorisations/interdictions exceptionnelles";
				}
				
				$listeOptionsHtml = self::getOptionsHtml($listePermissionsOuLimites, "titrePerm");				
				$templateAfficherDroitsOuLimites = "afficherDroits.php";
			}
			elseif (isset($_POST['limitesDeQui'])) {
				$roleOuUser = ($_POST['limitesDeQui']=="afficherParRole") ? "du rôle" : "de l'utilisateur";
				
				if ($_POST['limitesDeQui'] == "afficherParRole") {
					$nomRoleOuUser = $_POST['nomRole'];
				}
				else if ($_POST['limitesDeQui'] == "afficherParUser") {
					$nomRoleOuUser = $_POST['login'];
				}
				
				$listeOptionsHtml = self::getOptionsHtml($listePermissionsOuLimites, "nomLimite");
				$templateAfficherDroitsOuLimites = "afficherLimites.php";				
			}

			
			
			require_once $templateAfficherDroitsOuLimites;
		}
		
		public function menuBan($listBans, $msgRetour) {
			
			$csrfToken = Tokens::insererTokenForm();
			
			$bannis=$listBans[0];
			$banniPar=$listBans[1];
			
			require_once "menuBans.php";
		}
		
		public function interdiction() {
			require_once "interdiction.php";
		}
		
		public function gererFormules($formules,$msgRetour) {
			$csrfToken=Tokens::insererTokenForm();
			
			require_once "gererFormules.php";
		}
		
		public function annonce($annonce, $msgRetour) {
			$csrfToken=Tokens::insererTokenForm();
			
			require_once "gererAnnonce.php";
		}
		
	}
	
?>
