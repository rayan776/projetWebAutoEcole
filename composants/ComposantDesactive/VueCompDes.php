<?php
	if  (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class VueCompDes extends VueGenerique {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function messageDesac() {
			require_once "msgDesac.html";
		}
		
	}
	
?>
