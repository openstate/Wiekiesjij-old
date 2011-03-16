<?php
	// elitversion = 0058, elitdate  2007-02-08 18:13
	require_once "def/globals.php";
	require_once("classes/dbquery_class.php");
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_STEMHULP;
	$partijen = array();
	$vanSW = false;
	$sw_score = array();

	if(isset($_GET['sw'])) {
		// klant komt van stemwijzer!
		include_once("rekenmodel.php");
		setcookie(COOKIE_NAME, $prov);
		$region = $prov;
		$sw_score = $score;
		$vanSW = true;
	}

	if(isset($_GET['profile']) && is_numeric($_GET['profile'])) {
		header( 'Location: http://www.wiekiesjij.nl/vrk_wiekiesjij.php?profile=' . $_GET['profile'] ) ;
	}

	$j = 10; // number of pages

	if (isset($_REQUEST['region'])) {
		if ($_REQUEST['region'] == -1) {
 			// cookie moet verwijdert worden
			setcookie(COOKIE_NAME, $region, time() - 1000);
		} else {
 			$region = (int)($_REQUEST['region']);
			setcookie(COOKIE_NAME, $region);
		}
	}

	if (!isset($region) && isset($HTTP_COOKIE_VARS[COOKIE_NAME]) && $_REQUEST['region'] != -1 && !isset($prov)) {
		$region = (int)($HTTP_COOKIE_VARS[COOKIE_NAME]);
	}

	function writeWeightBox($count)
	{
		echo "<div class=\"belang\">";
		echo "<span class=\"onbelangrijk\">onbelangrijk</span> ";
		echo "<input type=\"radio\" value=\"0.25\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"0.5\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"1\" name=\"weight$count\" checked=\"checked\"/> ";
		echo "<input type=\"radio\" value=\"2\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"4\" name=\"weight$count\"/> ";
		echo "<span class=\"belangrijk\">belangrijk</span>";
		echo "</div>";
	}

	function writePartijSpectr($id) {
		$partijnaam['1'] = "de VVD";
		$partijnaam['2'] = "de PvdA";
		$partijnaam['3'] = "het CDA";
		$partijnaam['4'] = "de SP";
		$partijnaam['5'] = "GroenLinks";
		$partijnaam['6'] = "D66";
		$partijnaam['7'] = "de Lijst Pim Fortuyn";
		$partijnaam['8'] = "de ChristenUnie";
		$partijnaam['9'] = "de SGP";
		$partijnaam['10'] = "EenNL";
		$partijnaam['11'] = "de Partij voor de Vrijheid";
		$partijnaam['12'] = "de Partij voor de Dieren";
		$partijnaam['13'] = "Gelderland Lokaal";
		$partijnaam['14'] = "Groen Hop";
		$partijnaam['16'] = "Mooi Utrecht";
		$partijnaam['17'] = "de Nederlandse Klokkenluiders Partij";
		$partijnaam['18'] = "de NVU";
		$partijnaam['19'] = "ONS Flevoland";
		$partijnaam['20'] = "de Ouderenpartij NH/VSP";
		$partijnaam['21'] = "Noord-Holland Anders/ De Groene";
		$partijnaam['22'] = "de ChristenUnie/SGP";
		$partijnaam['23'] = "Eén voor de Vrijheid";
		$partijnaam['24'] = "Leefbaar Zuid-Holland";
		$partijnaam['25'] = "Lokaal Zuid-Holland";
		$partijnaam['29'] = "Nieuw Rechts";

		echo "					<div class=\"votequestion\" id=\"spect_$id\" style=\"display: none;\">\n";
		echo "							<h2>Waar in het politieke spectrum van de partij ".$partijnaam[$id]." moet uw kandidaat zich bevinden?</h2>\n";
		echo "							<div class=\"questionbox\">\n";
		echo "							<span class=\"onbelangrijk\">Links</span>\n";
		echo "							<input type=\"radio\" value=\"1\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"2\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"3\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"4\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"5\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"6\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"7\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"8\" name=\"party$id\"/>\n";
		echo "							<input type=\"radio\" value=\"9\" name=\"party$id\"/>\n";
		echo "							<span class=\"belangrijk\">Rechts</span>\n";
		echo "							</div>\n";
		echo "						</div>\n";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<script src="javascript/stemhulp.js" type="text/javascript"></script>
		<title>Stemhulp</title>
	</head>

	<body id="base">

		<div id="wrap">

			<?php include "layout/page_header.php"; ?>

 			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">

    		    	    <form action="vrk_stemadvies.php?region=<?php echo $region; if (isset($_REQUEST['sw'])) { echo "&sw=".$_REQUEST['sw']; } ?>" id="stemhulpform" method="post">
<?php
$scr1 = "block";
$back1 = "0";

 if ($vanSW) { ?>
					<div id="page-1" class="votehelppage" style="display: block">
						<p  style="padding: 10px; border: 1px solid #ED1C24">Welkom van www.stemwijzer.nl. Hieronder staat welke 
						kandidaten het meeste overeenkomt met uw antwoorden op de stellingen bij www.stemwijzer.nl</p>

<div style="float: left;">
<?php
$scr1 = "none";
$back1 = "-1";

$sqlq = "SELECT ns_groslijst.id, nw_foto as foto, voornaam, tussenvoegsel, voorletters, achternaam, ns_fracties.naam AS fractienaam, ns_fracties.id as fractie_id FROM ns_groslijst LEFT JOIN ns_fracties ON ns_fracties.id = fractie_id WHERE 0";
$count=0;
foreach ($sw_score as $id=>$sc) {
	if ($count > 4) {
		break;
	} else {
		$sqlq .= " OR ns_groslijst.id=$id";
		$count++;
	}
}
$count=0;
//echo $sqlq;
$rq = new DBQuery($sqlq);
while ($resq = $rq->fetch()) {
	$foto = $resq['foto'];
	if($resq['voornaam'] != '') {
		$naam = $resq['voornaam'];
	} else {
		$naam = $resq['voorletters'];
	}
	if ($resq['tussenvoegsel']) {
		$naam .= " " . $resq['tussenvoegsel'];
	}
	$naam .= " ".$resq['achternaam'];

	echo "	    <div class=\"kandidaat_box\">\n";
	echo "	      <div class=\"kandidaat_pasfoto\">\n";
	echo "	        <a href=\"vrk_wiekiesjij.php?profile=".$resq['id']	."\">";
	if($foto) {
		echo "<img  title=\"".$name."\" $css src=\"foto/50/".$foto."\"/>";
	} else {
		echo "<img title=\"".$name."\" src=\"foto/50/_dummy.jpg\" $css/>";
	}
	$count++;
	echo "</a> \n";
	echo "	      </div>\n";
	echo "\n";
	echo "	      <div class=\"kandidaat_info_top\">\n";
	echo "	        <span style=\"vertical-align: top; float: left;\">".$count.".  ".$naam."</span>\n";
	echo "		<span style=\"vertical-align: top; float: right;\"><a href=\"vrk_wiekiesjij.php?partij=".$resq['fractie_id']."\">".$resq['fractienaam']."</a></span>\n";
	echo "	      </div>\n";
	echo "	      <div class=\"kandidaat_info_bottom\">\n";
	echo "	        <span style=\"float: left; vertical-align: bottom\"><h2 class=\"percentage\">".floor($sw_score[$resq['id']])."%</h2></span>\n";
	echo "		<span style=\"float: right; vertical-align: bottom\"><a href=\"vrk_wiekiesjij.php?profile=".$resq['id']."\">profiel</a></span>\n";
	echo "\n";
	echo "	      </div> \n";
	echo "	    </div>\n";
}
?>				
	</div>		
					<div style="float: right; width: 250px;">
Als u ook een analyse wilt van de persoonlijke eigenschappen van de kandidaten, klik dank <a href="javascript:nextPage(-1,1, false)" ><img src="images/start-groen.gif" onclick="nextPage(-1,1,false)" alt="" /></a>
					</div>


					</div>

<?php } ?> 


<?php if (isset($region)) { ?>
					<div id="page0" class="votehelppage" style="display: <?php echo $scr1; ?>">

						<h1>De Voorkeurstemhulp</h1>
						<img src="images/poppetje.gif" alt="stemhulp" style="margin: 0 20px; float: right"/>
						<p>Stichting <b>Het Nieuwe Stemmen</b> heeft voor u de <b>Voorkeurstemhulp</b> ontwikkeld. Met deze stemhulp
						kunt u, nadat u (ongeveer) heeft bepaald welke partij uw stem zal krijgen, eenvoudig bepalen
						wie van de kandidaten het best bij u past.</p>

						<p>De stemhulp bestaat uit <?php echo $j; ?> pagina's met
						één of meerdere korte vragen. U begint met het selecteren
						van de politieke partij(en) die wij in onze overweging mee moeten nemen. Bij ieder onderdeel
						kunt u bovendien aangeven hoe <span class="important">belangrijk</span> of <span class="unimportant">onbelangrijk</span> uw voorkeur moet meewegen in onze aanbeveling aan het eind.</p>

						<p>Klik op start om naar de voorkeursstemwijzer te gaan</p>
						
						<div style="width:100%; text-align: center"><a href="javascript:nextPage(0,1, false)" ><img src="images/start-groen.gif" onclick="nextPage(0,1,false)" alt="" /></a></div>
<?php
						$q = "SELECT naam FROM `ns_gros_provincies` WHERE id=$region";	
						$r = new DBQuery($q);
						$res=$r->fetch()
?>
						<p class="small">Momenteel heeft u de provincie <?php echo $res['naam']; ?> geselecteerd, als dat niet klopt, klik dan <a href="vrk_stemhulp.php?region=-1">hier</a>. </p>

					</div>
<?php } else { ?>
					<div id="page0" class="votehelppage" style="display: block">

						<p>Heeft u vragen over het SP!TS artikel, klik dan <a href="spits.php">hier</a>.</p>
						<h1>Kies uw regio</h1>
						<p>Wat is de provincie waarin u uw stem mag uitbrengen?</p>

						<div style="text-align:center"><img src="images/kaart.gif" width="410" height="470" border="0" alt="" usemap="#kaart_Map">
						<map name="kaart_Map">
						<area shape="poly" alt="Zuid-Holland" coords="120,205, 132,206, 127,220, 130,226, 154,222, 157,226, 160,233, 155,237, 153,258, 164,270, 185,261, 191,265, 188,274, 180,277, 174,276, 174,282, 174,293, 188,303, 174,308, 129,308, 101,303, 81,288, 71,275" href="?region=12" target="_self">
						<area shape="poly" alt="Gelderland" coords="271,172, 279,178, 286,172, 294,182, 297,196, 291,206, 305,220, 325,215, 353,233, 363,241, 359,252, 373,263, 359,277, 353,274, 328,287, 308,282, 295,285, 286,284, 279,289, 279,298, 254,300, 238,287, 219,291, 206,302, 188,303, 188,297, 181,295, 174,287, 175,279, 188,278, 194,266,
						200,264, 210,267, 220,263, 225,262, 242,269, 243,261, 237,251, 233,236, 219,221, 223,215, 233,215, 241,201, 257,189" href="?region=4" target="_self">
						<area shape="poly" alt="Flevoland" coords="244,115, 262,123, 276,132, 279,149, 262,158, 266,172, 258,184, 238,196, 234,197, 230,211, 216,215, 203,203, 190,200, 188,192, 199,180, 192,174, 193,161, 221,159, 239,151, 236,125" href="?region=2" target="_self">
						<area shape="poly" alt="Noord-Holland" coords="160,55, 170,73, 161,91, 164,103, 182,99, 210,140, 193,156, 184,171, 181,198, 203,210, 195,226, 174,209, 150,220, 138,221, 130,219, 135,203, 123,201, 136,145, 84,145, 81,127, 139,126, 148,95, 142,87, 154,60" href="?region=8" target="_self">
						<area shape="poly" alt="Utrecht" coords="172,212, 156,220, 161,236, 156,254, 163,266, 168,266, 184,257, 194,258, 194,262, 221,262, 225,262, 234,262, 239,266, 243,262, 216,217, 204,212, 194,226, 188,227" href="?region=10" >
						</map></div>

					</div>
<?php } ?>

					<div id="page1" class="votehelppage" style="display: none;">
					       <div class="box">
					      	  <div class="dummy"><h1> Partijen </h1></div>
						  <div class="voortgang">[1 / 10]</div>
						</div>
	
						<h2>Welke partij(en) maken kans op uw stem?</h2>
						<div class="votequestion" id="parties">

							<?php 
								$pagenr = 1;

								$q = "SELECT * FROM ns_fraclink INNER JOIN ns_fracties ON ns_fraclink.fractie=ns_fracties.id WHERE ns_fraclink.provincie=".(int)$region.";";	
								$r = new DBQuery($q);
								$cnt = 0;
								while ($res=$r->fetch()) {
									array_push($partijen, $res['id']);
									$cnt++;
									echo "<div class=\"party\" onclick=\"toggle_spect('party".$cnt."','spect_".$res['id']."');\">\n";
									echo "<img alt=\"".$res['naam']."\" src=\"voorkeurstemmer/_".$res['abbr'].".png\"/>\n";
									echo "<input type=\"checkbox\" value=\"".$res['id']."\" name=\"party[]\" id=\"party".$cnt."\"/>\n";
									echo "<label for=\"party".$cnt."\" >".$res['html']."</label>\n";
									echo "</div>\n";
								}
								echo "<script>numcheckies=$cnt;</script>";
								$i = $pagenr;
			
							?>
						</div>

					    	<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".$back1; ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
					    	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>,true)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div> <!-- einde page partijen -->

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">
        <div class="box">
      	  <div class="dummy"><h1> Geslacht </h1></div>
	  <div class="voortgang">[2 / 10]</div>
	</div>
						<?php writeWeightBox(2); ?>
						<div class="votequestion">
							<h2>Gaat uw voorkeur uit naar een mannelijke of vrouwelijke kandidaat?</h2>
							<div class="questionbox">
								<label for="man">man</label><input id="man" value="1" type="radio" name="geslacht"/>
								<label for="vrouw">vrouw</label><input id="vrouw" value="2" type="radio" name="geslacht"/>
								<label for="oges">maakt niet uit</label><input id="oges" value="0" type="radio" name="geslacht" checked="checked"/> 
							</div>
						</div>
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div> <!-- einde page geslacht -->

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--						<h1 class="votehelptitle">Leeftijd</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Leeftijd </h1></div>
	  <div class="voortgang">[3 / 10]</div>
	</div>
						<?php writeWeightBox(3); ?>
						<div class="votequestion">
							<h2>In welke leeftijdsgroep valt uw ideale kandidaat?</h2>
							<div class="questionbox">
								<?php for($k=19;$k<88;$k++) $ages .= "<option value=\"$k\">$k</option>"; ?>
								<p style="margin: 0 10px">Tussen de
									<select id="startage" name="startage" onchange="checkages()"><option value="18" selected="selected">18</option><?php echo $ages; ?></select> en
									<select id="endage" name="endage" onchange="checkages()"><?php echo $ages; ?><option value="88" selected="selected">88</option></select> jaar oud.
								</p>
							</div>
						</div>
