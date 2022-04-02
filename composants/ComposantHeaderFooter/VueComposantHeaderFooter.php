<?php
	if  (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class VueComposantHeaderFooter extends VueGenerique {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function getHeader() {
			require_once "autoEcoleDuPhp_header.php";
		}
		
		public function getFooter() {
			require_once "autoEcoleDuPhp_footer.php";
		}
		
	}
	
?>
