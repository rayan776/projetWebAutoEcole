<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleComposantNav.php");
	require_once("VueComposantNav.php");

	class ComposantNav {
	
		public $modele;
		public $vue;
		
		public function __construct() {
			$this->modele = new ModeleComposantNav();
			$this->vue = new VueComposantNav();
		}
		
		public function getMenu($module) {
			$nomMethod = "menu" . $module;
			
			$this->vue->menu($this->modele->$nomMethod(), "menu$module");
			return $this->vue->getAffichage();
		}

	}	
	
?>
