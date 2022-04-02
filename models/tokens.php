<?php

	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class Tokens extends ConnectDB {
	
		public static function generateToken() {
			$chars_hexa = "abcdef0123456789";
			
			$tokenTab = array();
			
			for ($i=0; $i < 64; $i++) {
				$tokenTab[] = $chars_hexa[rand(0,15)];
			}
			
			return implode("", $tokenTab);
		}
		
		public static function insererTokenForm() {
			$token = self::generateToken();
			
			$sql = "INSERT INTO tokens VALUES (:token, CURRENT_TIMESTAMP + INTERVAL 10 MINUTE, :sessionToken)";
			
			parent::connect();
			$query = parent::$bdd->prepare($sql);
			$query->bindValue(":token", $token, PDO::PARAM_STR);
			$query->bindValue(":sessionToken", $_SESSION['token'], PDO::PARAM_STR);
			$query->execute();
			return $token;
		}
		
		public static function verifierToken($token) {
			parent::connect();
			$query = parent::$bdd->prepare('SELECT * FROM tokens WHERE token = :token AND sessionToken = :sessionToken AND expireTime > CURRENT_TIMESTAMP');
			
			$query->bindValue(":token", $token, PDO::PARAM_STR);
			$query->bindValue(":sessionToken", $_SESSION['token'], PDO::PARAM_STR);
			
			$query->execute();
			
			$res = $query->fetchAll();
			
			return (count($res) > 0);
		}
		
		public static function checkCSRF() {
			if (isset($_POST['csrfToken'])) {
				if (!self::verifierToken($_POST['csrfToken'])) {
					return 1;
				}
			}
			else {
				return 1;
			}
			
			return 0;
		}
	}
	
?>