<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--					<h1 class="votehelptitle">Politieke spectrum</h1>-->
        <div class="box">
      	  <div class="dummy"><h1> Politieke spectrum </h1></div>
	  <div class="voortgang">[4 / 10]</div>
	</div>
<?php
writeWeightBox(4);
foreach ($partijen as $partyID) {
  writePartijSpectr($partyID);
}
?>

<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--						<h1 class="votehelptitle">Expertise</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Expertise </h1></div>
	  <div class="voortgang">[5 / 10]</div>
	</div>
						<?php writeWeightBox(5); ?>
						<div class="votequestion">
							<h2>Mijn kandidaat heeft expertise op de volgende beleidsterreinen:</h2>
							<div class="questionbox" style="padding-left: 20px">
<!--								<input type="checkbox" class="checkbox" id="expertise1" value="1" name="expertise[]"/><label for="expertise1">Antilliaanse en Arubaanse Zaken</label><br/>
								<input type="checkbox" class="checkbox" id="expertise2" value="2" name="expertise[]"/><label for="expertise2">Bestuurlijke Vernieuwing</label><br/>
								<input type="checkbox" class="checkbox" id="expertise3" value="3" name="expertise[]"/><label for="expertise3">Buitenlandse en Europese Zaken</label><br/>
								<input type="checkbox" class="checkbox" id="expertise4" value="4" name="expertise[]"/><label for="expertise4">Cultuur</label><br/>
								<input type="checkbox" class="checkbox" id="expertise5" value="5" name="expertise[]"/><label for="expertise5">Defensie</label><br/>
								<input type="checkbox" class="checkbox" id="expertise6" value="6" name="expertise[]"/><label for="expertise6">Economische Zaken</label><br/>
								<input type="checkbox" class="checkbox" id="expertise7" value="7" name="expertise[]"/><label for="expertise7">Energie &amp; Technologie</label><br/>
								<input type="checkbox" class="checkbox" id="expertise8" value="8" name="expertise[]"/><label for="expertise8">Financien</label><br/>
								<input type="checkbox" class="checkbox" id="expertise9" value="9" name="expertise[]"/><label for="expertise9">Integratiebeleid</label><br/>
								<input type="checkbox" class="checkbox" id="expertise10" value="10" name="expertise[]"/><label for="expertise10">Justitie</label><br/>
								<input type="checkbox" class="checkbox" id="expertise11" value="11" name="expertise[]"/><label for="expertise11">Landbouw &amp; Voedselkwaliteit</label><br/>
								<input type="checkbox" class="checkbox" id="expertise12" value="12" name="expertise[]"/><label for="expertise12">Natuur &amp; Milieu</label><br/>
								<input type="checkbox" class="checkbox" id="expertise13" value="13" name="expertise[]"/><label for="expertise13">Onderwijs</label><br/>
								<input type="checkbox" class="checkbox" id="expertise14" value="14" name="expertise[]"/><label for="expertise14">Ontwikkelingssamenwerking</label><br/>
								<input type="checkbox" class="checkbox" id="expertise15" value="15" name="expertise[]"/><label for="expertise15">Verkeer &amp; Waterstaat</label><br/>
								<input type="checkbox" class="checkbox" id="expertise16" value="16" name="expertise[]"/><label for="expertise16">Wetenschap</label><br/>
								<input type="checkbox" class="checkbox" id="expertise17" value="17" name="expertise[]"/><label for="expertise17">Zorg &amp; Welzijn</label>-->
