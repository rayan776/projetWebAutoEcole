<?php

header ('Content-type: text/html; charset=utf-8');

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class VueAccueil extends VueGenerique {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function afficherErrors($errors) {
			$nbErreurs = count($errors);
				?>
					<h3 class="textesEnTete"> <?=$nbErreurs ?> erreur(s) </h3>
					<div class='erreur'>
						<ul>
				<?php
			for ($i=0; $i<$nbErreurs; $i++):
				?> 
					<li> <?=$errors[$i] ?> </li>
			<?php endfor; ?>
					</ul>
				</div>
			<?php
		}
		
		public function afficherError($error) {
			?> <p class='erreur'> <?= $error ?> </br> </p>
			
			<?php
		}
		
		public function formulaireConnexion() {
			$login = "";
			$task = "connexion";
			$tache = "Connectez";
			$valSubmit = "Se connecter";
			$csrfToken = Tokens::insererTokenForm();
			
			require_once "templateFormConnexion.php";
		}
	
		public function formulaireInscription() {
		
			$login = "";
			$task = "inscription";
			$tache = "Inscrivez";
			$valSubmit = "S'inscrire";
			$csrfToken = Tokens::insererTokenForm();
	
			if (isset($_POST['login']))
				$login = htmlspecialchars($_POST['login'], ENT_QUOTES);
			
			$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom'], ENT_QUOTES) : "";
			$prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom'], ENT_QUOTES) : "";
			$ville = isset($_POST['ville']) ? htmlspecialchars($_POST['ville'], ENT_QUOTES) : "";
			$cp = isset($_POST['cp']) ? htmlspecialchars($_POST['cp'], ENT_QUOTES) : "";
			$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone'], ENT_QUOTES) : "";
			$neph = isset($_POST['neph']) ? htmlspecialchars($_POST['neph'], ENT_QUOTES) : "";
			
			$eleveCheck = "checked";
			$nephDisabled = "";
			$moniteurCheck = "";
			
			if (isset($_POST['type'])) {
				if ($_POST['type'] == "moniteur") {
					$eleveCheck = "";
					$moniteurCheck = "checked";
					$nephDisabled = "disabled";
				}
			}
			
			require_once "templateFormInscription.php";
		}
		
		public function connexionReussie() {
			$login = $_SESSION['login']['login'];
			require_once "connexionReussie.php";
		}
		
		public function inscriptionReussie() {
			require_once "inscriptionReussie.html";
		}
		
		public function bienvenue($formules, $lastArticles, $annonce) {
			require_once "bienvenue.php";
		}
		
	}

?>
