<?php
	session_start();
	
	if (isset($_SESSION['login'])&&isset($_POST['login'])&&isset($_POST['inputName'])&&isset($_POST['chercherEleve'])) {
	
	 	$bdd=new PDO("mysql:host=localhost; dbname=autoecoleduphp", "root", "");

		$login=htmlspecialchars($_POST['login']);
		$inputName=$_POST['inputName'];
		
		if ($_POST['chercherEleve']=="moniteur") {
		
			$sql="SELECT login, prenom, nom FROM users INNER JOIN estMoniteur USING (idUser) INNER JOIN correspondre USING (idUser) INNER JOIN infosPerso USING (idInfo) WHERE login LIKE :login";
		
			$query=$bdd->prepare($sql);
			$query->bindValue(":login", "%$login%", PDO::PARAM_STR);
			$query->execute();
			
			$res=$query->fetchAll();
			
			if (count($res)==0) {
				echo "<span style='color:white'> Aucun moniteur trouvé. </span>";
				exit;
			}
			
			$i=-1;
			
			?>
			<table>
				<tr class="tableTitres">
					<td class="tdAlternate2"> Login </td>
					<td class="tdAlternate2"> Prenom </td>
					<td class="tdAlternate2"> Nom </td>
				</tr>
			<?php
			 
			foreach ($res as $tuple) {

				$i++;
				$tdClass=($i%2==0)?"tdAlternate1":"tdAlternate2";
				?>
				<tr>
					
					<td class="<?=$tdClass?>">
						<a style="padding:0;margin:0" href="index.php?module=ModEspaceMembre&action=voirProfil&login=<?=$tuple['login']?>"> <?=$tuple['login']?> </a>
					</td>
					
					<td class="<?=$tdClass?>">
						<?=$tuple['prenom']?>
					</td>
					
					<td class="<?=$tdClass?>">
						<?=$tuple['nom']?>
					</td>
				</tr>
			
			<?php 
			}
			echo "</table>";
			exit;
		}
		
			
		

		$sql="SELECT login FROM users INNER JOIN detientRole USING (idUser) INNER JOIN roles USING (idRole) WHERE login LIKE :login AND nomRole <> 'robot' AND nomRole <> 'supprime'";


		if ($_POST['chercherEleve']=="yes") {
				$sql="SELECT login FROM users INNER JOIN estEleve USING (idUser) WHERE login LIKE :login";
		}
		
		
		$query=$bdd->prepare($sql);
		$query->bindValue(":login", "%$login%", PDO::PARAM_STR);
		$query->execute();
		$result=$query->fetchAll();

		if (count($result)==0) {
			echo "Aucun login trouvé.";
		}
		else {
			
			foreach ($result as $tuple) {
				$log=htmlspecialchars($tuple['login']);
				?> <div class='suggestionsDiv' onclick="changeInputLogin('<?=$inputName?>','<?=$log?>');"> <?=$tuple['login']?> </div> <?php
			}
		}

		exit;
	}
	else {
		echo "offline";
		exit;
	}
?>
