<?php
	require_once "def/globals.php";

	$elitversion = "1411";
	$elitdate = "2006-11-16 1:08";

	// Page Identification
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_SEARCH;

	require_once "classes/dbquery_class.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>
		<script src="javascript/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>
		<!--<script src="javascript/vrkstemwzr.js" type="text/javascript"></script>-->
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
				?>
			</div>

			<div id="col_mid">
				<h1>Kandidaten Zoekmachine</h1>
				<p>Doorzoek de persoonlijke websites van de kandidaatleden.</p>
				<!-- Google Search Result Snippet Begins -->
				<div id="results_014716286875882597072:tje6xp_z7fq" style="margin: 20px">
					<div onclick="$('searchbox_014716286875882597072:tje6xp_z7fq').submit()" style="margin: 0px 10px 20px 5px; width: 65px; float: right" class="button">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button" style="padding: 0px 5px 0 21px; margin: 0; ">zoek</p>
					</div>
					<!-- Google CSE Search Box Begins -->
					<form id="searchbox_014716286875882597072:tje6xp_z7fq" action="<? echo $searchres; ?>">
					  <input type="hidden" name="cx" value="014716286875882597072:tje6xp_z7fq" />
					  <input name="q" type="text" size="70"/>
					  <!--<input type="submit" name="sa" value="Search" />-->
					  <input type="hidden" name="cof" value="FORID:11" />
					</form>
					<script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=searchbox_014716286875882597072%3Atje6xp_z7fq"></script>
					<!-- Google CSE Search Box Ends -->
				</div>

				<script type="text/javascript">
				  var googleSearchIframeName = "results_014716286875882597072:tje6xp_z7fq";
				  var googleSearchFormName = "searchbox_014716286875882597072:tje6xp_z7fq";
				  var googleSearchFrameWidth = 400;
				  var googleSearchFrameborder = 0;
				  var googleSearchDomain = "www.google.com";
				  var googleSearchPath = "/cse";
				</script>
				<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
				<!-- Google Search Result Snippet Ends -->
			</div>

			<div id="col_right">
				<h3 style="text-align: right; background-image: url('layout/fade_right_header.png');" class="menuheader">Ook op <i>uw</i> site?</h3>
				<div class="menu" style="background-image: url('layout/fade_left_dark.png');">
					<p>Wilt u onze zoekmachine ook op <i>uw</i> site? Klik dan <span style="cursor: pointer; color: #B70F17;" onclick="Element.toggle('code')">hier</span>
					en kopiëer de HTML code naar uw pagina.</p>
					<div id="code" style="display: none">
						<textarea  onclick="this.focus(); this.select();" cols="18" rows="12" readonly="" wrap="off">
<!-- Google CSE Search Box Begins -->
  <form id="searchbox_014716286875882597072:tje6xp_z7fq" action="http://www.google.com/cse">
    <input type="hidden" name="cx" value="014716286875882597072:tje6xp_z7fq" />
    <input name="q" type="text" size="20" />
    <input type="submit" name="sa" value="Search" />
    <input type="hidden" name="cof" value="FORID:0" />
  </form>
  <script type="text/javascript" src="http://www.google.com/coop/cse/brand?form=searchbox_014716286875882597072%3Atje6xp_z7fq"></script>
<!-- Google CSE Search Box Ends -->
						</textarea>
					</div>

				</div>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
