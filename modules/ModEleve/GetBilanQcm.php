<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");


	class GetBilanQcm extends ConnectDB {
	
		public function __construct() {
			parent::connect();
		}
		
		public function getBilanParTentative() {
		
			$idTentative = intval($_GET['idTentative']);
			
			if (!$idTentative)
				die();
		
			$sql = "SELECT idQcm, idUser FROM userTenteQcm WHERE idTentative=$idTentative";
			
			$query = parent::$bdd->prepare($sql);
			$query->execute();
			$objRes = $query->fetch(PDO::FETCH_OBJ);
			
			if (!$objRes)
				die();
			
			$idQcm = $objRes->idQcm;
			$idUser = $objRes->idUser;
			
			$resultatsQcm = new FonctionsQcm($idQcm, $idUser);
		
			return $resultatsQcm->getResultatsQCM();
		}

	}

?>
