<?php
	if(!defined('CONSTANTE'))
		die("Accès interdit");
		
	require_once("ModeleBanni.php");
	require_once("VueBanni.php");
	
	class ContBanni {
		public $modele;
		public $vue;
		
		function __construct() {
			$this->modele = new ModeleBanni();
			$this->vue = new VueBanni();
		}
		
		public function messageBan() {
			$this->vue->messageBan($this->modele->getBan());
		}
		
	}
?>
