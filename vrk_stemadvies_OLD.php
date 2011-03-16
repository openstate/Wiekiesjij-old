<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_STEMHULP;

	if (isset($_REQUEST['region'])) {
		$region = (int)($_REQUEST['region']);
	}
	if (isset($_REQUEST['sw'])) {
		$swquery = $_REQUEST['sw'];
		$a = explode("|",urldecode(stripslashes($swquery)));
		$b = explode("'",array_shift($a));
		$swid = array_shift($b);
		$suggparty = array_shift($b);
		if ($swid=="7277") { $region=10; }
		if ($swid=="7275") { $region= 4; }
		if ($swid=="7273") { $region= 2; }
		if ($swid=="7269") { $region=12; }
		if ($swid=="7271") { $region= 8; }
		$swquery = stripslashes($_REQUEST['sw']);
		include("rekenmodel.php");
		$swscore = $score;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<link href="layout/stemhulp.css" rel="styleSheet" type="text/css"/>
		<title>Stemadvies</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">

				<?php
					require_once("classes/dbquery_class.php");

					$postdata = file_get_contents('php://input');
					$postmd5  = md5($postdata);

					$sql = "SELECT * FROM hits_votehelp WHERE id='".$postmd5."'";
					$q = new DBQuery($sql);

					if($q->numRows() == 1) {
						// Count hit
						$sql = "UPDATE hits_votehelp SET hits=hits+1 WHERE id='".$postmd5."'";
						$q = new  DBQuery($sql);
					}
					else {
						// Count hit
						$sql = "INSERT INTO hits_votehelp (`id`, `hits`, `query`) VALUES ('".$postmd5."',1,'".$postdata."')";
						$q = new DBQuery($sql);
					}


					$names = array();
					$score = array();
					$frac = array();
					$frac_id = array();
					$foto = array();
					$accum = 0;

					$sql = "SELECT ns_groslijst.id, nw_foto as foto, voornaam, tussenvoegsel, voorletters, achternaam, geslacht, leeftijd, geboorteprovincie, opgroeiprovincie, woonprovincie, burgerlijkestaat, gemeenschap, geloof, opleiding, ervaringsjaren, rook, vega, koophuur, auto, voetbal, ns_fracties.naam AS fractienaam, ns_fracties.id as fractie_id, eerstekamer, spectrum FROM ns_groslijst LEFT JOIN ns_fracties ON ns_fracties.id = fractie_id WHERE provincie=".(int)($region)." AND (";
					$record = "{";
					if(count($_POST['party']) == 0) {
						echo "<p style=\"text-align: center; font-weight: bold; color: red\">U moet 1 of meerdere partijen selecteren! Ga <a href=\"vrk_stemhulp.php\">terug</a> naar de vorige pagina.</p>";
					} else if(count($_REQUEST['region']) == 0) {
						echo "<p style=\"text-align: center; font-weight: bold; color: red\">Geen regio gekozen! Ga <a href=\"vrk_stemhulp.php\">terug</a> naar de vorige pagina.</p>";
					}
					else {
						foreach($_POST['party'] as $party) {
							$sql .= "fractie_id = $party OR ";
							if (is_numeric($party) && is_numeric($_POST['party'.$party])) {
							  $record .= "$party:" . $_POST['party'.$party] . ",";
							}
						}

						$sql = substr($sql,0,-3);
						$sql .= ")";
						$q = new DBQuery($sql);
						$numres = $q->numRows();

						while($r = $q->fetch()) {
							if($r['voornaam'] != '') {
								$naam = $r['voornaam'];
							}
							else {
								$naam = $r['voorletters'];
							}

							if ($r['tussenvoegsel']) {
								$naam .= " " . $r['tussenvoegsel'];
							}
							$naam .= " ".$r['achternaam'];
							$names[$r['id']] = $naam;
							$foto[$r['id']] = $r['foto'];
							$frac[$r['id']] = $r['fractienaam'];
							$frac_id[$r['id']] = $r['fractie_id'];


							$score[$r['id']] = 0;

							if($_POST['geslacht'] != 0) {
								$accum += $_POST['weight2'];
								if($_POST['geslacht'] == $r['geslacht']) {
									$score[$r['id']] += $_POST['weight2'];
								}

								$gesl = $_POST['weight2'] . "@" . $_POST['geslacht'];
							}

							if($_POST['startage'] <= $r['leeftijd'] && $_POST['endage'] >= $r['leeftijd']) {
								$score[$r['id']] += $_POST['weight3'];
								$leeftd = $_POST['weight3']."@{".$_POST['startage'] .",". $_POST['endage']."}";
							}
							$accum += $_POST['weight3'];  // Age is ALWAYS checked, so accum outside if statement

							if($_POST['province'] != 0) {
								$accum += $_POST['weight4'];
								if($_POST['province'] == $r['geboorteprovincie'] || $_POST['province'] == $r['opgroeiprovincie'] || $_POST['province'] == $r['woonprovincie']) {
									$score[$r['id']] += $_POST['weight4'];
								}
							}

							if(count($_POST['expertise']) > 0) {
								$exprt = $_POST['weight5'] . "@{";
								$accum += ($_POST['weight5'] * 0.5);
								$sql = "SELECT COUNT(*) as hit FROM `ns_question_answered` LEFT JOIN  `ns_question_answer` ON  ns_question_answer.id = ns_question_answered.answer_id WHERE question_id = 50 AND participant_id = ".$r['id']." AND (";
								foreach($_POST['expertise'] as $exp) {
									$sql .= "value = $exp OR ";
									if (is_numeric($exp)) {
										$exprt .= $exp .",";
									}
								}
								if (substr($exprt, -1) == ",") {
									$exprt = substr_replace($exprt,"",-1);
								}
								$exprt .= "}";

								$sql = substr($sql,0,-3).")";
								$sq = new DBQuery($sql);
								$sr = $sq->fetch();
								if($sr['hit'] > 0) {
									$score[$r['id']] += $_POST['weight5'] * 0.5;
								}
							}

							if(count($_POST['sector']) > 0) {
								$sect = $_POST['weight5'] . "@{"; // voor ervaring in userdata 
								$accum += ($_POST['weight5'] * 0.5);
								$sql = "SELECT COUNT(*) as hit FROM `ns_question_answered` LEFT JOIN  `ns_question_answer` ON  ns_question_answer.id = ns_question_answered.answer_id WHERE question_id = 51 AND participant_id = ".$r['id']." AND (";
								foreach($_POST['sector'] as $exp) {			
									$sql .= "value = $exp OR ";
									if (is_numeric($exp)) {
										$sect .= $exp .",";
									}
								}
								if (substr($sect, -1) == ",") {
									$sect = substr_replace($sect,"",-1);
								}
								$sect .= "}";

								$sql = substr($sql,0,-3).")";
								$sq = new DBQuery($sql);
								$sr = $sq->fetch();
								if($sr['hit'] > 0) {
									$score[$r['id']] += $_POST['weight5'] * 0.5;
								}
							}

							if($_POST['burgerlijkestaat'] != 0) {
								$accum += $_POST['weight6'];
								$gezin = $_POST['weight6'] . "@" . $_POST['burgerlijkestaat'];
								if($_POST['burgerlijkestaat'] == $r['burgerlijkestaat']) {
									$score[$r['id']] += $_POST['weight6'];
								}
							}

							if($_POST['geloof'] != 0) {
								$accum += ($_POST['weight7'] * 0.5);
								$overt = $_POST['weight7'] . "@" . $_POST['geloof'];
								if($_POST['geloof'] == $r['geloof']) {
									$score[$r['id']] += $_POST['weight7'] * 0.5;
								}
							}

							if($_POST['gemeenschap'] != 0) {
								$accum += ($_POST['weight7'] * 0.5);
								$gel = $_POST['weight7'] . "@" . $_POST['gemeenschap'];
								if($_POST['gemeenschap'] == $r['gemeenschap']) {
									$score[$r['id']] += $_POST['weight7'] * 0.5;
								}
							}

							if($_POST['opleiding'] != 0) {
								$accum += $_POST['weight9'];
								$opl = $_POST['weight9'] . "@" . $_POST['opleiding'];
								if($_POST['opleiding'] == $r['opleiding']) {
									$score[$r['id']] += $_POST['weight9'];					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 9 ipv 8 ->aloch uitgeschakeld
								}
							}

							if($_POST['ervaringsjaren'] != 0) {
								$accum += ($_POST['weight10'] * 0.5);
								if($_POST['ervaringsjaren'] <= $r['ervaringsjaren']) {
									$score[$r['id']] += $_POST['weight10'] * 0.5;
								}
							}

							if(count($_POST['posities']) > 0) {
								$erva = $_POST['weight10'] . "@{"; // voor ervaring in userdata 
								$accum += ($_POST['weight10'] * 0.5);
								$sql = "SELECT COUNT(*) as hit FROM `ns_question_answered` LEFT JOIN  `ns_question_answer` ON  ns_question_answer.id = ns_question_answered.answer_id WHERE (question_id = 11 OR question_id = 12) AND participant_id = ".$r['id']." AND (";
								foreach($_POST['posities'] as $pos) {
									$sql .= "value = $pos OR ";
									if (is_numeric($pos)) {
										$erva .= $pos .",";
									}
								}

								if (substr($erva, -1) == ",") {
									$erva = substr_replace($erva,"",-1);
								}
								$erva .= "}";

								$sql = substr($sql,0,-3).")";
								$sq = new DBQuery($sql);
								$sr = $sq->fetch();
								if($sr['hit'] > 0) {
									$score[$r['id']] += $_POST['weight10'] * 0.5;
								}
							}

							// REST


							if($_POST['rook'] != 0) {
								$accum += $_POST['weight11'] * 0.2;
								if($_POST['rook'] == $r['rook']) {
									$score[$r['id']] += $_POST['weight11'] * 0.2;
								}
							}

							if($_POST['vega'] != 0) {
								$accum += $_POST['weight11'] * 0.2;
								if($_POST['vega'] == $r['vega']) {
									$score[$r['id']] += $_POST['weight11'] * 0.2;
								}
							}

							if($_POST['koophuur'] != 0) {
								$accum += $_POST['weight11'] * 0.2;
								if($_POST['koophuur'] == $r['koophuur']) {
									$score[$r['id']] += $_POST['weight11'] * 0.2;
								}
							}

							if($_POST['auto'] != 0) {
								$accum += $_POST['weight11'] * 0.2;
								if($_POST['auto'] == $r['auto']) {
									$score[$r['id']] += $_POST['weight11'] * 0.2;
								}
							}

							if($_POST['voetbal'] != 0) {
								$accum += $_POST['weight11'] * 0.2;
								if($_POST['voetbal'] == $r['voetbal']) {
									$score[$r['id']] += $_POST['weight11'] * 0.2;
								}
							}

							if($_POST['eerstekamer'] != 0) {
								$accum += $_POST['weight10'];
								$eerstekamer = $_POST['weight10'] . "@" . $_POST['eerstekamer'];
								if($_POST['eerstekamer'] == $r['eerstekamer']) {
									$score[$r['id']] += $_POST['weight10'];
								}
							}

							// SPECTRUM
							if($_POST['party'.$r['fractie_id']] != 0) {
								$accum += $_POST['weight4'] * 4;
								$verschil = abs($_POST['party'.$r['fractie_id']]- $r['spectrum']);
/*								echo "<!-- $naam, $verschil = (".$_POST['party'.$r['fractie_id']]."-".$r['spectrum']."),  voor: ".$score[$r['id']] .", "; 
*/
									if ($verschil == 0) {
									$score[$r['id']] += $_POST['weight4'] * 4;
								} else if ($verschil == 1) {
									$score[$r['id']] += $_POST['weight4'] * 3;
								} else if ($verschil == 2) {
									$score[$r['id']] += $_POST['weight4'] * 2;
								} else if ($verschil == 3) {
									$score[$r['id']] += $_POST['weight4'] * 1;
								} else if ($verschil == 4) {
									$score[$r['id']] += 0;
								}
/*								echo ",na : ".$score[$r['id']] ."  -->\n";
*/
							}


							

						}


						foreach($score as $id => $val) {
							$perc = ($val/($accum/$numres) * 100);
							if($perc > 100) {
								//$perc = 100;
							}
							if (isset($swscore)) {
								$weight = $_POST['mixweight'];
								$score[$id] = ($weight*$perc)+((1.0-$weight)*$swscore[$id]);
							} else {
								$score[$id] = $perc;
							}
						}

						arsort($score, SORT_NUMERIC);

						$count = 0;
						$votelink = "";
						$fotos = "";
						
						foreach($score as $id => $val) {
							if($count < 5) {
								$count++;
//								$votelink .= $id.","..";";
								if ($foto[$id] == "") {
									$foto[$id] = "_dummy.jpg";
								}
								$fotos .= "<img style=\"float: left\" alt=\"".$names[$id]."\" src=\"foto/50/".$foto[$id]."\"/>";
								$votelink .= $id . "," . $foto[$id] . ";";
							}
							else {
								break;
							}
						}

/************************************************************
 ** Data van kiezers opslaan                               **
 ************************************************************/

					//if($_POST['eerstekamermail'] != "" && $_POST['eerstekamernaam'] != "") {
					$record .= "}"; 
					// om te beveiligen worden email en naam in BASE64 opgeslagen
					$sql2 = "INSERT INTO `ns_kiezerdata` (`naam` , `email` , `ipadres`, `region`, `partijen`, `geslacht`, `leeftijd`, `expertise`, `sector`, `gezin`, `overtuig`, `geloof`, `opleiding`, `ervaring`, `eerstekamer`)
 						VALUES ('".base64_encode($_POST['eerstekamernaam'])."','".base64_encode($_POST['eerstekamermail'])."','". getenv('REMOTE_ADDR')."', $region, '$record', '".$gesl."', '$leeftd', '$exprt', '$sect', '$gezin', '$overt', '$gel', '$opl','$erva', '$eerstekamer')";
//					echo "<!-- $sql2 -->\n";
					$foobar = new DBQuery($sql2);
					if($_POST['eerstekamermail'] != "" && $_POST['eerstekamernaam'] != "") {
						echo "<p class=\"small\">U heeft zich aangemeld voor de eerstekamer verkiezingen </p>\n";
					}
						?>
          <div style="width: 260px; margin-right: 10px; float: right;">
            <h1 style="margin-left: 0; color: red;">Laat uw Stem horen!</h1>

            <p style="margin-left: 0">Klik op de onderstaande stemknop om online uw stem te laten horen
            via ons <b>One Mobile One vote</b> systeem en voor <a href="vrk_omov_meerinfo.php">meer informatie</a>
	    <div style="margin: 10px 0; height: 66px;">
		<?php echo $fotos; ?>
	    </div>
            <div style="text-align: center"><a href="vrkst_vote.php?c=<?php echo substr($votelink,0,-1); ?>"><img src="images/stemopdeze.gif"></a></div> 
	    <h1 style="margin-top: 50px; margin-left: 0; color: red;">Zelf zoeken</h1>
	    <p style="margin-left: 0">U kunt natuurlijk ook zelf een selectie van kandidaten samenstellen
	      met behulp van <b>'Wie kies jij? Freestyle'</b>
	    <div><a href="vrk_wiekiesjij.php"><img src="images/freestyle-wit.gif" style="float: right;"></a></div>
	  </div>

          <div style="float: right; width: 262px;">
	    <h1> Uw ideale kandidaten! </h1>
<!--	    <p> Deze tien kandidaten sluiten het beste aan op de door u gegeven voorkeuren.</p>
						<h1>Uw ideale kandidaten!</h1>
						<div style="width: 260px; margin-right: 10px; float: right; border-bottom: 1px solid black">
							<p style="margin-left: 0">Deze 10 kandidaten sluiten het beste aan op de door u gegeven voorkeuren.</p>
							<h1 style="margin-left: 0; color: #ED1C24;">Laat uw Stem horen!</h1>
							<p style="margin-left: 0">Klik op de onderstaande stemknop om online uw stem te laten horen
							via ons <b>One Mobile One vote</b> systeem.
							<div style="margin: 10px 0; height: 66px;">
							<?php echo $fotos; ?>
							</div>
							<div onclick="location.href = 'http://www.primaries.nl/vrkst_vote.php?c=<?php echo substr($votelink,0,-1); ?>'" style="clear: both; margin: 10px 0 0 30px; width: 180px" class="button">
								<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
								<p class="button" style="font-weight: bold; padding: 0px 5px 0 21px; margin: 0; ">stem op deze kandidaten!</p>
							</div>
							<p style="margin-left: 0">U kunt natuurlijk ook zelf een selectie van 1 tot 5 kandidaten samenstellen
							met behulp van onze <a href="vrk_wiekiesjij.php" title="kandidatenbrowser">kandidatenbrowser</a>!</p>
						</div>
						<table style="display: block; margin-bottom: 10px; margin-left: 20px; width: 280px; border: 1px solid black; background-image: url('layout/background_graph.jpg')"> -->
						<?php
							//echo $accum/$numres;
							$count = 0;
							$size = 50;
							foreach($score as $id => $val) {
								if($count > 9) {
									break;
								}
								else {

echo "	    <div class=\"kandidaat_box\">\n";
echo "	      <div class=\"kandidaat_pasfoto\">\n";
echo "	        <a href=\"vrk_wiekiesjij.php?profile=".$id."\">";
									if($foto[$id]) {
										echo "<img  title=\"".$names[$id]."\" $css src=\"foto/$size/".$foto[$id]."\"/>";
									}
									else {
										echo "<img title=\"".$names[$id]."\" src=\"foto/$size/_dummy.jpg\" $css/>";
									}
									$count++;
echo "</a> \n";
echo "	      </div>\n";
echo "\n";
echo "	      <div class=\"kandidaat_info_top\">\n";
echo "	        <span style=\"vertical-align: top; float: left;\">".$count.".  ".$names[$id]."</span>\n";
echo "		<span style=\"vertical-align: top; float: right;\"><a href=\"vrk_wiekiesjij.php?partij=".$frac_id[$id]."\">".$frac[$id]."</a></span>\n";
echo "	      </div>\n";
echo "	      <div class=\"kandidaat_info_bottom\">\n";
echo "	        <span style=\"float: left; vertical-align: bottom\"><h2 class=\"percentage\">".floor($score[$id])."%</h2></span>\n";
echo "		<span style=\"float: right; vertical-align: bottom\"><a href=\"vrk_wiekiesjij.php?profile=".$id."\">profiel</a></span>\n";
echo "\n";
echo "	      </div> \n";
echo "	    </div>\n";

/*									//$perc = ($val/($accum/$numres) * 100);
									//if($perc > 100) {
										//$perc = 100;
									//}
									$perc = $val;
									$css = "style=\"margin: 10px 0px 10px 10px; border: 1px solid black\"";
									echo "<tr>";
									echo "<td><a href=\"vrk_wiekiesjij.php?profile=".$id."\">";
									if($foto[$id]) {
										echo "<img  title=\"".$names[$id]."\" $css src=\"foto/$size/".$foto[$id]."\"/>";
									}
									else {
										echo "<img title=\"".$names[$id]."\" src=\"foto/$size/_dummy.jpg\" $css/>";
									}

									echo "</a></td><td  style=\"vertical-align: bottom;\"><h2 class=\"percentage\">".floor($perc)."%</h2></td><td style=\"font-size: 12px; vertical-align: bottom; font-weight: bold; padding: 10px 5px;\">".$names[$id];
									echo ", ".$frac[$id]."</td>";
									echo "</tr>";

									//if($count == 5) {
										echo "</table><table style=\"display: block; margin-bottom: 10px; margin-left: 20px; width: 280px; border: 1px solid black; background-image: url('layout/background_graph.jpg')\">";
									//} 
*/
								}
							}
						}
					?>
<!--				</table> -->
				</div>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
