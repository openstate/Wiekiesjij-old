<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMER;
	$page = PAGE_VRKST_VOTE;

 	$harakiri = false;
 	$warn = false;

 	if(!isset($_GET['c'])) {
 		$warn = "<p>U moet eerst op <a href=\"http://www.wiekiesjij.nl\">www.wiekiesjij.nl</a> kandidaten selecteren, alvorens u kunt stemmen!</p>";
 	}
 	else {
 		$candidates = explode(";",$_GET['c']);
 		$numcan = count($candidates);
 		if($numcan < 1 || $numcan > 5) {
			$harakiri = true;
 		}
 		else {
 			$where = "WHERE 0 ";
 			foreach($candidates as $value) {
 				if(!is_numeric($value)) {
 					$harakiri = true;
 				}
 				else {
 					$where .= " OR ns_groslijst.id = " . $value;
 				}
 			}
 		}
 	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<script src="javascript/logwin.js" type="text/javascript"></script>
		<script src="javascript/vote.js?vrkst" type="text/javascript"></script>
		<link href="layout/primary.css" rel="styleSheet" type="text/css"/>
		<title>Voorkeurstemmer</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">
				<h1>Stempagina</h1>

				<?php

					if($warn !== false) {
						echo $warn;
					}
					else if($harakiri == true) {
						echo "<p class=\"filterinfo\" style=\"text-align: center; color: red\">Does not compute.</p>";
					}
					else {
						echo "<p>Welkom bij Voorkeurstemmer.nl! U staat op het punt om uw stem uit te brengen op ";
						echo "de door u gekozen kandidaten. Vul uw mobiele telefoonnummer in, en u ontvangt ";
						echo "van ons <b>gratis</b> een stemcode per sms.</p> ";

						require_once("classes/dbquery_class.php");

						$sql = "SELECT ns_groslijst.id, voornaam, voorletters, tussenvoegsel, achternaam, foto, ns_fracties.naam AS fractienaam FROM ns_groslijst ";
						$sql .= "LEFT JOIN ns_fracties ON (ns_groslijst.fractie_id = ns_fracties.id) ";
						$sql .= $where;
						$q = new DBQuery($sql);

						$marg[1] = "240";
						$marg[2] = "180";
						$marg[3] = "120";
						$marg[4] = "60";
						$marg[5] = "10";

						$xhtml = "<div id=\"votingdiv\" style=\"margin-left: ".$marg[$numcan]."px\">";

						while($r=$q->fetch()) {
							if($r['voornaam'] != '') {
								$naam = $r['voornaam'];
							}
							else {
								$naam = $r['voorletters'];
							}

							$naam .= " ".$r['tussenvoegsel']." ".$r['achternaam'];

							$xhtml .= "<div style=\"width: 100px; padding: 3px; overflow: hidden; margin: 3px; float: left; position: relative\">";
							if($r['foto'] != ''){
								$xhtml .= "<img alt=\"".$naam."\" style=\"border: 1px solid black\" src=\"voorkeurstemmer/100/".$r['foto']."\"/>";
							}
							else {
								$xhtml .= "<img alt=\"".$naam."\" style=\"border: 1px solid black\" src=\"voorkeurstemmer/100/_dummy.jpg\"/>";
							}

							$xhtml .= "<p style=\"margin: 0; text-align: center; font-weight: bold; font-size: 0.7em;\">$naam</p>"; // id=\"".$r['code']."_handle\"
							$xhtml .= "<p style=\"margin: 0; color: gray; text-align: center; font-weight: bold; font-size: 0.6em;\">".$r['fractienaam']."</p>";
							$xhtml .= "</div>";
						}

						echo $xhtml."</div>";

						$candidates = $_GET['c'];

						echo <<<EOD
							<div style="clear: both">
								<div class="attention">stem nu zelf!</div>
									<form action="prm_vote.php" method="post" id="votingform">

									<div><input type="hidden" name="pollid" value="4"/> <!-- truukje -->
									<input type="hidden" name="selectedcandidates" id="selectedcandidates" value="$candidates" /></div>
									<table id="votingbooth">
										<tr>


									<td colspan="4">
										<p id="help2" class="help"><b>Stap 1:</b> Vul het nummer van uw mobiele telefoon in en klik op de knop.
										Binnen enkele ogenblikken ontvangt u een <b>gratis</b> SMS met daarin een code waarmee u
										uw stem kunt bevestigen.<br/>
										U heeft al gestemd? Dan kunt u uw stem veranderen door uw telefoonnummer en reeds toegezonden
										stemcode in te vullen.</p>
									</td>
								</tr>

								<tr>
									<td class="label">1. mijn mobiele nummer:</td>
									<td colspan="2">
										<input onfocus="processPhoneNumber('4')" onkeyup="processPhoneNumber('4')" disabled="disabled" name="phonenumber" id="phonenumber" style="width: 160px" type="text"/>
									</td>
								</tr>
								<tr>
									<td></td>

									<td colspan="2">
										<div style="width: 164px" class="button" id="phone_button" onclick="requestCode('4',false)">
											<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
											<p class="button">klik en stem!</p>
										</div>
										<div style="" id="sms_confirm">conf</div>
										<div style="width: 164px; display: none" class="button" id="resend_button" onclick="requestCode('4',true)">
												<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>

												<p class="button">herzend stemcode</p>
											</div>
									</td>
									<td><a onfocus="this.blur()" href="javascript: toggleView('help2')" title="Uitleg">uitleg</a></td>
								</tr>

								<tr>
									<td colspan="4">

										<p id="help3" class="help"><b>Stap 2:</b> Vul de ontvangen code in. Deze code bestaat uit 8 cijfers en/of
										letters. Klik vervolgens op de knop om uw stem te bevestigen.</p>
									</td>
								</tr>
								<tr>
									<td class="label">2. mijn stemcode:</td>
									<td colspan="2">
										<input disabled="disabled" name="votingcode" id="votingcode" style="width: 160px" type="text"/>
									</td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2">
										<div style="width: 164px" class="button" id="code_button" onclick="voteVrk()">
											<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
											<p class="button">bevestig uw stem!</p>

										</div>
									</td>
									<td><a onfocus="this.blur()" href="javascript: toggleView('help3'); toggleView('help4')" title="Uitleg">uitleg</a></td>
								</tr>
								<tr>
									<td colspan="4">
										<p id="help4" class="help">U heeft nu <b>eenvoudig</b> en <b>gratis</b> uw stem laten horen!
										Voor verdere informatie kunt u het menu aan de linker zijde van de pagina gebruiken.</p>

									</td>
								</tr>
							</table>
						</form>
					</div>
EOD;
		}

			?>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
