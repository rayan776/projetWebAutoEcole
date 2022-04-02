<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");

	class ConnectDB {
		
		public static $dns = "mysql:host=localhost; dbname=autoecoleduphp";
		public static $user = "root";
		public static $password = "";
		
		protected static $bdd;
		
		protected static function connect() {			
			ConnectDB::$bdd = new PDO(self::$dns, self::$user, self::$password);	
		}
	
	}

?>
