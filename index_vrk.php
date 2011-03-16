<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_INTRO;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>

		<title>Stichting Het Nieuwe Stemmen: Voorkeursstemhulp</title>
	</head>

	<body id="base">

		<div id="wrap">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
				<h3 style="background-image: url('layout/fade_left_header.png');" class="menuheader">Kandidaten zoekmachine</h3>
				<div class="menu" style="background-image: url('layout/fade_left_dark.png');\">
					<p style="display: block; margin: 5px">Doorzoek de persoonlijk websites van de kandidaten.</p>
					<!-- Google CSE Search Box Begins -->
					<form id="searchbox_014716286875882597072:tje6xp_z7fq" action="<?php echo SEARCH_TARGET; ?>">
					  <input type="hidden" name="cx" value="014716286875882597072:tje6xp_z7fq" />
					  <input name="q" type="text" size="20" style="width: 220px" />
					  <!--<input type="submit" name="sa" value="Search" />-->
					  <input type="hidden" name="cof" value="FORID:11" />
					</form>
					<script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=searchbox_014716286875882597072%3Atje6xp_z7fq"></script>
					<!-- Google CSE Search Box Ends -->

					<div onclick="$('searchbox_014716286875882597072:tje6xp_z7fq').submit()" style="margin: 5px 0 0 5px; width: 65px" class="button">
							<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
							<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">zoek</p>
					</div>
				</div>
			</div>

			<div id="col_mid">
				<h1>De Voorkeursstemhulp</h1>
				<img class="textright" src="layout/img_wieishet.jpg" alt="Wie kiest jij?"/>

				<p>Onze Voorkeursstemhulp biedt u een gedetailleerd overzicht van alle kandidaten op de Nederlandse kieslijsten.
				Kandidaten kunnen eenvoudig worden gezocht en vergeleken op tal van interessante kenmerken en eigenschappen.
				oor dit initiatief is onze stichting een samenwerking aangegaan met het Instituut voor Publiek en Politiek (IPP),
				de makers van <a href="http://www.stemwijzer.nl">stemwijzer.nl</a>.
				</p>

				<p>Momenteel is stichting Het Nieuwe Stemmen informatie
				aan het verzamelen over de kandidaten. Zodra een substantieel deel van deze informatie
				binnen is, zal de Voorkeursstemhulp daadwerkelijk online gaan.</p>

				<p><b>De kandidaten enquête</b></p>
				<p>Wij hebben de kandidaat kamerleden opgeroepen, middels deze <a href="doc/briefaankandidaten.pdf">brief (pdf)</a>, om een
				online <a href="vrk_enquete.php">enquête</a> in te vullen. Hieronder ziet u hoeveel personen deze enquête reeds hebben ingevuld.</p>
				<div class="personalia">

				<?php
					require_once "classes/dbquery_class.php";

					$fractie = array();
					$fractieTotaal = array();
					$fractieCount = array();

					$q = new DBQuery("SELECT * FROM ns_fracties");
					while($r = $q->fetch()) {
						$fractie[$r['id']] = $r['naam'];
					}

					$q = new DBQuery("SELECT fractie_id,COUNT(*) AS aantal FROM ns_groslijst LEFT JOIN ns_fracties ON(ns_fracties.id = fractie_id) GROUP BY fractie_id");

					while($r = $q->fetch()) {
						$fractieTotaal[$r['fractie_id']] = $r['aantal'];
					}

					$q = new DBQuery("SELECT fractie_id,COUNT(*) AS aantal FROM ns_groslijst LEFT JOIN ns_fracties ON(ns_fracties.id = fractie_id) WHERE ipadres != '' GROUP BY fractie_id");

					while($r = $q->fetch()) {
						$fractieCount[$r['fractie_id']] = $r['aantal'];
					}

					echo "<table style=\"width: 540px;\">";
					$i=1;

					echo "<tr>";
					for($i;$i<5;$i++) {
						if($fractieCount[$i] == '') {
							$fractieCount[$i] = 0;
						}
						echo "<td style=\"text-align: center\">";
						if($fractieTotaal[$i] > 0) {
							echo "<p style=\"margin: 0; padding: 0; color: #ED1C24; font-family: 'trebuchet ms',sans-serif; font-size: 24px; font-weight: normal; line-height: 24px;\">";
							echo round(((($fractieCount[$i])/$fractieTotaal[$i])*1000)/10,1);
							echo "%</p>";
						}
						echo "<p style=\"margin: 0; padding: 0; font-weight: bold\">".$fractie[$i]."</p>";
						echo "</td>";
					}
					echo "</tr>";

					echo "<tr>";
					for($i;$i<9;$i++) {
						if($fractieCount[$i] == '') {
							$fractieCount[$i] = 0;
						}
						echo "<td style=\"text-align: center\">";
						if($fractieTotaal[$i] >0) {
							echo "<p style=\"color: #ED1C24; font-family: 'trebuchet ms',sans-serif; font-size: 24px; font-weight: normal; line-height: 24px;\">";
							echo round(((($fractieCount[$i])/$fractieTotaal[$i])*1000)/10,1);
							echo "%</p>";
						}
						echo "<p style=\"font-weight: bold\">".$fractie[$i]."</p>";
						echo "</td>";
					}
					echo "</tr>";

					echo "<tr>";
					for($i;$i<13;$i++) {
						if($fractieCount[$i] == '') {
							$fractieCount[$i] = 0;
						}
						echo "<td style=\"text-align: center\">";
						if($fractieTotaal[$i] > 0) {
							echo "<p style=\"color: #ED1C24; font-family: 'trebuchet ms',sans-serif; font-size: 24px; font-weight: normal; line-height: 24px;\">";
							echo round(((($fractieCount[$i])/$fractieTotaal[$i])*1000)/10,1);
							echo "%</p>";
						}
						else {
							echo "<p style=\"color: #ED1C24; font-family: 'trebuchet ms',sans-serif; font-size: 24px; font-weight: normal; line-height: 24px;\">0%</p>";
						}
						echo "<p style=\"margin: 0; padding: 0; font-weight: bold\">".$fractie[$i]."</p>";
						echo "</td>";
					}
					echo "</tr>";

					echo "</table>";

				?>

				</div>



				<h2>Waarom dit initiatief?</h2>
				<p>Het is ons opgevallen dat meer dan 80% van de Nederlanders zijn stem uitbrengt op de lijsttrekker van een partij.
				Gelet op het grote aantal kandidaten, allen met een eigen profiel en persoonlijkheid, verbaast het ons dat mensen
				niet preciezer bepalen op wie zij willen stemmen. Misschien maakt men zich er niet druk om. De achterliggende reden
				kan ook zijn dat het op het moment een zeer lastig en arbeidsintensief proces is om voldoende informatie
				over de verschillende kandidaten te vinden en met elkaar te vergelijken. Wij denken dat er meer
				voorkeurstemmen worden uitgebracht wanneer kiezers op een laagdrempelige en aantrekkelijke manier
				informatie over kandidaten kunnen vinden. Om dit te bereiken hebben wij een voorkeurstemhulp ontwikkeld.</p>

				<h2>Hoe werkt het?</h2>
				<p>Binnen de Voorkeursstemhulp kan de gebruiker allerlei eigenschappen aangeven waarvan hij of zij het
				belangrijk vindt (of juist niet) dat zijn of haar kandidaat daar aan voldoet. Op deze manier wordt
				de de kieslijst gefilterd, zodat er minder en minder kandidaten over blijven. En hoe minder kandidaten
				in de selectie zitten, hoe gedetailleerder de informatie die zichtbaar wordt over deze kandidaten.</p>
				<p>Dit proces kan worden herhaald en verfijnd, net zolang totdat de ideale kandidaat gevonden is.</p>

			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
