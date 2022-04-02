<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once "VueComposantHeaderFooter.php";
		
	class ComposantHeaderFooter {
		public $vue;
		
		public function __construct() {
			$this->vue = new VueComposantHeaderFooter();
		}
		
		public function getHeader() {
			$this->vue->getHeader();
			return $this->vue->getAffichage();
		}
		
		public function getFooter() {
			$this->vue->getFooter();
			return $this->vue->getAffichage();
		}
	}
	
?>
