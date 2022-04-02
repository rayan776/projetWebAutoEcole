<?php
	   require_once("../JBBCode/Parser.php");

	   $idArt=intval($_POST['idArt']);
	   
	   $bdd=new PDO("mysql:host=localhost; dbname=autoecoleduphp", "root", "");

	   $stmt=$bdd->prepare("SELECT contenu FROM article WHERE idArt=:idArt");
	   $stmt->bindValue(':idArt', $idArt, PDO::PARAM_INT);
	   $stmt->execute(); 
	   $count=$stmt->fetchColumn();

	   $parser=new JBBCode\Parser();
	   $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	   $parser->parse($count);
           $result=$parser->getAsHTML();
           
	   echo $result;
	   exit;

?>
