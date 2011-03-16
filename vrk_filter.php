<?php

	require_once("classes/dbquery_class.php");
	require_once("def/globals.php");

	function microtime_float()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

	$caching = false;

	if(strpos($_SERVER['HTTP_HOST'], "gleuf") !== false) {
		$caching = false;
	}

	$postdata = file_get_contents('php://input');
	$postmd5  = md5($postdata);

	$sizes[0] = "width: 33%; ";
	$sizes[1] = "width: 25%; ";
	$sizes[2] = "width: 14%; ";
	$sizes[3] = "width: 9%; ";

	//$padding[0] = "top: 22px; left: 17px;";
	//$padding[1] = "top: 21px; left: 19px;";
	//$padding[2] = "top: 20px; left: 13px;";
	//$padding[3] = "top: 21px; left: 11px;";

	$padding[0] = "top: 213px; left: 7px;";
	$padding[1] = "top: 145px; left: 10px;";
	$padding[2] = "top: 80px; left: 5px;";
	$padding[3] = "top: 47px; left: 5px;";

	$dir[0] = "foto/150";
	$dir[1] = "foto/100";
	$dir[2] = "foto/50";
	$dir[3] = "foto/25";

	$sql = "SELECT * FROM cache_query WHERE id='".$postmd5."'";
	$q = new DBQuery($sql);

