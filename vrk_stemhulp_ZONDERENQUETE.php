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
		arsort($sw_score,SORT_NUMERIC);
		$vanSW = isset($_GET['isadmin']);
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
<!--		<link href="layout/stemhulp.css" rel="styleSheet" type="text/css"/> -->
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

$count=0;
foreach ($sw_score as $id=>$sc) {
	if ($count > 9) {
		break;
	} else {
		$count++;
		$sqlq = "SELECT ns_groslijst.id, nw_foto as foto, voornaam, tussenvoegsel, voorletters, achternaam, ns_fracties.naam AS fractienaam, ns_fracties.id as fractie_id FROM ns_groslijst LEFT JOIN ns_fracties ON ns_fracties.id = fractie_id WHERE ns_groslijst.id=$id";
		$rq = new DBQuery($sqlq);
		$resq = $rq->fetch();
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

<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=15");
 $r = $q->fetch();
 echo base64_decode($r['content']);

						$q = "SELECT naam FROM `ns_gros_provincies` WHERE id=$region";	
						$r = new DBQuery($q);
						$res=$r->fetch()
?>
						<p class="small">Momenteel heeft u de provincie <?php echo $res['naam']; ?> geselecteerd, als dat niet klopt, klik dan <a href="?region=-1">hier</a>. </p>

					</div>
<?php } else { ?>
					<div id="page0" class="votehelppage" style="display: block">

<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=24");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>

					</div>
<?php }
$i =1 ;
 ?>

					<div id="page1" class="votehelppage" style="display: none;">
					       <div class="box">
					      	  <div class="dummy"><h1> Partijen </h1></div>
						  <div class="voortgang">[1 / 10]</div>
						</div>
	
						<h2>Welke partij(en) maken kans op uw stem?</h2>
						<div class="votequestion" id="parties">

							<?php 

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
								echo "<script type=\"test/javascript\">numcheckies=$cnt;</script>";
			
							?>
						</div>

						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
							<div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".$back1; ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
							<div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>,true)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">
					        <div class="box">
					      		<div class="dummy"><h1> Geslacht </h1></div>
							<div class="voortgang">[2 / 10]</div>
						</div>
						<?php writeWeightBox(2); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=16");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
							<div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
							<div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

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
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

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

						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Expertise </h1></div>
						  <div class="voortgang">[5 / 10]</div>
						</div>
						<?php writeWeightBox(5); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=18");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Gezinssituatie </h1></div>
						  <div class="voortgang">[6 / 10]</div>
						</div>
						<?php writeWeightBox(6); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=19");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Levensovertuiging </h1></div>
						  <div class="voortgang">[7 / 10]</div>
						</div>
						<?php writeWeightBox(7); ?>

<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=20");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Opleiding </h1></div>
						  <div class="voortgang">[8 / 10]</div>
						</div>
						<?php writeWeightBox(9); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=21");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
						<img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>
					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Ervaring </h1></div>
						  <div class="voortgang">[9 / 10]</div>
						</div>
						<?php writeWeightBox(10); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=22");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
					       <img src="images/stippellijn.gif" alt=""/>
						<div class="box">
						  <div class="vorige"> <a href="javascript:previousPage(<?php echo $i.",".($i - 1); ?>)"><img src="images/vorige.gif" alt="vorige"/></a></div>
						  <div class="volgende"> <a href="javascript:nextPage(<?php echo $i.",".($i + 1); ?>)"><img src="images/volgende.gif" alt="volgende"/></a></div>
						</div>

					</div>

					<div id="page<?php $i++; echo $i;?>" class="votehelppage" style="display: none;">

					        <div class="box">
					      	  <div class="dummy"><h1> Eerste Kamer </h1></div>
						  <div class="voortgang">[10 / 10]</div>
						</div>
						<?php writeWeightBox(11); ?>
<?php
 $q = new DBQuery("SELECT content FROM ns_static WHERE id=23");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
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
