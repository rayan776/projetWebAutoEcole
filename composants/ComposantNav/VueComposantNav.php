<?php
	if (!defined('CONSTANTE'))
		die("Accès interdit");
		
	class VueComposantNav extends VueGenerique {
		
		public function __construct() {
			parent::__construct();
		}

		public function menu($tab, $nomMenu) {
			
			?>
		
			<div class='menu' id='<?=$nomMenu ?>'>
				<?php foreach ($tab as $lien => $nom): 
				
					if ($nom=="Messagerie") {
						if (Utilitaires::nouveauxMessages($_SESSION['login']['login'])) {
							echo "<head> <link rel='stylesheet' href='stylesheets/newMsgStyle.css'/> </head>";
						}
					}
				
				?>
					<a id="nav<?=$nom?>" href='<?=$lien ?>'> <?=$nom?> </a>
				<?php endforeach; ?>
			</div>
		
			<?php
		}
	}
?>
