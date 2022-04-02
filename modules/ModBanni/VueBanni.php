<?php if(!defined('CONSTANTE'))
	die("Accès interdit");

	
	class VueBanni extends VueGenerique {
	
		public function __construct() {
			parent::__construct();
		}
		
		public function messageBan($res) {
			
			$ban=$res[0];
			$banniPar=$res[1];
			
			$dateFin="Le " . Utilitaires::remplacerDate($ban->dateFin);
			
			require_once "messageBan.php";
		}
	}
?>
