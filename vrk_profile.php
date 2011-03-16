<?php

	require_once("classes/dbquery_class.php");

	$caching = true;

	if(strpos($_SERVER['HTTP_HOST'], "gleuf") !== false) {
		$caching = false;
	}

	// Hulp functies
	function writeItem($label, $text, $searchable = false, $val = 0)
	{
		$result = "";
		if($label != "")
				$label .= ":";

		if($searchable != false) {
			$result .= "<tr><td class=\"label\">$label";
			$result .= "</td><td>";
			$result .= "<a title=\"Toon alle kandidaten met dezelfde eigenschap\" href=\"javascript: filterSame('$searchable','$val')\">".$text."</a></td></tr>\n";
		}
		else {
			$result .= "<tr><td class=\"label\" width=\"40px\">".$label."</td><td>".$text."</td></tr>\n";
		}
		return $result;
	}

	function microtime_float()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

	$time_start = microtime_float();

	if(isset($_POST['id'])) {
		$profid = $_POST['id'];
	}
	else if(isset($_GET['profile'])) {
		$profid = $_GET['profile'];
	}
	else {
		die;
	}

	if(is_numeric($profid)) {
		// Count hit
		$sql = "UPDATE hits_profile SET hits=hits+1 Where id=".$profid;
		$q = new DBQuery($sql);

		$sql = "SELECT * FROM cache_profile WHERE id=".$profid;
		$q = new DBQuery($sql);

		if(!$caching) {
//			echo "<h1 style=\"color: red; text-align: center\">Caching is off</h1>";
		}

		if($q->numRows() == 1 && $caching) {
			$r = $q->fetch();
			echo $r['xhtml'];
			echo "<p class=\"profilefooter\">generated from cache</p>";
		}
		else {
			$sql = "SELECT ns_groslijst.*, ns_fracties.naam AS fractienaam FROM ns_groslijst ";
			$sql .= "LEFT JOIN ns_fracties ON (ns_groslijst.fractie_id = ns_fracties.id) ";
			$sql .= "WHERE ns_groslijst.id=".$profid;
			$q = new DBQuery($sql);
			$r = $q->fetch();

			$xhtml = "";

			// Foto en sluitknop
			if($r['nw_foto'] != '') {
				$foto = $r['nw_foto'];
			}
			else {
				$foto= "_dummy.jpg";
			}

			$xhtml .= "<div style=\"clear: both; width: 150px; float: left;\"><img style=\"border: 1px solid black\" src=\"foto/150/".$foto."\"/>\n";

//			$xhtml .= "<div onclick=\"closeProfile()\" style=\"margin: 10px 0; width: 90px\" class=\"button\">";
			//$xhtml .= "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/arr_left.png\"/>";
//			$xhtml .= "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">terug</p></div>";
			$xhtml .= "<p style=\"margin: 0px\"><a title=\"Terug\" href=\"javascript:closeProfile()\">terug</a></p>";
			

//			$xhtml .= "<div id=\"canadd\" onclick=\"addToSelection('".$r['id']."','$name','$foto')\" style=\"display: none; margin: 10px 0; width: 90px\" class=\"button\">";
//			$xhtml .= "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/checkbox_checked.png\"/>";
//			$xhtml .= "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">selecteer</p></div>";
//
//			$xhtml .= "<div id=\"candel\" onclick=\"removeFromSelection('".$r['id']."')\" style=\"display: none; margin: 10px 0; width: 90px\" class=\"button\">";
//			$xhtml .= "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/checkbox_checked.png\"/>";
//			$xhtml .= "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">verwijder</p></div>";

			$xhtml .= "<p style=\"margin: 0px\"><a title=\"Directe link naar deze kandidaat\" href=\"vrk_wiekiesjij.php?profile=".$r['id']."\">Permanente link</a></p>";

if ($r['writetothem'] == 1) {
			$xhtml .= "<p style=\"margin: 0px\"><a title=\"Mail deze kandidaat\" href=\"vrk_mailkandidaat.php?mailcode=".$r['mailcode']."\">Mail deze kandidaat</a></p>";
}
			$xhtml .= "</div>";

			$xhtml .= "<div style=\"overflow: hidden; margin: 0 5px; width: 335px; float: left; padding-left: 5px; border-left: 1px solid #AAAAAA\"><table style=\"width: 300px;\">";

			// Partij
			$xhtml .= writeItem("partij", $r['fractienaam'], "fractie_id", $r['fractie_id']);

			// Positie
			$xhtml .= writeItem("lijstpositie", $r['positie'], "positie", $r['positie']);

			// Naam

			if($r['voornaam'] != '') {
				$naam = $r['voornaam'];
			}
			else {
				$naam = $r['voorletters'];
			}
			
			if($r['tussenvoegsel']) {
			    $naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];
			$xhtml .= writeItem("naam", $naam);

			// Als er een enquete is ingevuld...

			if($r['ipadres'] != "") {

				// Leeftijd
				if($r['leeftijd'] > 0) {
					$xhtml .= writeItem("leeftijd",$r['leeftijd'], "leeftijd", $r['leeftijd']."&leeftijdop=3");
				}

				// Website
				if($r['website'] != "") {
					$xhtml .= writeItem("website", "<a href=\"http://".str_replace("http://","",$r['website'])."\">".$r['website']."</a>");
				}

				// Woonplaats
				if($r['woonplaats'] != "0") {

					$sql = "SELECT naam FROM ns_gros_gemeenten WHERE id=".$r['woonplaats'];
					$qopl = new DBQuery($sql);
					$ropl = $qopl->fetch();
					$xhtml .= writeItem("woonplaats",$ropl['naam'], "woonplaats", $r['woonplaats']);
				}

				// Opleiding
				if($r['opleiding'] != '0') {
					$sql = "SELECT ns_question_answer_link.*, ns_question_answer.* ".
					"FROM ns_question_answer_link ".
					"LEFT JOIN ns_question_answer ON (question_answer_id = id) ".
					"WHERE question_id = 42 AND value = ".$r['opleiding'];
					$qopl = new DBQuery($sql);
					$ropl = $qopl->fetch();

					$xhtml .= writeItem("opleiding",$ropl['label'], "opleiding",$r['opleiding']);
				}



				// Kranten
				if($r['krant'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 55 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "leest krant(en)";
					while($ropl = $qopl->fetch()) {
						$xhtml .= writeItem($label,$ropl['label'], "krant",$ropl['id']);
						$label = "";
					}
				}


				// Sector
				if($r['sector'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 51 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "achtergrond";
					while($ropl = $qopl->fetch()) {
						$xhtml .= writeItem($label,$ropl['label'], "sector",$ropl['id']);
						$label = "";
					}
				}

				// Expertises
				if($r['expertise'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
    							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 50 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "expertise(s)";
					while($ropl = $qopl->fetch()) {
						if ($ropl['id']==88) {
						    $ropl['label'] = "Jeugd";
						}
						if ($ropl['id']==90) {
						    $ropl['label'] = "Regionale en Europese Zaken";
						}
						$xhtml .= writeItem($label,$ropl['label'], "expertise",$ropl['id']);
						$label = "";
					}
				}

				// Fractielid sinds
				if($r['fractielidsinds'] != '0000-00-00') {
					list($jaar, $maand, $dag) = split("-", $r['fractielidsinds'], 3);
					$xhtml .= writeItem("partijlid sinds",$jaar);
				}

				// Eerdere fracties
/*				if($r['eerderefracties'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 19 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "was eerder lid van";
					while($ropl = $qopl->fetch()) {
						$xhtml .= writeItem($label,$ropl['label'], "eerderefracties",$ropl['id']);
						$label = "";
					}
				}*/


				// Ervaring
				if($r['ervaringsjaren'] != '0') {
					$xhtml .= writeItem("Ervaring in volks-<br/>vertegenwoordiging",$r['ervaringsjaren']." jaar");
				}

				// Minister?
				if($r['aanbod'] != '0') {
					if($r['aanbod'] == 1) {
						$label = "nee";
					}
					else {
						$label = "ja";
					}

					$xhtml .= writeItem("Zou minister<br/>willen worden",$label);
				}

				// Goede doelen
				if($r['doelen'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 53 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "voelt zich verwant met";
					while($ropl = $qopl->fetch()) {
						$xhtml .= writeItem($label,$ropl['label']);//, "doelen",$ropl['id']);
						$label = "";
					}
				}

				// Huisdieren
				if($r['huisdier'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 44 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "heeft als huisdier(en)";
					while($ropl = $qopl->fetch()) {
						if($ropl['label'] != "Anders") {
							$xhtml .= writeItem($label,$ropl['label']);//, "huisdier",$ropl['id']);
							$label = "";
						}
					}
				}

				// Sporten
				if($r['sporten'] != '0') {
					$sql = "SELECT * ".
							"FROM ns_question_answered ".
							"LEFT JOIN ns_question_answer ON answer_id = ns_question_answer.id ".
							"WHERE question_id = 57 AND participant_id = ".$r['id'];
					$qopl = new DBQuery($sql);

					$label = "favoriete sport(en)";
					while($ropl = $qopl->fetch()) {
						if($ropl['label'] != "Anders") {
							$xhtml .= writeItem($label,$ropl['label']);//, "sporten",$ropl['id']);
							$label = "";
						}
					}
				}

				// Voetbal club
				if($r['voetbal'] != '0') {
					$sql = "SELECT ns_question_answer_link.*, ns_question_answer.* ".
					"FROM ns_question_answer_link ".
					"LEFT JOIN ns_question_answer ON (question_answer_id = id) ".
					"WHERE question_id = 58 AND value = ".$r['voetbal'];
					$qopl = new DBQuery($sql);
					$ropl = $qopl->fetch();

					$xhtml .= writeItem("favoriete voetbalclub",$ropl['label']);//, "voetbalclub",$r['voetbal']);
					}

				if($r['4jaarbereiken'] != '') {
					$xhtml .= writeItem("In 4 jaar bereiken",$r['4jaarbereiken']);//
				}

				if($r['promo'] != '') {
					$xhtml .= writeItem("Waarom op mij stemmen?",$r['promo']);//
				}


			}else {
				$xhtml .= writeItem("helaas","Geen verdere informatie bekend.");
			}

			$xhtml .= "</table></div>";

			echo $xhtml;
			//echo  "<p class=\"profilefooter\">".$sql."</p>";

			$time_end = microtime_float();
			$time = $time_end - $time_start;

			if($caching) {
				$sql = "INSERT INTO cache_profile (id, xhtml) VALUES (".$profid.",'".addslashes($xhtml)."')";
				$q = new DBQuery($sql);
			}

			echo "<p class=\"profilefooter\">".sprintf("gegenereerd in %.3f seconden",$time)."</p>";

		}
	}
	else {
		echo "<p class=\"filterinfo\" style=\"color: red\">Does not compute.</p>";
		die();
	}

?>

<script type="text/javascript">
	var found = false;

	for(var i=0; i<selectedCandidates.length;i++) {
		if (selectedCandidates[i] == <?php echo $r['id']; ?>) {
			Element.toggle('candel');
			found = true;
		}
	}

	if(!found) Element.toggle('canadd');

</script>