<input class="checkbox" id="expertise2" value="2" name="expertise[]" type="checkbox"><label for="expertise2">Bestuurlijke Vernieuwing</label><br/>
<input class="checkbox" id="expertise19" value="19" name="expertise[]" type="checkbox"><label for="expertise19">Bouw en Ruimtelijke Ordening</label><br/>
<input class="checkbox" id="expertise4" value="4" name="expertise[]" type="checkbox"><label for="expertise4">Cultuur</label><br/>
<input class="checkbox" id="expertise5" value="5" name="expertise[]" type="checkbox"><label for="expertise5">Defensie</label><br/>
<input class="checkbox" id="expertise6" value="6" name="expertise[]" type="checkbox"><label for="expertise6">Economische Zaken</label><br/>
<input class="checkbox" id="expertise7" value="7" name="expertise[]" type="checkbox"><label for="expertise7">Energie &amp; Technologie</label><br/>
<input class="checkbox" id="expertise8" value="8" name="expertise[]" type="checkbox"><label for="expertise8">Financien</label><br/>
<input class="checkbox" id="expertise9" value="9" name="expertise[]" type="checkbox"><label for="expertise9">Integratiebeleid</label><br/>
<input class="checkbox" id="expertise1" value="1" name="expertise[]" type="checkbox"><label for="expertise1">Jeugd</label><br/>
<input class="checkbox" id="expertise10" value="10" name="expertise[]" type="checkbox"><label for="expertise10">Justitie</label><br/>
<input class="checkbox" id="expertise11" value="11" name="expertise[]" type="checkbox"><label for="expertise11">Landbouw &amp; Voedselkwaliteit</label><br/>
<input class="checkbox" id="expertise12" value="12" name="expertise[]" type="checkbox"><label for="expertise12">Natuur &amp; Milieu</label><br/>
<input class="checkbox" id="expertise13" value="13" name="expertise[]" type="checkbox"><label for="expertise13">Onderwijs</label><br/>
<input class="checkbox" id="expertise14" value="14" name="expertise[]" type="checkbox"><label for="expertise14">Ontwikkelingssamenwerking</label><br/>
<input class="checkbox" id="expertise3" value="3" name="expertise[]" type="checkbox"><label for="expertise3">Regionale en Europese Zaken</label><br/>
<input class="checkbox" id="expertise18" value="18" name="expertise[]" type="checkbox"><label for="expertise18">Sociale Zekerheid</label><br/>
<input class="checkbox" id="expertise15" value="15" name="expertise[]" type="checkbox"><label for="expertise15">Verkeer &amp; Waterstaat</label><br/>
<input class="checkbox" id="expertise16" value="16" name="expertise[]" type="checkbox"><label for="expertise16">Wetenschap</label><br/>
<input class="checkbox" id="expertise17" value="17" name="expertise[]" type="checkbox"><label for="expertise17">Zorg &amp; Welzijn</label><br/>
							</div>

							<h2>Mijn kandidaat heeft ervaring binnen de volgende sector:</h2>

							<div class="questionbox" style="padding-left: 20px">
								<input type="checkbox" class="checkbox" id="sector2" value="2" name="sector[]"/><label for="sector2">De landbouw</label><br/>
								<input type="checkbox" class="checkbox" id="sector3" value="3" name="sector[]"/><label for="sector3">De media</label><br/>
								<input type="checkbox" class="checkbox" id="sector5" value="5" name="sector[]"/><label for="sector5">De overheid</label><br/>
								<input type="checkbox" class="checkbox" id="sector7" value="7" name="sector[]"/><label for="sector7">De zorg</label><br/>
								<input type="checkbox" class="checkbox" id="sector1" value="1" name="sector[]"/><label for="sector1">Het bedrijfsleven</label><br/>
								<input type="checkbox" class="checkbox" id="sector6" value="6" name="sector[]"/><label for="sector6">Het ondernemerschap</label><br/>
								<input type="checkbox" class="checkbox" id="sector4" value="4" name="sector[]"/><label for="sector4">Het onderwijs</label>
							</div>

						</div>
