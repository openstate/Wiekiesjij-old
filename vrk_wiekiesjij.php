<?php

	require_once "def/globals.php";

	$elitversion = "1347";
	$elitdate = "2006-11-17 15:45";

	// Page Identification
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_FILTER;

	require_once "classes/dbquery_class.php";

	if (isset($_REQUEST['region'])) {
		if ($_REQUEST['region'] == -1) {
 			// cookie moet verwijdert worden
			setcookie(COOKIE_NAME, $region, time() - 1000);
		} else {
 			$region = (int)($_REQUEST['region']);
			setcookie(COOKIE_NAME, $region);
		}
	}

	if (!isset($region) && isset($HTTP_COOKIE_VARS[COOKIE_NAME]) && $_REQUEST['region'] != -1) {
		$region = (int)($HTTP_COOKIE_VARS[COOKIE_NAME]);
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<script src="javascript/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>
		<script src="javascript/vrkstemwzr.js" type="text/javascript"></script>
<!--		<link href="layout/voorkeur.css" rel="styleSheet" type="text/css"/>  -->
		<title>Het Nieuwe Stemmen :: Voorkeurstemhulp</title>
	</head>

	<body id="base" onload="voorkeurInit()">

		<div id="container">

			<?php include "layout/page_header.php"; ?>
			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php

					include "layout/menu_left.php";

					require_once("classes/dbquery_class.php");

/*					function writeScore()
					{
						echo "<h3 style=\"background-image: url('layout/fade_left_header.png');\" class=\"menuheader\">medewerkingspercentages</h3>";

						echo "<div class=\"menu\" style=\"background-image: url('layout/fade_left_dark.png');\">";

						$fractieTotaal = array();
						$fractiePerc = array();

						$q = new DBQuery("SELECT naam , COUNT(*) AS aantal FROM ns_groslijst LEFT JOIN ns_fracties ON(ns_fracties.id = fractie_id) GROUP BY fractie_id");
						while($r = $q->fetch()) {
							$fractieTotaal[$r['naam']] = $r['aantal'];
						}

						$q = new DBQuery("SELECT naam,COUNT(*) AS aantal FROM ns_groslijst LEFT JOIN ns_fracties ON(ns_fracties.id = fractie_id) WHERE ipadres != '' GROUP BY fractie_id");

						while($r = $q->fetch()) {
							$fractiePerc[$r['naam']] = round(((($r['aantal'])/$fractieTotaal[$r['naam']])*1000)/10);
						}

						arsort ($fractiePerc);

						echo "<table>";
						foreach($fractiePerc as $key=>$val) {
							if($val < 75) {
								$color = "color: darkred";
							}
							else {
								$color = "";
							}
							echo "<tr><td style=\"font-weight: bold; font-size: 0.7em\">$key</td><td style=\"text-align: right; font-weight: bold; font-size: 0.8em; $color\">".$val."%</td></tr>";
						}
						echo "</table>";

						echo "</div>";
					}

					//writeScore();

*/				?>


				<?php
					//writeScore();
				?>
			</div>

<?php if (!isset($region)) { ?>
			<div id="col_mid">
					<div id="page0" class="votehelppage" style="display: block">

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
			</div>
			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>
<?php
// } else if(is_numeric($_GET{'profile'})) { 
//   include_once("vrk_profile.php");
 } else { ?>

			<div id="col_mid">
				<h1>De Kandidatenbrowser</h1>
				<p style="margin: 0 20px; padding: 0"><span class="number">1.</span> Selecteer een optie in het rechter menu om alle kandidaten met die eigenschap te zien.</p>
				<p style="margin: 0 20px; padding: 0"><span class="number">2.</span> Verfijn spelenderwijs uw keuzes</p>
				<p style="margin: 0 20px; padding: 0 0 10px 0;">
				<span class="number">3.</span> Klik op 'reset' om opnieuw te beginnen, of klik op een kandidaat voor meer informatie.</p>

				<?php
						$q = "SELECT naam FROM `ns_gros_provincies` WHERE id=$region";	
						$r = new DBQuery($q);
						$res=$r->fetch()
?>
						<p class="small" style="font-size: 13px;">Momenteel heeft u de provincie <?php echo $res['naam']; ?> geselecteerd, als dat niet klopt, klik dan <a href="vrk_wiekiesjij.php?region=-1">hier</a>. </p>



<!--				<div id="dropbar" style="border: 1px solid black; height: 76px; padding: 5px; margin-left: 20px; width: 510px; background-color: #B5B5B5">
					<div style="float: left; width: 130px;">
						<p style="font-weight: bold; margin: 5px; font-size: 16px; color: red">NIEUW!</p>
						<p style="font-weight: bold; margin: 5px; font-size: 14px;" id="dropheader">Selecteer 1 tot 5 kandidaten en stem!</p>
					</div>
					<div style="height: 66px; float: left;">
						<div id="dropzone0" class="dropzone"></div>
					 	<div id="dropzone1" class="dropzone"></div>
					 	<div id="dropzone2" class="dropzone"></div>
					 	<div id="dropzone3" class="dropzone"></div>
					 	<div id="dropzone4" class="dropzone"></div>
					</div>

					<div style="padding-left: 5px; padding-top: 10px; float: left; width: 70px;">
				 		<div onclick="startVote()" style="margin: 0 0 10px 0; width: 65px" class="button">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/> <
							<span class="button" style="padding: 0px 0px 0 21px; margin: 0; font-size: 14px; ">stem!</span></div>
						<div onclick="Element.toggle($('vrkuitleg'))" style="margin: 0; width: 65px" class="button">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/> 
							<p class="button" style="padding: 0px 5px 0 21px; margin: 0; font-size: 14px;">uitleg</p></div>
					</div>
				</div>
-->

				<div id="vrkuitleg" style="border: 1px solid black;display: none; padding: 5px; margin: 10px 0 10px 20px; width: 510px; background-color: #B5B5B5">
					<div onclick="Element.toggle($('vrkuitleg'))" style="margin: 0 2px; width: 65px; float: right" class="button">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
							<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">sluiten</p></div>
					<p style="font-weight: bold; margin: 5px; font-size: 1em; color: red">Uit uw voorkeurstem!</p>
					<p style="margin: 5px; padding: 0"><span class="number">1.</span>
					Selecteer 1 tot 5 Kandidaten door:
						<ul>
							<li><b>of</b> de foto van de betreffende kandidaat naar een van de vraagtekens te slepen (<img style="display: inline" src="voorkeurstemmer/dropsmall.png"/>).</li>
							<li><b>of</b> op de profielpagina van een kandidaat op 'selecteer' te klikken</li>
						</ul>
					</p>
					<p style="margin: 5px; padding: 0"><span class="number">2.</span>
					Klik op 'stem!' en u wordt naar <b>voorkeurstemmer.nl</b> gebracht, alwaar u <b>gratis</b>
					via ons principe 'One Mobile, One Vote' kunt stemmen.
					</p>

					<p style="margin: 5px; padding: 0">Kandidaten kunt u uit uw selectie verwijderen
					door op hun profielpagina op 'verwijder' te klikken. U hoeft natuurlijk niet te stemmen,
					maar u kunt in dat geval de dropbox wel gebruiken als eenvoudige manier om verschillende
					kandidaten te vergelijken.</p>
				</div>



				<div id="grospics">
					<p style="text-align: center">Selecteer enkele criteria in het rechter menu en klik op 'zoek!'</p>
				</div>

				<p style="margin: 0; padding: 0" class="profilefooter">Resultaten onder voorbehoud van invoerfouten door zowel de kandidaten als stichting HNS.</p>
			</div>

			<div id="col_right">

			    <div class="blokr-filter">
			      <img src="images/freestyle-top.gif" alt="" />
  
				<!-- BUTTONS -->
<?php 
/*
?>

				<div style="border-top: 1px solid black; clear: both; height: 30px;">
					<div onclick="filter(this)" style="margin: 5px; float: left; width: 64px" class="button">
						<!--<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/> -->
						<p class="button">zoek!</p>
					</div>
					<!--<div onclick="window.scrollTo(0,Element.getHeight(body))" style="margin: 5px 0px 5px 5px; float: left; width: 16px" class="button" title="Naar beneden">
						<img class="button" alt="keuzerondje" src="layout/button_down.png"/>
					</div> --><
					<div onclick="resetForm()" style="margin: 5px; float: right; width: 64px" class="button">
						<!--<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>-->
						<p class="button">reset</p>
					</div>
				</div>

<?php 
*/ 
?>
				<form action="" onsubmit="return false" method="post" id="filterform">
					<div style="margin-bottom: 0px; " class="menu">
						<p style="font-size:13px; padding:5px;">Klik op een van de onderstaande titels om het menu open te klappen</p>

						<!-- Persoonlijke eigenschappen -->

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menupersonal">persoonlijk</h3>
						<div class="submenu">

							<!-- Geslacht -->
							<div style="clear: both">
				 			     	<p class="sellabel">geslacht</p>
							      	<select onchange="autoFilter()" name="geslacht" id="geslacht">
							      		<option value="-1"></option>
							      		<option value="1">man</option>
							      		<option value="2">vrouw</option>
							      	</select>
						      </div>

						      <!-- Leeftijd -->
						      <div style="clear: both">
							      	<p class="sellabel">leeftijd</p>
							      	<input onchange="autoFilter()" type="text" style="float: right; width: 25px; border: 1px solid black; margin-right:20px;" name="leeftijd" id="leeftijd"/>
							      	<select style="width: 120px"  name="leeftijdop" id="leeftijdop">
							      		<option value="-1"></option>
							      		<option value="1">ouder dan</option>
							      		<option value="2">jonger dan</option>
							      		<option value="3">precies</option>
							      	</select>
						      	</div>

						      	<!-- Opleiding -->
						      	<div style="clear: both">
							      	<p class="sellabel">opleiding</p>
							      	<select onchange="autoFilter()" name="opleiding" id="opleiding">
						      			<option value="0"></option><option  value="1">Mavo/VMBO</option><option  value="2">Havo/VWO</option><option  value="3">MBO</option><option  value="4">HBO</option><option  value="5">Universitair</option><option  value="6">Promotieonderzoek</option>
								</select>
						      	</div>

							<!-- Roker -->
<!--							<div style="clear: both">
							      	<p class="sellabel">roker</p>
							      	<select onchange="autoFilter()" name="rook" id="rook">
							      		<option value="-1"></option>
							      		<option value="1">ja</option>
							      		<option value="2">nee</option>
							      	</select>
							</div> -->
	
						      <!-- Vegetarier -->
<!--						      <div style="clear: both">
							      	<p class="sellabel">vegetariër</p>
							      	<select onchange="autoFilter()" name="vega" id="vega">
							      		<option value="-1"></option>
							      		<option  value="1">Nee</option><option  value="2">Vegetariër</option><option  value="3">Veganist</option>
							      	</select>
						      </div> -->

							<!-- Levensbeschouwing -->
							<div style="clear: both">
						      		<p class="sellabel">religie?</p>
						      		<select onchange="autoFilter()" name="geloof" id="geloof">
						      			<option value="0"></option><option  value="1">Aanhanger van een religie</option><option  value="2">Er is wel iets, maar weet niet wat</option><option  value="3">Agnost</option><option  value="4">Atheïst</option>
								</select>
							</div>
		
						</div>

				   	<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menupolitics">politiek</h3>
						<div class="submenu">

							<!-- Fractie -->
							<div style="clear: both">
								<p class="sellabel">partij</p>
			      		<select onchange="autoFilter()" name="fractie_id" id="fractie_id">
			      			<?php
			      				$q = new DBQuery("SELECT * FROM ns_fracties f, ns_fraclink fl WHERE fl.provincie=".$region." AND fl.fractie=f.id");
			      				echo "<option value=\"-1\"></option>\n";
			      				while($r = $q->fetch()) {
			      					echo "<option value=\"".$r['id']."\">".$r['naam']."</option>\n";
			      				}
			      			?>
			      		</select>
							</div>
							<div style="clear: both">
								<p class="sellabel">plaats in het spectrum binnen de eigen partij (1 = links, 9 = rechts)</p>
				      	<input onchange="autoFilter()" type="text" style="float: right; width: 25px; border: 1px solid black; margin-right:20px;" name="spectrum" id="spectrum"/>
				      	<select style="width: 120px"  name="spectrumop" id="spectrumop">
				      		<option value="-1"></option>
				      		<option value="1">linkser dan</option>
				      		<option value="2">rechtser dan</option>
				      		<option value="3">precies</option>
				      	</select>
							</div> 




							<!-- Positie -->
							<div style="clear: both">
				      	<p class="sellabel">lijstpositie</p>
				      	<input onchange="autoFilter()" type="text" style="float: right; width: 25px; border: 1px solid black; margin-right:20px;" name="positie" id="positie"/>
				      	<select style="width: 120px"  name="positieop" id="positieop">
				      		<option value="-1"></option>
				      		<option value="1">lager dan</option>
				      		<option value="2">hoger dan</option>
				      		<option value="3">precies</option>
				      	</select>
			      	</div>

							<!-- Fractie lid sinds -->
							<div style="clear: both">
				      	<p class="sellabel">jaren partij lid</p>
				      	<input onchange="autoFilter()" type="text" style="float: right; width: 25px; border: 1px solid black; margin-right:20px;" name="fractielidsinds" id="fractielidsinds"/>
				      	<select style="width: 120px"  name="fractielidsindsop" id="fractielidsindsop">
				      		<option value="-1"></option>
				      		<option value="1">meer dan</option>
				      		<option value="2">minder dan</option>
				      		<option value="3">precies</option>
				      	</select>
			      	</div>

							<!-- Motivatie -->
							<div style="clear: both">
								<p class="sellabel">motivatie</p>
								<select onchange="autoFilter()" name="motivatie" id="motivatie"><option value="0"></option><option  value="1">Betere wereld</option><option  value="2">Beter Nederland</option><option  value="3">Krachtigere partij</option><option  value="4">Persoonlijke ontwikkeling</option><option  value="5">Het loonstrookje</option></select>
							</div>

							<!-- Ervaring -->
							<div style="clear: both">
				      	<p class="sellabel">jaren politieke ervaring</p>
				      	<input onchange="autoFilter()" type="text" style="float: right; width: 25px; border: 1px solid black; margin-right:20px;" name="ervaringsjaren" id="ervaringsjaren"/>
				      	<select style="width: 120px"  name="ervaringsjarenop" id="ervaringsjarenop">
				      		<option value="-1"></option>
				      		<option value="1">meer dan</option>
				      		<option value="2">minder dan</option>
				      		<option value="3">precies</option>
				      	</select>
			      	</div>

			      	<!-- Jongeren Organisatie -->
							<div style="clear: both">
				      	<p class="sellabel">actief geweest bij politieke jongerenorganisatie</p>
				      	<select onchange="autoFilter()" name="jongerenorga" id="jongerenorga">
				      		<option value="-1"></option>
				      		<option value="1">ja</option>
				      		<option value="2">nee</option>
				      	</select>
			      	</div>

			      	<!-- Artikelen -->
<!--							<div style="clear: both">
				      	<p class="sellabel">aantal gepubliceerde opinieartikelen</p>
				      	<select onchange="autoFilter()" name="artikelen" id="artikelen">
				      		<option value="0"></option><option  value="1">Geen</option><option  value="2">1-5 Artikelen</option><option  value="3">5-10 Artikelen</option><option  value="4">10-20 Artikelen</option><option  value="5">Meer dan 20 artikelen</option>
				      	</select>
			      	</div> -->

			      	<!-- Huidige Positie -->
							<div style="clear: both">
				      	<p class="sellabel">huidige positie</p>
				      	<select onchange="autoFilter()" name="huidigepositie" id="huidigepositie">
				      		<option value="-1"></option><option value="5">College van B&amp;W</option><option value="6">Deelgemeente</option><option value="7">Eerste Kamer</option><option value="8">Europees Parlement</option><option value="9">Gemeenteraad</option><option value="10">Provinciale Staten</option><option value="11">Kabinet</option><option value="12">Tweede Kamer</option>
				      	</select>
			      	</div>

			      	<!-- ervaring -->
<!--							<div style="clear: both">
				      	<p class="sellabel">voor huidige positie</p>
				      	<select onchange="autoFilter()" name="ervaring" id="ervaring">
				      		<option value="-1"></option><option value="5">College van B&amp;W</option><option value="6">Deelgemeente</option><option value="7">Eerste Kamer</option><option value="8">Europees Parlement</option><option value="9">Gemeenteraad</option><option value="10">Provinciale Staten</option><option value="11">Kabinet</option><option value="12">Tweede Kamer</option>
				      	</select>
			      	</div> -->
						</div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuervaring">ervaring</h3>
						<div class="submenu">
							<!-- sector -->
							<div style="clear: both">
				      	<p class="sellabel">komt uit sector</p>
				      	<select onchange="autoFilter()" name="sector" id="sector">
				      		<option value="-1"></option><option value="105">Het bedrijfsleven</option><option value="106">De landbouw</option><option value="107">De media</option><option value="108">Het onderwijs</option><option value="109">De overheid</option><option value="110">Het ondernemerschap</option><option value="111">De zorg	</option>
				      	</select>
			      	</div>

							<!-- expertise -->
							<div style="clear: both">
				      	<p class="sellabel">expertise op beleidsterrein</p>
				      	<select onchange="autoFilter()" name="expertise" id="expertise">
<!--				      		<option value="-1"></option><option value="88">Antilliaanse en Arubaanse Zaken</option><option value="89">Bestuurlijke Vernieuwing</option><option value="90">Buitenlandse en Europese Zaken</option><option value="91">Cultuur</option><option value="92">Defensie</option><option value="93">Economische Zaken</option><option value="94">Energie &amp; Technologie</option><option value="95">Financien</option><option value="96">Integratiebeleid</option><option value="97">Justitie</option><option value="98">Landbouw &amp; Voedselkwaliteit</option><option value="99">Natuur &amp; Milieu</option><option value="100">Onderwijs</option><option value="101">Ontwikkelingssamenwerking</option><option value="102">Verkeer &amp; Waterstaat</option><option value="103">Wetenschap</option><option value="104">Zorg &amp; Welzijn</option> -->
<option value="-1"></option>
<option value="89">Bestuurlijke Vernieuwing</option>
<option value="201">Bouw en Ruimtelijke Ordening</option>
<option value="91">Cultuur</option>
<option value="92">Defensie</option>
<option value="93">Economische Zaken</option>
<option value="94">Energie &amp; Technologie</option>
<option value="95">Financien</option>
<option value="96">Integratiebeleid</option>
<option value="88">Jeugd</option>
<option value="97">Justitie</option>
<option value="98">Landbouw &amp; Voedselkwaliteit</option>
<option value="99">Natuur &amp; Milieu</option>
<option value="100">Onderwijs</option>
<option value="101">Ontwikkelingssamenwerking</option>
<option value="90">Regionale en Europese Zaken</option>
<option value="102">Verkeer &amp; Waterstaat</option>
<option value="103">Wetenschap</option>
<option value="104">Zorg &amp; Welzijn</option>
				      	</select>
			      	</div>
						</div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menufamily">huiselijk</h3>
						<div class="submenu">

							<!-- Burgerlijke Staat -->
							<div style="clear: both">
				      	<p class="sellabel">burgerlijke staat</p>
				      	<select onchange="autoFilter()" name="burgerlijkestaat" id="burgerlijkestaat">
				      		<option value="-1"></option>
				      		<option  value="1">Alleenstaand</option>
				      		<option  value="2">Relatie, niet samenwonend</option>
				      		<option  value="3">Samenwonend</option>
				      		<option  value="4">Getrouwd / Geregistreerd partnerschap</option>
				      	</select>
				      </div>

				      <!-- Kinders -->
				      <div style="clear: both">
				      	<p class="sellabel">heeft kinderen</p>
				      	<select onchange="autoFilter()" name="kinders" id="kinders">
				      		<option value="-1"></option>
				      		<option  value="1">Ja</option><option  value="0">Nee</option>
				      	</select>
				      </div>

				      <!-- Huis -->
							<div style="clear: both">
				      	<p class="sellabel">koop- of huurhuis</p>
				      	<select onchange="autoFilter()" name="koophuur" id="koophuur">
				      		<option value="-1"></option>
				      		<option value="1">koop</option>
				      		<option value="2">huur</option>
				      	</select>
			      	</div>

				    	<!-- Auto -->
							<div style="clear: both">
				      	<p class="sellabel">autobezitter</p>
				      	<select onchange="autoFilter()" name="auto" id="auto">
				      		<option value="-1"></option>
				      		<option value="1">ja</option>
				      		<option value="2">nee</option>
				      	</select>
			      	</div>

			      	<!-- huisdier -->
<!--							<div style="clear: both">
				      	<p class="sellabel">huisdier</p>
				      	<select onchange="autoFilter()" name="huisdier" id="huisdier">
				      		<option value="-1"></option>
				      		<option value="61">Hond</option>
									<option value="62">Kat</option>
									<option value="63">Papegaai/Vogel</option>
									<option value="64">Vissen</option>
									<option value="65">Kleine knaagdieren</option>
									<option value="66">Grootvee</option>
									<option value="67">Anders</option>
				      	</select>
			      	</div>-->
 						</div>



			      <h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuliving">woont in...</h3>
						<div class="submenu">

			      	<!-- Woonplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select onchange="autoFilter()" name="woonplaats" id="woonplaats">
				      	</select>
				      </div>

				      <!-- Woonprovincie -->
<!--				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select onchange="autoFilter()" name="woonprovincie" id="woonprovincie">
				      	</select>
				      </div> -->
				    </div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuborn">geboren in...</h3>
						<div class="submenu">

				      <!-- Geboorteplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select onchange="autoFilter()" name="geboorteplaats" id="geboorteplaats">
				      	</select>
				      </div>

				      <!-- Geboorteprovincie -->
				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select onchange="autoFilter()" name="geboorteprovincie" id="geboorteprovincie">
				      	</select>
				      </div>

				      <!-- Geboorteland -->
				      <div style="clear: both">
				      	<p class="sellabel">land</p>
				      	<select onchange="autoFilter()" name="geboorteland" id="geboorteland">
				      	</select>
				      </div>

				    </div>

				    <h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuraised">opgegroeid in...</h3>
						<div class="submenu">

				      <!-- Opgroeiplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select onchange="autoFilter()" name="opgroeiplaats" id="opgroeiplaats">
				      	</select>
				      </div>

				      <!-- Opgroeiprovincie -->
				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select onchange="autoFilter()" name="opgroeiprovincie" id="opgroeiprovincie">

				      	</select>
				      </div>

				      <!-- Opgroeiland -->
				      <div style="clear: both">
				      	<p class="sellabel">land</p>
				      	<select onchange="autoFilter()" name="opgroeiland" id="opgroeiland">
				      	</select>
				      </div>

				    </div>

					</div>
				</form>

				<!-- BUTTONS -->
				<div style="height: 20px"> &nbsp;
				</div>
<?php
/*
?>
				<div style="clear: both; height: 40px;">
					 <div onclick="filter(this)" style="margin: 5px; float: left; width: 64px" class="button" title="Zoek kandidaten">
						<!--<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/> -->
						<p class="button">zoek!</p>
					</div>
					<!--<div id="arrowup" onclick="window.scrollTo(0,0)" style="margin: 5px 0px 5px 5px; float: left; width: 16px" class="button" title="Naar boven">
						<img class="button" alt="keuzerondje" src="layout/button_up.png"/>
					</div> -->
					<div onclick="resetForm()" style="margin: 5px; float: right; width: 64px" class="button" title="Leeg het formulier">
						<!-- <img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/> -->
						<p class="button">reset</p>
					</div>
				</div> 
<?php
*/
?>

			</div>
   		 </div>
			
<?php } // einde provincie kiezen ?>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
