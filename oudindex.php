<?php
	require_once "def/globals.php";

	$elitversion = "1106";
	$elitdate = "2006-10-26 10:07";

	// Page Identification
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_WIEKIESJIJ;

	require_once "classes/dbquery_class.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<!--<script src="javascript/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>-->
		<script src="javascript/vrkstemwzr.js" type="text/javascript"></script>
		<link href="layout/voorkeur.css" rel="styleSheet" type="text/css"/>
		<title>Het Nieuwe Stemmen :: Voorkeurstemhulp</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>
			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php

					include "layout/menu_left.php";

					require_once("classes/dbquery_class.php");

					function writeScore()
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

					writeScore();
				?>
			</div>

			<div id="col_mid">
				<h1>De Voorkeurstemhulp</h1>

				<p style="margin: 0 20px; padding: 0"><span class="number">1.</span> Maak <b>enkele</b> keuzes in het rechter menu.
				Wees niet meteen <i>te</i> specifiek!</p>
				<p style="margin: 0 20px; padding: 0"><span class="number">2.</span> Klik op 'zoek!'</p>
				<p style="margin: 0 20px; padding: 0 0 10px 0; border-bottom: 1px solid black;">
				<span class="number">3.</span> Klik op uw favoriete kandidaat of verfijn spelenderwijs uw keuzes!</p>

				<p style="margin: 0; padding: 0" class="profilefooter">Resultaten onder voorbehoud van invoerfouten door zowel de kandidaten als stichting HNS.</p>

				<!-- <div style="background-color: #DDDDDD; border: 1px solid black; width: 185px; margin: 0 auto;" id="dropboxtop">drop!</div> -->

				<div id="grospics">
					<?php //echo "<p class=\"filterinfo\">U heeft momenteel $count kandidaten in uw selectie.</p>"; ?>
					<img class="candidate" alt="Te veel matches" src="voorkeurstemmer/cloud.jpg" />
				</div>

			</div>

			<div id="col_right">

				<h3 style="background-image: url('layout/fade_right_header.png'); text-align: right" class="menuheader">filtermenu</h3>

				<!-- BUTTONS -->
				<div style="border-top: 1px solid black; clear: both; height: 30px;">
					<div onclick="filter(this)" style="margin: 5px; float: left; width: 64px" class="button">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button">zoek!</p>
					</div>
					<div onclick="window.scrollTo(0,Element.getHeight(body))" style="margin: 5px 0px 5px 5px; float: left; width: 16px" class="button" title="Naar beneden">
						<img class="button" alt="keuzerondje" src="layout/button_down.png"/>
					</div>
					<div onclick="Form.reset('filterform')" style="margin: 5px; float: right; width: 64px" class="button">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button">reset</p>
					</div>
				</div>

				<form action="" onsubmit="return false" method="post" id="filterform">
					<div style="background-image: url('layout/fade_right_dark.png'); margin-bottom: 0" class="menu">

						<!-- Persoonlijke eigenschappen -->

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menupersonal">persoonlijk</h3>
						<div class="submenu">

							<!-- Geslacht -->
							<div style="clear: both">
				      	<p class="sellabel">geslacht</p>
				      	<select name="geslacht" id="geslacht">
				      		<option value="-1"></option>
				      		<option value="1">man</option>
				      		<option value="2">vrouw</option>
				      	</select>
				      </div>

				      <!-- Leeftijd -->
				      <div style="clear: both">
				      	<p class="sellabel">leeftijd</p>
				      	<input type="text" style="float: right; width: 25px; " name="leeftijd" id="leeftijd"/>
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
				      	<select name="opleiding" id="opleiding">
			      			<option value="0"></option><option  value="1">Mavo/VMBO</option><option  value="2">Havo/VWO</option><option  value="3">MBO</option><option  value="4">HBO</option><option  value="5">Universitair</option><option  value="6">Promotieonderzoek</option>
								</select>
			      	</div>

							<!-- Roker -->
							<div style="clear: both">
				      	<p class="sellabel">roker</p>
				      	<select name="rook" id="rook">
				      		<option value="-1"></option>
				      		<option value="1">ja</option>
				      		<option value="2">nee</option>
				      	</select>
				      </div>

				      <!-- Vegetarier -->
				      <div style="clear: both">
				      	<p class="sellabel">vegetariër</p>
				      	<select name="vega" id="vega">
				      		<option value="-1"></option>
				      		<option  value="1">Nee</option><option  value="2">Vegetariër</option><option  value="3">Veganist</option>
				      	</select>
				      </div>

				      <!-- Levensbeschouwing -->
				      <div style="clear: both">
				      	<p class="sellabel">religie?</p>
				      	<select name="geloof" id="geloof">
				      		<option value="0"></option><option  value="1">Aanhanger van een religie</option><option  value="2">Er is wel iets, maar weet niet wat</option><option  value="3">Agnost</option><option  value="4">Atheïst</option>
				      	</select>
				      </div>

				    </div>

				   	<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menupolitics">politiek</h3>
						<div class="submenu">

							<!-- Fractie -->
							<div style="clear: both">
								<p class="sellabel">partij</p>
			      		<select name="fractie_id" id="fractie_id">
			      			<?php
			      				$q = new DBQuery("SELECT * FROM ns_fracties");
			      				echo "<option value=\"-1\"></option>\n";
			      				while($r = $q->fetch()) {
			      					echo "<option value=\"".$r['id']."\">".$r['naam']."</option>\n";
			      				}
			      			?>
			      		</select>
							</div>

							<!-- Positie -->
							<div style="clear: both">
				      	<p class="sellabel">lijstpositie</p>
				      	<input type="text" style="float: right; width: 25px; " name="positie" id="positie"/>
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
				      	<input type="text" style="float: right; width: 25px; " name="fractielidsinds" id="fractielidsinds"/>
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
								<select name="motivatie" id="motivatie"><option value="0"></option><option  value="1">Betere wereld</option><option  value="2">Beter Nederland</option><option  value="3">Krachtigere partij</option><option  value="4">Persoonlijke ontwikkeling</option><option  value="5">Het loonstrookje</option></select>
							</div>

							<!-- Ervaring -->
							<div style="clear: both">
				      	<p class="sellabel">jaren politieke ervaring</p>
				      	<input type="text" style="float: right; width: 25px; " name="ervaringsjaren" id="ervaringsjaren"/>
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
				      	<select name="jongerenorga" id="jongerenorga">
				      		<option value="-1"></option>
				      		<option value="1">ja</option>
				      		<option value="2">nee</option>
				      	</select>
			      	</div>

			      	<!-- Artikelen -->
							<div style="clear: both">
				      	<p class="sellabel">aantal gepubliceerde opinieartikelen</p>
				      	<select name="artikelen" id="artikelen">
				      		<option value="0"></option><option  value="1">Geen</option><option  value="2">1-5 Artikelen</option><option  value="3">5-10 Artikelen</option><option  value="4">10-20 Artikelen</option><option  value="5">Meer dan 20 artikelen</option>
				      	</select>
			      	</div>

			      	<!-- Huidige Positie -->
							<div style="clear: both">
				      	<p class="sellabel">huidige positie</p>
				      	<select name="huidigepositie" id="huidigepositie">
				      		<option value="-1"></option><option value="5">College van B&amp;W</option><option value="6">Deelgemeente</option><option value="7">Eerste Kamer</option><option value="8">Europees Parlement</option><option value="9">Gemeenteraad</option><option value="10">Provinciale Staten</option><option value="11">Kabinet</option><option value="12">Tweede Kamer</option>
				      	</select>
			      	</div>

			      	<!-- ervaring -->
							<div style="clear: both">
				      	<p class="sellabel">voor huidige positie</p>
				      	<select name="ervaring" id="ervaring">
				      		<option value="-1"></option><option value="5">College van B&amp;W</option><option value="6">Deelgemeente</option><option value="7">Eerste Kamer</option><option value="8">Europees Parlement</option><option value="9">Gemeenteraad</option><option value="10">Provinciale Staten</option><option value="11">Kabinet</option><option value="12">Tweede Kamer</option>
				      	</select>
			      	</div>
						</div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuervaring">ervaring</h3>
						<div class="submenu">
							<!-- sector -->
							<div style="clear: both">
				      	<p class="sellabel">komt uit sector</p>
				      	<select name="sector" id="sector">
				      		<option value="-1"></option><option value="105">Het bedrijfsleven</option><option value="106">De landbouw</option><option value="107">De media</option><option value="108">Het onderwijs</option><option value="109">De overheid</option><option value="110">Het ondernemerschap</option><option value="111">De zorg	</option>
				      	</select>
			      	</div>

							<!-- expertise -->
							<div style="clear: both">
				      	<p class="sellabel">expertise op beleidsterrein</p>
				      	<select name="expertise" id="expertise">
				      		<option value="-1"></option><option value="88">Antilliaanse en Arubaanse Zaken</option><option value="89">Bestuurlijke Vernieuwing</option><option value="90">Buitenlandse en Europese Zaken</option><option value="91">Cultuur</option><option value="92">Defensie</option><option value="93">Economische Zaken</option><option value="94">Energie &amp; Technologie</option><option value="95">Financien</option><option value="96">Integratiebeleid</option><option value="97">Justitie</option><option value="98">Landbouw &amp; Voedselkwaliteit</option><option value="99">Natuur &amp; Milieu</option><option value="100">Onderwijs</option><option value="101">Ontwikkelingssamenwerking</option><option value="102">Verkeer &amp; Waterstaat</option><option value="103">Wetenschap</option><option value="104">Zorg &amp; Welzijn</option>
				      	</select>
			      	</div>
						</div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menufamily">huiselijk</h3>
						<div class="submenu">

							<!-- Burgerlijke Staat -->
							<div style="clear: both">
				      	<p class="sellabel">burgerlijke staat</p>
				      	<select name="burgerlijkestaat" id="burgerlijkestaat">
				      		<option value="-1"></option>
				      		<option  value="1">Alleenstaand</option>
				      		<option  value="2">Relatie, niet samenwonend</option>
				      		<option  value="3">Samenwonend</option>
				      		<option  value="4">Getrouwd / Geregistreerd partnerschap</option>
				      	</select>
				      </div>

				      <!-- Kinders -->
				      <div style="clear: both">
				      	<p class="sellabel">kinderen</p>
				      	<select name="kinders" id="kinders">
				      		<option value="-1"></option>
				      		<option  value="1">Nee</option><option  value="2">1</option><option  value="3">2</option><option  value="4">3</option><option  value="5">Meer dan drie</option>
				      	</select>
				      </div>

				      <!-- Huis -->
							<div style="clear: both">
				      	<p class="sellabel">koop- of huurhuis</p>
				      	<select name="koophuur" id="koophuur">
				      		<option value="-1"></option>
				      		<option value="1">koop</option>
				      		<option value="2">huur</option>
				      	</select>
			      	</div>

				    	<!-- Auto -->
							<div style="clear: both">
				      	<p class="sellabel">autobezitter</p>
				      	<select name="auto" id="auto">
				      		<option value="-1"></option>
				      		<option value="1">ja</option>
				      		<option value="2">nee</option>
				      	</select>
			      	</div>

			      	<!-- huisdier -->
							<div style="clear: both">
				      	<p class="sellabel">huisdier</p>
				      	<select name="huisdier" id="huisdier">
				      		<option value="-1"></option>
				      		<option value="61">Hond</option>
									<option value="62">Kat</option>
									<option value="63">Papegaai/Vogel</option>
									<option value="64">Vissen</option>
									<option value="65">Kleine knaagdieren</option>
									<option value="66">Grootvee</option>
									<option value="67">Anders</option>
				      	</select>
			      	</div>
 						</div>



			      <h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuliving">woont in...</h3>
						<div class="submenu">

			      	<!-- Woonplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select name="woonplaats" id="woonplaats">
				      	</select>
				      </div>

				      <!-- Woonprovincie -->
				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select name="woonprovincie" id="woonprovincie">
				      	</select>
				      </div>
				    </div>

						<h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuborn">geboren in...</h3>
						<div class="submenu">

				      <!-- Geboorteplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select name="geboorteplaats" id="geboorteplaats">
				      	</select>
				      </div>

				      <!-- Geboorteprovincie -->
				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select name="geboorteprovincie" id="geboorteprovincie">

				      	</select>
				      </div>

				      <!-- Geboorteland -->
				      <div style="clear: both">
				      	<p class="sellabel">land</p>
				      	<select name="geboorteland" id="geboorteland">
				      	</select>
				      </div>

				    </div>

				    <h3 style="margin-bottom: 10px;" class="tree" onclick="fold(this)" id="menuraised">opgegroeid in...</h3>
						<div class="submenu">

				      <!-- Opgroeiplaats -->
			      	<div style="clear: both">
				      	<p class="sellabel">gemeente</p>
				      	<select name="opgroeiplaats" id="opgroeiplaats">
				      	</select>
				      </div>

				      <!-- Opgroeiprovincie -->
				      <div style="clear: both">
				      	<p class="sellabel">provincie</p>
				      	<select name="opgroeiprovincie" id="opgroeiprovincie">

				      	</select>
				      </div>

				      <!-- Opgroeiland -->
				      <div style="clear: both">
				      	<p class="sellabel">land</p>
				      	<select name="opgroeiland" id="opgroeiland">
				      	</select>
				      </div>

				    </div>

					</div>
				</form>

				<!-- BUTTONS -->
				<div style="border-bottom: 1px solid black; clear: both; height: 30px;">
					<div onclick="filter(this)" style="margin: 5px; float: left; width: 64px" class="button" title="Zoek kandidaten">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button">zoek!</p>
					</div>
					<div id="arrowup" onclick="window.scrollTo(0,0)" style="margin: 5px 0px 5px 5px; float: left; width: 16px" class="button" title="Naar boven">
						<img class="button" alt="keuzerondje" src="layout/button_up.png"/>
					</div>
					<div onclick="Form.reset('filterform')" style="margin: 5px; float: right; width: 64px" class="button" title="Leeg het formulier">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button">reset</p>
					</div>
				</div>

			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