<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--					<h1 class="votehelptitle">Gezinssituatie</h1>-->
        <div class="box">
      	  <div class="dummy"><h1> Gezinssituatie </h1></div>
	  <div class="voortgang">[6 / 10]</div>
	</div>
						<?php writeWeightBox(6); ?>
						<div class="votequestion">
							<h2>Wat is de burgerlijke staat van uw kandidaat?</h2>
							<div class="questionbox">
								<p style="margin: 0 10px"><select name="burgerlijkestaat" id="burgerlijkestaat"><option value="0">Geen voorkeur</option><option value="1">Alleenstaand</option><option value="2">Relatie, niet samenwonend</option><option value="3">Samenwonend</option><option value="4">Getrouwd / Geregistreerd partnerschap</option></select></p>
							</div>
						</div>
<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--						<h1 class="votehelptitle">Levensovertuiging</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Levensovertuiging </h1></div>
	  <div class="voortgang">[7 / 10]</div>
	</div>
						<?php writeWeightBox(7); ?>
						<div class="votequestion">
							<h2>Mijn kandidaat heeft de volgende levensovertuiging:</h2>
							<div class="questionbox">
								<p style="margin: 0 10px"><select name="geloof" id="geloof"><option value="0">Geen voorkeur</option><option  value="1">Aanhanger van een religie</option><option  value="2">Er is wel iets, maar ik weet niet wat</option><option  value="3">Agnost</option><option  value="4">Atheïst</option></select></p>
							</div>

							<h2>Mijn kandidaat behoort tot de volgende geloofsgemeenschap:</h2>
							<div class="questionbox">
								<p style="margin: 0 10px"><select name="gemeenschap" id="gemeenschap"><option value="0">Geen voorkeur</option><option  value="1">Geen</option><option  value="2">Anders</option><option  value="3">Rooms-Katholiek kerk</option><option  value="4">Gereformeerde kerk</option><option  value="5">Nederlands Hervormde kerk</option><option  value="6">Overige Christelijke kerken</option><option  value="7">Islam</option><option  value="8">Jodendom</option><option  value="9">Hindoeisme</option></select></p>
							</div>
						</div>

