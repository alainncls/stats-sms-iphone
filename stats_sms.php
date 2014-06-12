<?php date_default_timezone_set('Europe/Paris');

// Page d'affichage du catalogue suite à la requête de l'utilisateur

// *************************************************
// DEFINITION DES VARIABLES POUR LA GESTION DE LA BDD
// *************************************************

$mode=$_GET["mode"];

$bdd= "csv_db"; // Nom de la base de données
$host= "localhost"; // Nom de l'hôte de la BDD
$user= "root";
$tablesms="sms_2014";
$tableliens="handle";
$tablecontacts="contacts";

$premierjanvier2014_i=378691200 + 86400 * 365;
$premierjanvier2001=978307200;
$date_inf = $premierjanvier2014_i;
$date_sup = $premierjanvier2014_i + 86400;
$date_limite = time() - $premierjanvier2001;

$count_envoyes=0;
$count_recus=0;
$count_envoyes_roxanne=0;
$count_recus_roxanne=0;
$count_envoyes_jessica=0;
$count_recus_jessica=0;

$total_recus=0;
$total_envoyes=0;
$total_envoyes_roxanne=0;
$total_recus_roxanne=0;
$total_envoyes_jessica=0;
$total_recus_jessica=0;

$format_long="d F Y H:i:s";
$format_attendu="d F Y";

// ******************
// CONNEXION A LA BDD
// ******************

	// Connexion à la BDD en utilisant les identifiants. Si erreur : affichage message
	@mysql_connect($host, $user) or die("Impossible de se connecter à base de données");

	// Sélection de la BDD
	@mysql_select_db($bdd);

	// Affichage d'une éventuelle erreur dans la BDD (pas de table, etc.)
	if (mysql_error()){
		print "Erreur dans la base de données : ".mysql_error();
		exit();
	}

// ************
// SMS PAR JOUR
// ************

	if ($mode === 'jour'){
		echo "<body bgcolor=#EEEEE text=\"#000000\">"; // Définition de la couleur de fond de la page
			echo "<table border=2 cellpadding=5>"; // Définition d'un tableau				
				echo "<tr>
							<td><b>Date</b>
							<td><b>Envoyés</b>
							<td><b>Reçus</b>
							<td><b>E à R</b>
							<td><b>R de R</b>
							<td><b>E à J</b>
							<td><b>R de J</b>";
				while ($date_sup <= $date_limite){
				
					$query_total = "SELECT * FROM $tablesms WHERE date <= $date_sup AND date >= $date_inf";
					$liste_total = mysql_query($query_total);
					
					if (!$liste_total) {
						die('Requête invalide : ' . mysql_error());
					}
						
					while ($total = mysql_fetch_row($liste_total)){
						if ($total[3] == 1){
							$count_envoyes = $count_envoyes + 1;
							if ($total[1] == 143 || $total[1] == 82){
								$count_envoyes_roxanne = $count_envoyes_roxanne + 1;
							}
							else if ($total[1] == 171 || $total[1] == 297){
								$count_envoyes_jessica = $count_envoyes_jessica + 1;
							}
						}
						else if ($total[3] == 0){
							$count_recus = $count_recus + 1;
							if ($total[1] == 143 || $total[1] == 82){
								$count_recus_roxanne = $count_recus_roxanne + 1;
							}
							else if ($total[1] == 171 || $total[1] == 297){
								$count_recus_jessica = $count_recus_jessica + 1;
							}
						}
					}
						
					$date_brute=$date_inf + $premierjanvier2001;
					$date=date($format_attendu, $date_brute);
					echo "<tr>
							<td>$date
							<td>$count_envoyes
							<td>$count_recus
							<td>$count_envoyes_roxanne
							<td>$count_recus_roxanne
							<td>$count_envoyes_jessica
							<td>$count_recus_jessica";
					$date_sup = $date_sup + 86400;
					$date_inf = $date_inf + 86400;
					
					$total_recus=$total_recus+$count_recus;
					$total_envoyes=$total_envoyes+$count_envoyes;
					$total_envoyes_roxanne=$total_envoyes_roxanne+$count_envoyes_roxanne;
					$total_recus_roxanne=$total_recus_roxanne+$count_recus_roxanne;
					$total_envoyes_jessica=$total_envoyes_jessica+$count_envoyes_jessica;
					$total_recus_jessica=$total_recus_jessica+$count_recus_jessica;
					
					$count_total = 0;
					$count_envoyes=0;
					$count_recus=0;
					$count_envoyes_roxanne=0;
					$count_recus_roxanne=0;
					$count_envoyes_jessica=0;
					$count_recus_jessica=0;
				}
				echo "<tr>
						<td><b>TOTAL</b>
						<td><b>$total_envoyes</b>
						<td><b>$total_recus</b>
						<td><b>$total_envoyes_roxanne</b>
						<td><b>$total_recus_roxanne</b>
						<td><b>$total_envoyes_jessica</b>
						<td><b>$total_recus_jessica</b>
					</table>
					</body>";
	}

