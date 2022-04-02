<?php
	session_start();
	
	if (isset($_POST['login'])&&isset($_SESSION['login'])&&isset($_POST['table'])&&isset($_POST['inputName'])) {
		
		if ($_POST['table']!="recevoir"&&$_POST['table']!="envoyer")
			return;
		
		$inputName=htmlspecialchars($_POST['inputName']);
			
		$bdd=new PDO("mysql:host=localhost; dbname=autoecoleduphp", "root", "");
		
		$login=$_SESSION['login']['login'];
				
		$sql="SELECT idUser FROM users WHERE login = :login";
		$query=$bdd->prepare($sql);
		$query->bindValue(":login", $login, PDO::PARAM_STR);
		$query->execute();
		
		$idUser=$query->fetchColumn();
		
		$sql="";
		
		if ($_POST['table']=="recevoir") {
			$sql="SELECT login FROM recevoir INNER JOIN users ON (recevoir.idExp=users.idUser) INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE recevoir.idDest = $idUser AND login LIKE :login AND nomRole <> 'robot' AND nomRole <> 'supprime'";
		}
		else {
			$sql="SELECT login FROM envoyer INNER JOIN users ON (envoyer.idDest=users.idUser) INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE envoyer.idExp = $idUser AND login LIKE :login AND nomRole <> 'robot' AND nomRole <> 'supprime'";
		}
		
		$login=$_POST['login'];
		$query=$bdd->prepare($sql);
		$query->bindValue(":login", "%$login%", PDO::PARAM_STR);
		$query->execute();
		
		$res=$query->fetchAll();
		
		foreach ($res as $tuple) {
			$log=htmlspecialchars($tuple['login']);
			?> <div class='suggestionsDiv' onclick="changeInputLogin('<?=$inputName?>','<?=$log?>');"> <?=$tuple['login']?> </div> <?php
		}
		
		if (count($res)==0)
			echo "Aucune suggestion";
		
		exit;
	}

?>