<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<!--<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">
						<?php writeWeightBox(8); ?>
						<h1 class="votehelptitle">Allochtoon of autochtoon</h1>
						<div class="votequestion">
							<h2>Wat is de burgerlijke staat van uw kandidaat?</h2>
							<div class="questionbox">
								<p style="margin: 0 10px"><select name="burgerlijkestaat" id="burgerlijkestaat"><option value="0">Geen voorkeur</option><option  value="1">Alleenstaand</option><option  value="2">Relatie, niet samenwonend</option><option  value="3">Samenwonend</option><option  value="4">Getrouwd / Geregistreerd partnerschap</option></select></p>
							</div>
						</div>
						<div class="helpfooter">
							<?php //$i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div>
					</div>-->

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!-- 						<h1 class="votehelptitle">Opleiding</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Opleiding </h1></div>
	  <div class="voortgang">[8 / 10]</div>
	</div>
						<?php writeWeightBox(9); ?>
						<div class="votequestion">
							<h2>Het opleidingsniveau van mijn kandidaat is:</h2>
							<div class="questionbox">
								<p style="margin: 0 10px"><select name="opleiding" id="opleiding"><option value="0">Geen voorkeur</option><option  value="1">Mavo/VMBO</option><option  value="2">Havo/VWO</option><option  value="3">MBO</option><option  value="4">HBO</option><option  value="5">Universitair en/of Promotieonderzoek</option><option  value="6">Promotieonderzoek</option></select></p>
							</div>
						</div>
