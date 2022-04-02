<?php
	if  (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class VueComposantUserActu extends VueGenerique {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function userActuBox() {
			$userActu=$_SESSION['login']['login'];
			$roleUserActu=Utilitaires::getRoleCurrentUser();
			require_once "userActuBox.php";
		}
		
	}
	
?>
