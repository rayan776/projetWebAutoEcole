<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class ModeleBanni extends ConnectDB {
		
		public function __construct() {
			parent::connect();
		}
		
		public function getBan() {
			$sql="SELECT idBan, login, motif, dateFin FROM estBanni INNER JOIN bannir USING (idBan) INNER JOIN users USING (idUser) WHERE login = :login";
			$query=parent::$bdd->prepare($sql);
			$query->bindValue(":login", $_SESSION['login']['login'], PDO::PARAM_STR);
			$query->execute();
			
			$objRes1=$query->fetch(PDO::FETCH_OBJ);
			
			$idBan=$objRes1->idBan;
			
			$sql="SELECT login FROM banniPar INNER JOIN users USING (idUser) WHERE idBan = $idBan";
			$query=parent::$bdd->prepare($sql);
			$query->execute();
			$objRes2=$query->fetch(PDO::FETCH_OBJ);
			
			$res=array();
			
			$res[0]=$objRes1;
			$res[1]=$objRes2->login;
			
			return $res;
		}
		
	}