<!-- 						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>
					</div>

						<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--						<h1 class="votehelptitle">Politieke Ervaring</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Ervaring </h1></div>
	  <div class="voortgang">[9 / 10]</div>
	</div>
						<?php writeWeightBox(10); ?>
						<div class="votequestion">
							<h2>Mijn kandidaat heeft de volgende ervaring als volksvertegenwoordiger:</h2>
							<div class="questionbox">
								<?php for($k=0;$k<31;$k++) $exp .= "<option value=\"$k\">$k</option>"; ?>
								<p style="margin: 0 10px">Minmaal <select name="ervaringsjaren" id="ervaringsjaren"><?php echo $exp; ?></select> of meer jaren op een van de volgende posities:<br/><br/>
<!--								<input type="checkbox" class="checkbox" id="posities" value="1" name="posities[]"/><label for="posities1">College van B&amp;W</label><br/><input type="checkbox" class="checkbox" id="posities2" value="2" name="posities[]"/><label for="posities2">Deelgemeente</label><br/><input type="checkbox" class="checkbox" id="posities3" value="3" name="posities[]"/><label for="posities3">Eerste Kamer</label><br/><input type="checkbox" class="checkbox" id="posities4" value="4" name="posities[]"/><label for="posities4">Europees Parlement</label><br/><input type="checkbox" class="checkbox" id="posities5" value="5" name="posities[]"/><label for="posities5">Gemeenteraad</label><br/><input type="checkbox" class="checkbox" id="posities7" value="7" name="posities[]"/><label for="posities7">Kabinet</label><br/><input type="checkbox" class="checkbox" id="posities6" value="6" name="posities[]"/><label for="posities6">Provinciale Staten</label><br/><input type="checkbox" class="checkbox" id="posities8" value="8" name="posities[]"/><label for="posities8">Tweede Kamer</label><br/><input type="checkbox" class="checkbox" id="posities9" value="9" name="posities[]"/><label for="posities9">Waterschappen</label></p> -->
<input class="checkbox" id="positie1" value="1" name="posities[]" type="checkbox"/><label for="posities1">College van B&amp;W</label><br/>
<input class="checkbox" id="positie2" value="2" name="posities[]" type="checkbox"/><label for="posities2">Deelgemeente</label><br/>
<input class="checkbox" id="positie3" value="3" name="posities[]" type="checkbox"/><label for="posities3">Eerste Kamer</label><br/>
<input class="checkbox" id="positie4" value="4" name="posities[]" type="checkbox"/><label for="posities4">Europees Parlement</label><br/>
<input class="checkbox" id="positie9" value="9" name="posities[]" type="checkbox"/><label for="posities9">Gedeputeerde Staten</label><br/>
<input class="checkbox" id="positie5" value="5" name="posities[]" type="checkbox"/><label for="posities5">Gemeenteraad</label><br/>
<input class="checkbox" id="positie7" value="7" name="posities[]" type="checkbox"/><label for="posities7">Kabinet</label><br/>
<input class="checkbox" id="positie6" value="6" name="posities[]" type="checkbox"/><label for="posities6">Provinciale Staten</label><br/>
<input class="checkbox" id="positie8" value="8" name="posities[]" type="checkbox"/><label for="posities8">Tweede Kamer</label><br/>
<input class="checkbox" id="positie10" value="10" name="posities[]" type="checkbox"/><label for="posities10">Waterschappen</label><br/>
							</div>
						</div>
