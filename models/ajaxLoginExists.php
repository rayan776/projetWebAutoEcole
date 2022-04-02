<?php

	if(isset($_POST['login'])){
	   $login=$_POST['login'];
	   
	   $bdd=new PDO("mysql:host=localhost; dbname=autoecoleduphp", "root", "");

	   $stmt=$bdd->prepare("SELECT count(*) as cntUser FROM users WHERE login=:login");
	   $stmt->bindValue(':login', $login, PDO::PARAM_STR);
	   $stmt->execute(); 
	   $count=$stmt->fetchColumn();

	   $response = "Ce login est disponible.";
	   if($count > 0){
	      $response = "Ce login est déjà pris.";
	   }

	   echo $response;
	   exit;
	}


?>
