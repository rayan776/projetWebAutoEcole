<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once "VueComposantUserActu.php";
		
	class ComposantUserActu {
		public $vue;
		
		public function __construct() {
			$this->vue = new VueComposantUserActu();
		}
		
		public function getUserActuBox() {
		
			if(isset($_SESSION['login'])){
				$this->vue->userActuBox();
				return $this->vue->getAffichage();
			}
			
			return "";
		}
	}
	
?>
