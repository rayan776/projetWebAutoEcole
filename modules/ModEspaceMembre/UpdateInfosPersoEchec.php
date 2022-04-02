<?php
    if (!defined('CONSTANTE'))
        die("Accès interdit");

?>
    <h3 class="textesEnTete"> Désolé, vos informations personnelles n'ont pas pu être mises à jour pour les raisons suivantes: </h3>

    <div class="erreur">
        <ul>
<?php for ($i=0; $i<count($tab); $i++): ?>
    <li> <?=$tab[$i]?> </li>
<?php endfor; ?>
        </ul>
    </div>

    <a id="retourEchecUpdateInfosPerso" href="index.php?module=ModEspaceMembre&action=afficherInfosPerso"> Retour aux informations personnelles </a>