// *************
// SMS PAR HEURE
// *************
	
	else if ($mode === 'heure'){
		$i = 0;
		$j = 0;
		$k = 0;
		$l = 1;
		
		$duree = 24;
	
		while ($i < $duree){
			$i = $i + 1;
			${'count_echanges_'.$i} = 0;
			${'count_envoyes_'.$i} = 0;
			${'count_recus_'.$i} = 0;
		}
		
		while ($k <= $duree){
			${'h_'.$k} = ($premierjanvier2014_i + 3600 * $k);
			$k = $k + 1;
		}
		
		$h_courante = $h_1;
		$h_precedente = $h_0;
		
		while ($l <= $duree){
			echo $l;
			while ($h_precedente<=$date_limite){
				$query_echanges = "SELECT * FROM $tablesms WHERE date <= $h_courante AND date >= $h_precedente";
				$liste_echanges= mysql_query($query_echanges);
				
				while ($echanges = mysql_fetch_row($liste_echanges)){
					${'count_echanges_'.$l} = ${'count_echanges_'.$l} + 1;
				}
				
				$h_courante = $h_courante + 86400;
				$h_precedente = $h_precedente + 86400;
				set_time_limit(100);
			}
			$l = $l + 1;
			if ($l < $duree+1){$h_courante = ${'h_'.$l};}
			$l = $l - 1;
			$h_precedente = ${'h_'.$l};
			$l = $l + 1;
		}
		
		echo "<body bgcolor=#EEEEE text=\"#000000\">";
			echo "<table border=2 cellpadding=5>";				
				echo "<tr>
					<td><b>Heures</b>
					<td><b>Echangés</b>";
				
				while ($j < $duree){
					$bas = $j;
					$j = $j + 1;
					echo"<tr>
						<td>Entre $bas h et $j h
						<td>${'count_echanges_'.$j}";
				}
			echo "</table>";
		echo "</body>";
	}

// ****************
// SMS PAR PERSONNE
// ****************

	else if ($mode === 'personne'){
	$i = 0;
		$count_pers = 0;
		//$tableau = Array ();
		
		$query_num = "SELECT * FROM $tableliens";
		$liste_num = mysql_query($query_num);
		$query_noms = "SELECT * FROM $tablecontacts";
		
		while ($num = mysql_fetch_row($liste_num)){
			$liste_noms = mysql_query($query_noms);
			while ($noms = mysql_fetch_row($liste_noms)){
				$recherche = substr($num[1], 2);
				if (strpos($noms[2], $recherche) != false){
					$query_echanges = "SELECT * FROM $tablesms WHERE handle_id = $num[0]";
					$liste_echanges= mysql_query($query_echanges);
					
					while ($echanges = mysql_fetch_row($liste_echanges)){
						$count_pers = $count_pers + 1;
					}
					
					if ($count_pers != 0){
						//echo"$num[0] - $noms[0] $noms[1] = $count_pers<br>";
						$tableau[$i] = array (
						"Total" => "$count_pers",
						"Prenom" => "$noms[0]",
						"Nom" => "$noms[1]");
						$i = $i+1;
					}
				}
				$count_pers = 0;
			}
		}
		
		arsort ($tableau, $count_pers);
		
		echo $tableau[2];
		
		/*$j = 0;
		for($j = 0; $j < $i; $j++){
			echo $tableau[$j] . '<br />';
		}*/
		
		
		/*foreach($tableau as $cle => $element){
			echo '['.$cle.'] vaut ' .$element . '<br />';
		}*/
	
		echo'<pre>';
		print_r($tableau);
		echo'</pre>';
	}
	
?>