<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_STEMHULP;

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
					$foto = array();
					$accum = 0;

					

					if(count($_REQUEST['sw']) == 0) {
						echo "<p style=\"text-align: center; font-weight: bold; color: red\">U kunt alleen op deze pagina binnen komen vanuit de stemwijzer!</p>";
					}
					else {
						$swquery = $_REQUEST['sw'];
						include("rekenmodel.php");

						$count = 0;
						$votelink = "";
						$fotos = "";

						foreach ($score as $id=>$val) {
							$sql = "SELECT ns_groslijst.id, foto, voornaam, voorletters, achternaam, geslacht, leeftijd, geboorteprovincie, opgroeiprovincie, woonprovincie, burgerlijkestaat, gemeenschap, geloof, opleiding, ervaringsjaren, rook, vega, koophuur, auto, voetbal, ns_fracties.naam AS fractienaam FROM ns_groslijst LEFT JOIN ns_fracties ON ns_fracties.id = fractie_id WHERE ns_groslijst.id = ".(int)$id.";";
							$q = new DBQuery($sql);
							$numres = $q->numRows();
							
							$r = $q->fetch();
							if($r['voornaam'] != '') {
								$naam = $r['voornaam'];
							}
							else {
								$naam = $r['voorletters'];
							}

							$naam .= " ".$r['tussenvoegsel']." ".$r['achternaam'];
							$names[$r['id']] = $naam;
							$foto[$r['id']] = $r['foto'];
							$frac[$r['id']] = $r['fractienaam'];
						}


						foreach($score as $id => $val) {
							if($count < 5) {
								$count++;
								$votelink .= $id.";";
								$fotos .= "<img style=\"float: left\" alt=\"".$names[$id]."\" src=\"voorkeurstemmer/50/".$foto[$id]."\"/>";
							}
							else {
								break;
							}
						}


						?>
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
						<table style="display: block; margin-bottom: 10px; margin-left: 20px; width: 280px; border: 1px solid black; background-image: url('layout/background_graph.jpg')">
						<?php
							//echo $accum/$numres;
							$count = 0;
							$size = 50;
							foreach($score as $id => $val) {
								if($count > 9) {
									break;
								}
								else {
									$count++;
									$perc = (int)$val;
									if($perc > 100) {
										//$perc = 100;
									}
									$css = "style=\"margin: 10px 0px 10px 10px; border: 1px solid black\"";
									echo "<tr>";
									echo "<td><a href=\"vrk_wiekiesjij.php?profile=".$id."\">";
									if($foto[$id]) {
										echo "<img  title=\"".$names[$id]."\" $css src=\"voorkeurstemmer/$size/".$foto[$id]."\"/>";
									}
									else {
										echo "<img title=\"".$names[$id]."\" src=\"voorkeurstemmer/$size/_dummy.jpg\" $css/>";
									}

									echo "</a></td><td  style=\"vertical-align: bottom;\"><h2 class=\"percentage\">".floor($perc)."%</h2></td><td style=\"font-size: 12px; vertical-align: bottom; font-weight: bold; padding: 10px 5px;\">".$names[$id];
									echo ", ".$frac[$id]."</td>";
									echo "</tr>";

									//if($count == 5) {
										echo "</table><table style=\"display: block; margin-bottom: 10px; margin-left: 20px; width: 280px; border: 1px solid black; background-image: url('layout/background_graph.jpg')\">";
									//}
								}
							}
						}
					?>
				</table>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
