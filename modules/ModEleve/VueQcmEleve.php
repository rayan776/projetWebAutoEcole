<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
	
	class VueQcmEleve {
	
		public function __construct() {
		
		}
		
		public function showQuestion($tabVue) {
			
			$csrfToken=Tokens::insererTokenForm();
			
			$reponses = $tabVue[1];
			$question = $tabVue[2];
			$idTentative = $tabVue[3];
			$tabDejaRep = $tabVue[4];
			$tempsRestant = $tabVue[5];
			
			if ($tempsRestant >= 0)
				$msgRestant = "Il est trop tard pour répondre à cette question.";
			else if ($tempsRestant < 0)
				$tempsRestant *= -1;
			
			if (count($tabDejaRep)>0) {
				$msgRestant = "Vous avez déjà répondu à cette question.";
			}
			
			$nomQcm = $question->nomQcm;
			$numeroQuestion = isset($_POST['numeroQuestion']) ? intval($_POST['numeroQuestion']) : 0;
		
			$onclick = (count($tabDejaRep)>0) ? "onclick='this.checked=!this.checked'" : "";
			
			require_once "templateQuestion.php";
		}
		
		public function showResultats($tabVue) {
		
		
			$nbQuestionsEtNote = $tabVue[1];
			
			
			$questionsReponses = $tabVue[2][0];
			$choixUser = $tabVue[2][1];
			
			$reponsesUser = array();
			
			foreach ($choixUser as $tuple) {
			
				$nomIndex = "question" . $tuple['idQuestion'];
				
				if (!isset($reponsesUser[$nomIndex])) {
					$reponsesUser[$nomIndex] = array();
					$reponsesUser[$nomIndex][] = $tuple['idReponse'];
				}
				else
					$reponsesUser[$nomIndex][] = $tuple['idReponse'];
			}
			
			$nomQcm = $tabVue[3]->nomQcm;
			$pourcentageReussite = Utilitaires::arrondirPourcentage($tabVue[3]->pourcentageReussite);
			
			$nbQuestionsQcm = Utilitaires::getNbQuestionsParIdQcm($nbQuestionsEtNote->idQcm);
			
			require_once "bilanQcm.php";
		
			
		}
		
	}

?>


				
					
					
					
				
					
						
