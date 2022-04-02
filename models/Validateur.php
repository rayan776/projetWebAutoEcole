<?php

	if(!defined('CONSTANTE'))
		die("Accès interdit");

	class Validateur extends ConnectDB {
	
		function __construct() {
			parent::connect();
		}
	
		function validerNomPrenomVille($chaine) {
			// un nom, un prénom ou une ville ne doivent contenir que des lettres, accents, ainsi que des tirets, apostrophes et espaces


			return preg_match('/^[\p{L} -\'-]*$/u', $chaine);
		}
		
		function verifierNumeroTelephone($phone) {
			// que des chiffres
			
			if (!self::queDesChiffres($phone))
				return 0;
			
			if (strlen($phone) == 9) {
			// autre option: 9 chiffres, le cas où le numéro est valide mais le 0 du début n'a pas été saisi. On le rajoute nous même.
				$phone = "0" . $phone;
			}
			
			// il doit y avoir 10 chiffres, avec 0 en premier chiffre, pour que ça soit valide.
			if (strlen($phone) == 10) {
				if ($phone[0] == '0') {
					return 1;
				}
			}
			
			return 0;
			
		}
		
		function queDesChiffres($chaine) {
		
			$digits=array("1","2","3","4","5","6","7","8","9","0");

			for ($i=0; $i<strlen($chaine); $i++) {
				if (!in_array($chaine[$i],$digits)) {
					return 0;
				}
			}
			
			return 1;
		}
		
		function verifierUsername() {
		
			$errors = array();
			// trim
			$login = trim($_POST['login']);
		
			// vérifie la longueur
			$longueur = strlen($login);
			
			if ($longueur < 5 || $longueur > 15) {
				$errors[] = "Le login doit faire entre 5 et 15 caractères";
			}
			
			// il ne doit pas y avoir que des chiffres
			if (self::queDesChiffres($login))
				$errors[] = "Le login ne doit pas être composé uniquement de chiffres.";
			
			// vérifie la présence de caractères spéciaux
			
			$loginLower = strtolower($login);
			
			$caracteresAdmis = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			
			for ($i=0; $i<strlen($loginLower); $i++) {
				if (!in_array($loginLower[$i], $caracteresAdmis)) {
					$errors[] = "Présence de caractères non autorisés dans le nom d'utilisateur.";
					break;
				}
			}
			
			// vérifie la présence du login dans la BD
			
			$query = parent::$bdd->prepare('SELECT login FROM users WHERE login = :login');
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			
			$query->execute();
			
			$resultats = $query->fetchAll();
			
			if (count($resultats) == 1) {
				$errors[] = "Login déjà utilisé";
			}
			
			return $errors;
			
		}
		
		function verifierPwd() {
		
			$errors = array();
			
			$newMdp = isset($_POST['password']) ? $_POST['password'] : "";
			
			if (empty($newMdp)) {
				$errors[] = "Vous n'avez pas saisi de nouveau mot de passe";
				return $errors;
			}
			
			// trim
			$newMdp = trim($newMdp);
			
			// test longueur : 8 caractères minimum
			if (strlen($newMdp) < 8)
				$errors[] = "Votre nouveau mot de passe doit faire au moins 8 caractères.";
				
			$chiffres = 0;
			$maj = 0;
			$min = 0;
			$special = 0;
			$specialChars = array("\"", "&", "\\", "/", "!", "?", "_", "-", "=", "+", "*", ",", ";", ":", "§", ".", "<", ">", "'", "^", "~", "@", "`", "#", ")", "(", "]", "[", "{", "}", "¨", "$", "£", "µ", "°", "%");

			$digits=array("1","2","3","4","5","6","7","8","9","0");
			$uppers=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			
			$lowers=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
			
			for ($i=0; $i<strlen($newMdp); $i++) {
				if (in_array($newMdp[$i], $digits))
					$chiffres++;
				elseif (in_array($newMdp[$i], $uppers))
					$maj++;
				elseif (in_array($newMdp[$i], $lowers))
					$min++;
				elseif (in_array($newMdp[$i], $specialChars))
					$special++;
			}
			
			// au moins 3 chiffres
			if ($chiffres < 3)
				$errors[] = "Votre nouveau mot de passe doit contenir au moins 3 chiffres";
			
			// au moins 1 minuscule et 1 majuscule
			if ($maj < 1) {
				$errors[] = "Votre nouveau mot de passe doit contenir au moins 1 majuscule";
			}
			
			if ($min < 1) {
				$errors[] = "Votre nouveau mot de passe doit contenir au moins 1 minuscule";
			}
			
			// au moins 1 caractère spécial
			if ($special < 1)
				$errors[] = "Votre nouveau mot de passe doit contenir au moins 1 caractère spécial";
				
			return $errors;
		}
		
		function verifierInfosPerso($inscription) {
			$errors = array();
			
			$infos = array();
			
			$nom=isset($_POST['nom']) ? trim($_POST['nom']) : "";
			$infos['nom']=$nom;
			
			$prenom=isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
			$infos['prénom']=$prenom;
			
			$ville=isset($_POST['ville']) ? trim($_POST['ville']) : "";
			$infos['ville']=$ville;
			
			$cp=isset($_POST['cp']) ? Utilitaires::virerEspaces($_POST['cp']) : "";
			$infos['code postal']=$cp;
			
			$phone=isset($_POST['phone']) ? Utilitaires::virerEspaces($_POST['phone']) : "";
			$infos['numéro de téléphone']=$phone;
			
			// vérification que rien n'est vide
			foreach ($infos as $champ => $val) {
				if (empty($val)) {
					$errors[]="Vous devez saisir votre $champ";
				}
			}
			
			// vérification du nom
			if (!empty($nom)&&!self::validerNomPrenomVille($nom))
				$errors[]="Votre nom ne doit contenir que des lettres, des tirets, des apostrophes ou des espaces.";
			
			// prénom
			if (!empty($prenom)&&!self::validerNomPrenomVille($prenom))
				$errors[]="Votre prénom ne doit contenir que des lettres, des tirets, des apostrophes ou des espaces.";
			
			// ville
			if (!empty($ville)&&!self::validerNomPrenomVille($ville))
				$errors[]="Un nom de ville ne doit contenir que des lettres, des tirets, des apostrophes ou des espaces.";
			
			// code postal
			if(!empty($cp)&&(!self::queDesChiffres($cp)||(strlen($cp)!= 5)))
				$errors[]="Un code postal est composé de 5 chiffres.";
			
			// numéro de téléphone
			if (!empty($phone) && !self::verifierNumeroTelephone($phone))
				$errors[]="Un numéro de téléphone doit être composé de 5 paires de chiffres et commencer par un zéro.";
			
			
			// vérifications autour du type	
			if ($inscription) {
				$type=isset($_POST['type']) ? trim(strtolower($_POST['type'])) : "";
			
				if (empty($type)) {
					$errors[]="Vous devez indiquer votre rôle au sein de l'auto-école (élève/moniteur)";
				}
				else {
					if ($type != "eleve" && $type != "moniteur") {
						$errors[]="Rôle invalide";
					}
					else {
						if ($type == "eleve") {
							$neph=isset($_POST['neph']) ? Utilitaires::virerEspaces($_POST['neph']) : "";

							if (empty($neph))
								$errors[] = "Vous devez saisir votre numéro NEPH.";
							else {
								if (self::queDesChiffres($neph) && strlen($neph) == 12) {
									// vérifier que NEPH n'existe pas déjà
			
									$sql="SELECT NEPH FROM infosPerso WHERE NEPH = :neph";
									$query=parent::$bdd->prepare($sql);
									$query->bindValue(':neph', $neph, PDO::PARAM_STR);
									$query->execute();
									
									$objRes=$query->fetch(PDO::FETCH_OBJ);
									
									if ($objRes)
										$errors[]="Le numéro NEPH que vous avez saisi est déjà utilisé, il est censé être unique. Veuillez contacter l'administration.";
									$infos['neph']=$neph;
								}
								else {
									$errors[]="Numéro NEPH invalide. Il s'agit normalement d'une série de 12 chiffres.";
								}
							}
						
						}
					}
				}
			}
			
			return $errors;
			
		}
	
	}

?>