/*	if(!$caching) {
		echo "<h1 style=\"color: red; text-align: center\">Caching is off</h1>";
	} */

	if($caching && $q->numRows() == 1) {
		$r = $q->fetch();
		echo $r['xhtml'];
		echo "<p class=\"profilefooter\">generated from cache</p>"; 
		// Count hit
		$sql = "UPDATE hits_query SET hits=hits+1 WHERE id='".$postmd5."'";
		$q = new DBQuery($sql);
	}
	else {
		// Count hit
		$sql = "INSERT INTO hits_query (`id`, `hits`, `query`) VALUES ('".$postmd5."',1,'".$postdata."')";
		$q = new DBQuery($sql);

		$time_start = microtime_float();

		$sql = "SELECT code, ns_groslijst.id AS num, voornaam, voorletters, tussenvoegsel, achternaam, ns_fracties.naam AS fractienaam, nw_foto as foto FROM ns_groslijst ";

		if(is_numeric($_POST['huidigepositie']) && $_POST['huidigepositie'] > -1) {
			$sql .= "INNER JOIN ns_question_answered AS huidig ON ns_groslijst.id = huidig.participant_id AND huidig.question_id = 11 AND huidig.answer_id = ".$_POST['huidigepositie']." ";
		}

		if(is_numeric($_POST['ervaring']) && $_POST['ervaring'] > -1) {
			$sql .= "INNER JOIN ns_question_answered AS vorig ON ns_groslijst.id = vorig.participant_id AND vorig.question_id = 12 AND vorig.answer_id = ".$_POST['ervaring']." ";
		}

		if(is_numeric($_POST['huisdier']) && $_POST['huisdier'] > -1) {
			$sql .= "INNER JOIN ns_question_answered AS huisdier ON ns_groslijst.id = huisdier.participant_id AND huisdier.question_id = 44 AND huisdier.answer_id = ".$_POST['huisdier']." ";
		}

		if(is_numeric($_POST['sector']) && $_POST['sector'] > -1) {
			$sql .= "INNER JOIN ns_question_answered AS sector ON ns_groslijst.id = sector.participant_id AND sector.question_id = 51 AND sector.answer_id = ".$_POST['sector']." ";
		}

		if(is_numeric($_POST['expertise']) && $_POST['expertise'] > -1) {
			$sql .= "INNER JOIN ns_question_answered AS expertise ON ns_groslijst.id = expertise.participant_id AND expertise.question_id = 50 AND expertise.answer_id = ".$_POST['expertise']." ";
		}

		$sql .= "LEFT JOIN ns_fracties ON(ns_fracties.id=fractie_id) ";
		$sql .= " WHERE 1 ";

	// PROVINCIE

		if (isset($HTTP_COOKIE_VARS[COOKIE_NAME])) {
			$region = $HTTP_COOKIE_VARS[COOKIE_NAME];
			$sql .= " AND provincie = $region";
		} else {
/**************************************************************************************************
 **           Als er geen regio nodig is (landelijke verkiezingen) haal dan deze regel weg       **
 **************************************************************************************************/	
			$sql .= " AND 0";
		}


		if(is_numeric($_POST['fractie_id']) && $_POST['fractie_id'] != -1) {
			$sql .= " AND fractie_id = " .  $_POST['fractie_id'];
		}

		if(is_numeric($_POST['positie']) && $_POST['positie'] > 0) {
			if ($_POST['positieop'] == 1) {
				$sql .= " AND positie != 0 AND positie > ".$_POST['positie'];
			}
			else if ($_POST['positieop'] == 2) {
				$sql .= " AND positie != 0 AND positie < ".$_POST['positie'];
			}
			else {
				$sql .= " AND positie != 0 AND positie = ".$_POST['positie'];
			}
		}

		if(is_numeric($_POST['spectrum']) && $_POST['spectrum'] > 0) {
			if ($_POST['spectrumop'] == 1) {
				$sql .= " AND positie != 0 AND spectrum < ".$_POST['spectrum'];
			}
			else if ($_POST['spectrumop'] == 2) {
				$sql .= " AND spectrum != 0 AND spectrum > ".$_POST['spectrum'];
			}
			else {
				$sql .= " AND spectrum != 0 AND spectrum = ".$_POST['spectrum'];
			}
		}

		if(is_numeric($_POST['leeftijd']) && is_numeric($_POST['leeftijdop'])) {
			if ($_POST['leeftijdop'] == 1) {
				$sql .= " AND leeftijd != 0 AND leeftijd > ".$_POST['leeftijd'];
			}
			else if ($_POST['leeftijdop'] == 2) {
				$sql .= " AND leeftijd != 0 AND leeftijd < ".$_POST['leeftijd'];
			}
			else if ($_POST['leeftijdop'] == 3) {
				$sql .= " AND leeftijd != 0 AND leeftijd = ".$_POST['leeftijd'];
			}
		}

		// WONEN
		if(is_numeric($_POST['woonprovincie']) && $_POST['woonprovincie'] > -1) {
			$sql .= " AND woonprovincie = " .  $_POST['woonprovincie'];
		}

		if(is_numeric($_POST['woonplaats']) && $_POST['woonplaats'] > 0) {
			$sql .= " AND woonplaats = " .  $_POST['woonplaats'];
		}

		if(is_numeric($_POST['geslacht']) && $_POST['geslacht'] > 0) {
			$sql .= " AND geslacht = " .  $_POST['geslacht'];
		}

		if(is_numeric($_POST['opleiding']) && $_POST['opleiding'] > 0) {
			if ($_POST['opleiding'] == 5) {
				$db = "<!-- hoi -->\n";
				$sql .= " AND (opleiding = 5 OR opleiding=6)";
			} else {
				$sql .= " AND opleiding = " .  $_POST['opleiding'];
			}
		}

		// GEBOREN
		if(is_numeric($_POST['geboorteland']) && $_POST['geboorteland'] > 0) {
			$sql .= " AND geboorteland = " .  $_POST['geboorteland'];
		}

		if(is_numeric($_POST['geboorteprovincie']) && $_POST['geboorteprovincie'] > 0) {
			$sql .= " AND geboorteprovincie = " .  $_POST['geboorteprovincie'];
		}

		if(is_numeric($_POST['geboorteplaats']) && $_POST['geboorteplaats'] > 0) {
			$sql .= " AND geboorteplaats = " .  $_POST['geboorteplaats'];
		}

		// OPGROEI
		if(is_numeric($_POST['opgroeiland']) && $_POST['opgroeiland'] > 0) {
			$sql .= " AND opgroeiland = " .  $_POST['opgroeiland'];
		}

		if(is_numeric($_POST['opgroeiprovincie']) && $_POST['opgroeiprovincie'] > 0) {
			$sql .= " AND opgroeiprovincie = " .  $_POST['opgroeiprovincie'];
		}

		if(is_numeric($_POST['opgroeiplaats']) && $_POST['opgroeiplaats'] > 0) {
			$sql .= " AND opgroeiplaats = " .  $_POST['opgroeiplaats'];
		}

		if(is_numeric($_POST['rook']) && $_POST['rook'] > 0) {
			$sql .= " AND rook = " .  $_POST['rook'];
		}

		if(is_numeric($_POST['motivatie']) && $_POST['motivatie'] > 0) {
			$sql .= " AND motivatie = " .  $_POST['motivatie'];
		}

		if(is_numeric($_POST['ervaringsjaren']) && is_numeric($_POST['ervaringsjarenop'])) {
			if ($_POST['ervaringsjarenop'] == 1) {
				$sql .= " AND ervaringsjaren > ".$_POST['ervaringsjaren'];
			}
			else if ($_POST['ervaringsjarenop'] == 2) {
				$sql .= " AND ervaringsjaren < ".$_POST['ervaringsjaren'];
			}
			else if ($_POST['ervaringsjarenop'] == 3) {
				$sql .= " AND ervaringsjaren = ".$_POST['ervaringsjaren'];
			}
		}

		if(is_numeric($_POST['burgerlijkestaat']) && $_POST['burgerlijkestaat'] > 0) {
			$sql .= " AND burgerlijkestaat = " .  $_POST['burgerlijkestaat'];
		}

		if(is_numeric($_POST['kinders']) && $_POST['kinders'] == 1) {
			$sql .= " AND kinders > 1 ";
		}

		if(is_numeric($_POST['kinders']) && $_POST['kinders'] == 0) {
			$sql .= " AND kinders = 1 ";
		}

		if(is_numeric($_POST['vega']) && $_POST['vega'] > 0) {
			$sql .= " AND vega = " .  $_POST['vega'];
		}

		if(is_numeric($_POST['fractielidsinds']) && is_numeric($_POST['fractielidsindsop'])) {
			if ($_POST['fractielidsindsop'] == 1) {
				$sql .= " AND YEAR(fractielidsinds) != 0 AND (YEAR(NOW()) - YEAR(fractielidsinds)) > ".$_POST['fractielidsinds'];
			}
			else if ($_POST['fractielidsindsop'] == 2) {
				$sql .= " AND YEAR(fractielidsinds) != 0 AND (YEAR(NOW()) - YEAR(fractielidsinds)) < ".$_POST['fractielidsinds'];
			}
			else if ($_POST['fractielidsindsop'] == 3) {
				$sql .= " AND YEAR(fractielidsinds) != 0 AND (YEAR(NOW()) - YEAR(fractielidsinds)) = ".$_POST['fractielidsinds'];
			}
		}

		if(is_numeric($_POST['vega']) && $_POST['vega'] > 0) {
			$sql .= " AND vega = " .  $_POST['vega'];
		}

		if(is_numeric($_POST['jongerenorga']) && $_POST['jongerenorga'] > 0) {
			$sql .= " AND jongerenorga = " .  $_POST['jongerenorga'];
		}

		if(is_numeric($_POST['artikelen']) && $_POST['artikelen'] > 0) {
			$sql .= " AND artikelen = " .  $_POST['artikelen'];
		}

		if(is_numeric($_POST['auto']) && $_POST['auto'] > 0) {
			$sql .= " AND auto = " .  $_POST['auto'];
		}

		if(is_numeric($_POST['koophuur']) && $_POST['koophuur'] > 0) {
			$sql .= " AND koophuur = " .  $_POST['koophuur'];
		}

		if(is_numeric($_POST['geloof']) && $_POST['geloof'] > 0) {
			$sql .= " AND geloof = " .  $_POST['geloof'];
		}

		$max = 45;

		$sql .= " ORDER BY code";

		//echo $sql;

		$q = new DBQuery($sql);

		$number = $q->numRows();
		$xhtml = "";
		$script = "";
//		$xhtml .= "<!-- $sql -->\n";

		if($number == 0){
			$xhtml .= "<p class=\"filterinfo\">Geen enkel persoon voldoet aan de door u ingevulde criteria.<br/> Verwijder 1 of meer zoekcriteria en probeer opnieuw.</p>";
		}
		else if($number == 1) {
			$xhtml .= "<p class=\"filterinfo\">1 Persoon voldoet aan de ingevulde criteria.</p>";
		}
		else {
			$xhtml .= "<p class=\"filterinfo\">".$q->numRows()." Personen voldoen aan de ingevulde criteria.</p>";
		}

		if($number >= $max) {
			$xhtml .= "<p class=\"filterinfo\" style=\"color: red\">Dit zijn slechts de eerste $max kandidaten van de ".$q->numRows();
			$xhtml .= " in uw selectie.<br/>Probeer het aantal onder de $max personen te brengen.<p>";
			$size = 3;
		}
		else if($number > 24) {  // 10 x 5
			$size = 3;
		}
		else if($number > 12) { // 6x4
			$size = 2;
		}
		else if($number > 9) { // 3x3
			$size = 1;
		}
		else {
			$size = 0;
		}

		//$size = 0;

		$i = 0;
		$width = $width?$width:1;
		while($r = $q->fetch()) {
			if(($i%$width) == 0) {
				$clear = "clear: left; ";
			}
			else {
				$clear = "";
			}

			$i++;
			//limiter
			$max--;
			if($max == 0) break;

			if($r['voornaam'] != '') {
				$naam = $r['voornaam'];
			}
			else {
				$naam = $r['voorletters'];
			}

			$naam .= " ".$r['tussenvoegsel']." ".$r['achternaam'];
			//$naam = utf8_encode($naam);

			$xhtml .= "<div  id=\"pdiv".$r['num']."\" style=\"".$sizes[$size]." float: left; position: relative\">";
			$css = "margin: 20px auto 10px auto; border: 1px solid black; cursor: pointer;";
			if($r['foto'] != ''){
				$xhtml .= "<img style=\"$css\" onclick=\"getProfile(".$r['num'].")\" title=\"$naam\" alt=\"".$naam."\" id=\"can".$r['num']."\" class=\"candidate\" src=\"".$dir[$size]."/".$r['foto']."\"/>";
			}
			else {
				$xhtml .= "<img style=\"$css\" onclick=\"getProfile(".$r['num'].")\" title=\"$naam\" alt=\"".$naam."\" id=\"can".$r['num']."\" class=\"candidate\" src=\"".$dir[$size]."/_dummy.jpg\"/>";
			}

			//$xhtml .= "<img id=\"handle".$r['num']."\" src=\"voorkeurstemmer/drag.gif\" style=\"position: absolute; ".$padding[$size]." cursor: pointer;\"/>";

			//$script .= "new Draggable('can".$r['num']."',{onEnd: endDrag, onStart: startDrag, revert:true,ghosting:true});\n"; //handle: 'handle".$r['num']."',

			if($size < 2) {
				if(strlen($naam) > 22) {
					$naam = substr($naam, 0, 19) ."...";
				}

				$xhtml .= "<p class=\"name\">$naam</p>"; // id=\"".$r['code']."_handle\"
				$xhtml .= "<p class=\"fractie\">".$r['fractienaam']."</p>";
			}
			$xhtml .= "</div>";
		}

		$time_end = microtime_float();
		$time = $time_end - $time_start;
		$xhtml .= "</div>";

		$xhtml .= "<script type=\"text/javascript\">\n";
		$xhtml .= "Draggables.drags = new Array();\n";
		$xhtml .= $script;
		$xhtml .= "candidates = document.getElementsByClassName('candidate', 'grospics');\n";
		$xhtml .= "</script>\n";

		echo $xhtml;

		if($caching) {
			$sql = "INSERT INTO cache_query (id, xhtml) VALUES ('".$postmd5."','".addslashes($xhtml)."')";
			$q = new DBQuery($sql);
		}

		echo "<p class=\"profilefooter\">".sprintf("gegenereerd in %.3f seconden",$time)."</p>";
	}
?>
