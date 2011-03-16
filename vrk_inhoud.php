<?php
	// elitversion = 0058, elitdate  2007-02-08 18:13
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_STEMHULP;

	if(isset($_GET['profile']) && is_numeric($_GET['profile'])) {
		header( 'Location: http://www.wiekiesjij.nl/vrk_wiekiesjij.php?profile=' . $_GET['profile'] ) ;
	}

	$regionxml = "xml/utrecht.xml";
	include "xml/parse2.php";
	$j = count($xmlstel);
	
	// if no region in query
	$j++;

	function writeWeightBox($count)
	{
		echo "<div class=\"weight\">";
		echo "<span class=\"unimportant\">onbelangrijk</span> ";
		echo "<input type=\"radio\" value=\"0.25\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"0.5\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"1\" name=\"weight$count\" checked=\"checked\"/> ";
		echo "<input type=\"radio\" value=\"2\" name=\"weight$count\"/> ";
		echo "<input type=\"radio\" value=\"4\" name=\"weight$count\"/> ";
		echo "<span class=\"important\">belangrijk</span>";
		echo "</div>";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<script src="javascript/stemhulp.js" type="text/javascript"></script>
		<link href="layout/stemhulp.css" rel="styleSheet" type="text/css"/>
		<title>Stemphulp</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">

				<form action="vrk_stemadvies.php" id="stemhulpform" method="post">
					<div id="page0" class="votehelppage" style="display: block">

						<p style="padding: 10px; border: 1px solid #ED1C24">Op 10 februari worden enquêtes naar alle kandidaten voor de Provinciale Staten van de provincies Noord-Holland,
						Zuid-Holland, Gelderland, Flevoland en Utrecht verzonden. Vanaf 19 februari zullen de voorkeurstemhulpen
						voor de genoemde provincies online te vinden zijn op www.wiekiesjij.nl.<br/><br/><i>Het Nieuwe Stemmen</i></p>

						<h1>De Voorkeurstemhulp</h1>
						<img src="layout/img_stemhulp.png" alt="stemhulp" style="margin: 0 20px; float: right"/>
						<p>Stichting <b>Het Nieuwe Stemmen</b> heeft voor u de <b>Voorkeurstemhulp</b> ontwikkeld. Met deze stemhulp
						kunt u, nadat u (ongeveer) heeft bepaald welke partij uw stem zal krijgen, eenvoudig bepalen
						wie van de kandidaten het best bij u past.</p>
							<div onclick="nextPage(0,1)" style="height: 64px; margin: 10px 0px 10px 80px; width: 205px; " class="button" title="Start de stemhulp">
								<img class="button" alt="keuzerondje" src="layout/arr_right_big.png"/>
								<p class="button" style="font-size: 48px; padding: 23px 5px 0 80px; margin: 0; ">start</p>
							</div>

						<p>De stemhulp bestaat uit <?php echo $j; ?> pagina's met
						één of meerdere korte vragen die u kunt beantwoorden. U begint met het selecteren
						van de politieke partij(en) die wij in onze overweging mee moeten nemen. Bij ieder onderdeel
						kunt u bovendien aangeven hoe <span class="important">belangrijk</span> of <span class="unimportant">onbelangrijk</span> uw voorkeur moet meewegen in onze aanbeveling aan het eind.</p>

						<p>Begin de voorkeurstemhulp door op de startknop onderaan de pagina te klikken.</p>

						<div class="helpfooter">

						</div>
					</div>



					<div id="page1" class="votehelppage" style="display: none;">
						<h1 class="votehelptitle">Politieke Partijen</h1>

						<h2>Welke partij(en) maken kans op uw stem?</h2>
						<div class="votequestion" id="parties">
							<div class="party">
								<img alt="CDA" src="voorkeurstemmer/_cda.png"/>
								<input type="checkbox" value="3" name="party[]" id="party1"/>
								<label for="party1" >CDA</label>
							</div>
							<div class="party">
								<img alt="CU" src="voorkeurstemmer/_cu.png"/>
								<input type="checkbox" value="8" name="party[]" id="party2"/>
								<label for="party2">Christen Unie</label>
							</div>
							<div class="party">
								<img alt="D66" src="voorkeurstemmer/_d66.png"/>
								<input type="checkbox" value="6" name="party[]" id="party3"/>
								<label for="party3">D66</label>
							</div>
							<div class="party">
								<img alt="1NL" src="voorkeurstemmer/_1nl.png"/>
								<input type="checkbox" value="10" name="party[]" id="party4"/>
								<label for="party4">EénNL</label>
							</div>
							<div class="party">
								<img alt="GL" src="voorkeurstemmer/_gl.png"/>
								<input type="checkbox" value="5" name="party[]" id="party5"/>
								<label for="party5">Groen Links</label>
							</div>
							<div class="party">
								<img alt="LVF" src="voorkeurstemmer/_lvf.png"/>
								<input type="checkbox" value="7" name="party[]" id="party6"/>
								<label for="party6">Lijst Vijf Fortuyn</label>
							</div>
							<div class="party">
								<img alt="PvdD" src="voorkeurstemmer/_pvdd.png"/>
								<input type="checkbox" value="12" name="party[]" id="party7"/>
								<label for="party7">Partij vd Dieren</label>
							</div>
							<div class="party">
								<img alt="PvdA" src="voorkeurstemmer/_pvda.png"/>
								<input type="checkbox" value="2" name="party[]" id="party8"/>
								<label for="party8">PVDA</label>
							</div>
							<div class="party">
								<img alt="PvV" src="voorkeurstemmer/_pvv.png"/>
								<input type="checkbox" value="11" name="party[]" id="party9"/>
								<label for="party9">Partij vd Vrijheid</label>
							</div>
							<div class="party">
								<img alt="SGP" src="voorkeurstemmer/_sgp.png"/>
								<input type="checkbox" value="9" name="party[]" id="party10"/>
								<label for="party10">SGP</label>
							</div>
							<div class="party">
								<img alt="SP" src="voorkeurstemmer/_sp.png"/>
								<input type="checkbox" value="4" name="party[]" id="party11"/>
								<label for="party11">SP</label>
							</div>
							<div class="party">
								<img alt="VVD" src="voorkeurstemmer/_vvd.png"/>
								<input type="checkbox" value="1" name="party[]" id="party12"/>
								<label for="party12">VVD</label>
							</div>
						</div>

						<div class="helpfooter">
							<?php $i=1; echo "<h1>$i/$j</h1>"; ?>
							<div onclick="nextPage(<?php echo $i.",".($i + 1); ?>, true)" style="margin: 10px 0; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_right.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">verder</p>
							</div>
							<div onclick="previousPage(<?php echo $i.",".($i - 1); ?>)" style="margin: 10px 10px; width: 65px; float: right" class="button">
								<img class="button" alt="keuzerondje" src="layout/arr_left.png"/>
								<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">terug</p>
							</div>
						</div>

					</div>

					<!-- begin page -->
<?php
				

				foreach ($xmlstel as $sid=>$sdata) {
					echo "<div id=\"page".($i + 1)."\" class=\"votehelppage\" style=\"display: none;\">\n";
						echo "<h1 class=\"votehelptitle\">".$sdata['THEMA']."</h1>\n";
						writeWeightBox($i);
						echo "<div class=\"votequestion\">\n";
							echo "<h2>".$sdata['STELLING']."</h2>\n";
							echo "<div class=\"questionbox\">\n";
								echo "<label for=\"eens".$i."\">eens</label><input id=\"eens".$i."\" value=\"1\" type=\"radio\" name=\"s".$i."\"/>\n";
								echo "<label for=\"oneens".$i."\">oneens</label><input id=\"oneens".$i."\" value=\"2\" type=\"radio\" name=\"s".$i."\"/>\n";
								echo "<label for=\"weetniet".$i."\">weet niet</label><input id=\"weetniet".$i."\" value=\"0\" type=\"radio\" name=\"s".$i."\" checked=\"checked\"/>\n";
							echo "</div>\n";
						echo "</div>\n";
						echo "<div class=\"helpfooter\">\n";
							$i++; echo "<h1>$i/$j</h1>\n";
							if ($i==$j) { 
								echo "<div onclick=\"getadvise()\" style=\"margin: 10px 0; width: 100px; float: right\" class=\"button\">\n";
									echo "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/arr_right.png\"/>\n";
									echo "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">stemadvies</p>\n";
								echo "</div>\n";
							} else {
								echo "<div onclick=\"nextPage(".$i.",".($i + 1).")\" style=\"margin: 10px 0; width: 65px; float: right\" class=\"button\">\n";
									echo "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/arr_right.png\"/>\n";
									echo "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">verder</p>\n";
								echo "</div>\n";
							}
							echo "<div onclick=\"previousPage(".$i.",".($i - 1).")\" style=\"margin: 10px 10px; width: 65px; float: right\" class=\"button\">\n";
								echo "<img class=\"button\" alt=\"keuzerondje\" src=\"layout/arr_left.png\"/>\n";
								echo "<p class=\"button\" style=\"padding: 0px 5px 0 21px; margin: 0; \">terug</p>\n";
							echo "</div>\n";
						echo "</div>\n";
					echo "</div>\n";
				}
?>
					<!-- end page -->
					
				</form>

			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
