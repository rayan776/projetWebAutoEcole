<?php
	if(!defined('CONSTANTE'))
		die("Accès interdit");

	require_once("ContBanni.php");

	class ModBanni {
	
		public $controleur;
	
		function __construct() {
			if (!isset($_SESSION['login'])) {
				die("Accès interdit");
			}
			else {
				if (!Utilitaires::estBanni($_SESSION['login']['login'])) {
					die ("Accès interdit");
				}
	
			}
		
			$this->controleur = new ContBanni();
		
			$this->controleur->messageBan();
			
		}
	
	}
	
?>