<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

					<div id="page<?php echo $i + 1;?>" class="votehelppage" style="display: none;">

<!--						<h1 class="votehelptitle">En verder...</h1> -->
        <div class="box">
      	  <div class="dummy"><h1> Eerste Kamer </h1></div>
	  <div class="voortgang">[10 / 10]</div>
	</div>
						<?php writeWeightBox(11); ?>
						<div class="votequestion" style="margin-bottom: 10px;">
							<h2>Hoe ziet u de toekomst van de Eerste Kamer?</h2>
							<div class="questionbox" style="padding-left:15px; padding-right:15px;">De eerste kamer ... <br />
<input class="radio" id="eerstekamer1" value="1" name="eerstekamer" type="radio"><label for="eerstekamer1">moet in huidige vorm blijven bestaan</label><br/>
<input class="radio" id="eerstekamer2" value="2" name="eerstekamer" type="radio"><label for="eerstekamer2">zou opgeheven kunnen worden</label><br/>
<input class="radio" id="eerstekamer3" value="3" name="eerstekamer" type="radio"><label for="eerstekamer3">dient rechtstreeks gekozen te worden</label><br/>
<input class="radio" id="eerstekamer4" value="4" name="eerstekamer" type="radio"><label for="eerstekamer4">dient te bestaan uit daadwerkelijk provinciale vertegenwoordiging (bepaald aantal zetels per provincie).</label><br/>
							</div>
						</div>
						<div class="votequestion" style="margin-bottom: 10px;">
							<h2>Eerste Kamer verkiezingen? ... maar die bestaan toch niet?</h2>
							<div class="questionbox" style="padding-left:15px; padding-right:15px;">
							  <p>In mei 2007 wordt de Eerste Kamer gekozen door de nieuwe leden van de Provinciale Staten. Ook voor deze verkiezingen zal er een voorkeurstemhulp op www.wiekiesjij.nl worden gelanceerd, en organiseren we Eerste Kamerverkiezingen.</p>
							  <p>Wilt u begin mei een uitnodiging per e-mail ontvangen voor de Eerste Kamerverkiezingen?</p>
							  <p>Naam : <input type="text" name="eerstekamernaam" value="" style="border: 1px solid black" size=60></p>
							  <p>Email : <input type="text" name="eerstekamermail" value="" style="border: 1px solid black" size=60></p>
							  <p style="font-size: 14px;">Het e-mail adres zal <b>slechts</b> worden gebruikt om u uit te nodigen voor de Eerste Kamerverkiezingen. U krijgt een oproepkaart op naam toegestuurd, en de mogelijkheid om te stemmen op de Eerste Kamer!</p>
							</div>
						</div>
<!--						<div class="helpfooter">
							<?php $i++; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="getadvise()" style="margin: 10px 0; width: 100px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">stemadvies</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div> -->
	<img src="images/stippellijn.gif" alt=""/>
	<div class="box">
	  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>

	  <div class="volgende"> <a href="javascript:getadvise()"><img src="images/volgende.gif" alt="volgende"/></a></div>
	</div>

					</div>

				</form>

			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
