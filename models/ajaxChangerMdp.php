<?php

	session_start();
	
	if (isset($_SESSION['login'])&&isset($_POST['mdpActu'])) {
	
		$bdd=new PDO("mysql:host=localhost; dbname=autoecoleduphp", "root", "");	
		
		$pwd=hash("sha256", $_POST['mdpActu']);
		$query=$bdd->prepare("SELECT password FROM users WHERE password = :password AND login = :login");
		$query->bindValue(":password", $pwd, PDO::PARAM_STR);
		$query->bindValue(":login", $_SESSION['login']['login'], PDO::PARAM_STR);
		$query->execute();
		$res=$query->fetchAll();
		
		if (count($res)==0) {
			echo "0";
		}
		else {
			echo "1";
		}
		
		exit;
		
	}



?>
