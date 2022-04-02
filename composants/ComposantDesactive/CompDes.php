<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once "VueCompDes.php";
		
	class CompDes {
		public $vue;
		
		public function __construct() {
			$this->vue = new VueCompDes();
		}
	
		public function messageDesac() {
			$this->vue->messageDesac();
			return $this->vue->getAffichage();
		}
	}
	
?>
