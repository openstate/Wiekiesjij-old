<?php

	require_once("classes/dbquery_class.php");

	$elitversion = "0315";
	$elitdate = "2006-11-23 11:53";

	/** This cript builds the left column navigation menu.
		 	It tries to determine the current position within the HNS
		 	site structure by checking two constants, which should be defined
		 	in the page which is currently viewed:

			$part	- The sub section of the site
			$page - The page within the sub section

		 	See /def/globals.php for valid values.
	**/

	// Retrieve polls from the database so links can be created for them in the menu
	$q = new DBQuery("SELECT id, actief, titel, titelmenu FROM ns_poll WHERE visible = 1");
	$ap = array();	// active polls
	$ip = array();	// inactive polls

	while ($pll = $q->fetch()){
		if($pll['actief'] == 1) {
			$ap[] = $pll;
		}
		else {
			$ip[] = $pll;
		}
	}

	// For dev purposes.
	if(strpos($_SERVER['HTTP_HOST'], "gleufserver") === false && strpos($_SERVER['HTTP_HOST'], "snkd") === false) {
		$dom_primaries = "http://www.primaries.nl/";
		$dom_wiekiesjij = "http://www.wiekiesjij.nl/";
	}

	function getHeader($title, $unfold)
	{
		$ufcss = "style=\"background-image: url('layout/arr_down.gif')\"";
		return "<h3 ".($unfold ? $ufcss : "")." class=\"tree\" onclick=\"fold(this)\">$title</h3>\n";
	}

	function getBlock($content, $unfold)
	{
		$showcss = "style=\"display: block\"";
		return "<div ".($unfold ? $showcss : "")." class=\"submenu\">\n".$content."</div>\n";
	}

	function getItem($label, $title, $url, $active)
	{
		$activeclass = " class=\"active\"";
		return "<p".($active? $activeclass : "")."><a title=\"$title\" href=\"$url\">$label</a></p>\n";
	}

	echo "<h3 id=\"slidemenuheader\" style=\"background-image: url('layout/fade_left_header.png');\" class=\"menuheader\">menu</h3>";
	echo "<div id=\"slidemenu\" style=\"background-image: url('layout/fade_left_dark.png');\" class=\"menu\">\n";

	// ============================ Het Nieuwe Stemmen ============================

	$unfold = ($part == PART_HETNIEUWESTEMMEN);
	echo getHeader("het nieuwe stemmen", $unfold);
	$content  = getItem("introductie","Een introductie tot Het Nieuwe Stemmen",$dom_primaries."index.php", $page == PAGE_HNS_INTRO);
	$content .= getItem("wie zijn wij?","Wie zijn wij?",$dom_primaries."hns_wie.php", $page == PAGE_HNS_WIE);
	$content .= getItem("contact","Contact",$dom_primaries."hns_contact.php", $page == PAGE_HNS_CONTACT);
	echo getBlock($content, $unfold);

	// ============================ Primaries ============================

	$unfold = ($part == PART_PRIMARIES);
	echo getHeader("primaries", $unfold);
	$content  = getItem("introductie","Wat zijn Primaries?",$dom_primaries."index_prm.php", $page == PAGE_PRM_INTRO);
	$content .= getItem("veelgestelde vragen","Veelgestelde vragen",$dom_primaries."prm_faq.php", $page == PAGE_PRM_VRAGEN);

	if(count($ap) > 0) {
		$content .= getHeader("lopende primaries", true);
		$subcontent = "";
		foreach($ap as $value) {
			$subcontent .=  getItem($value['titelmenu'],
															$value['titel'],
															$dom_primaries."prm_primary.php?pollid=".$value['id'],
															($_GET['pollid'] + $_POST['pollid']) == $value['id']);
		}
		$content .= getBlock($subcontent, true);
	}
	if(count($ip) > 0) {
		$content .= getHeader("beëindigde primaries", true);
		$subcontent = "";
		foreach($ip as $value) {
			$subcontent .=  getItem($value['titelmenu'],
															$value['titel'],
															$dom_primaries."prm_primary.php?pollid=".$value['id'],
															($_GET['pollid'] + $_POST['pollid']) == $value['id']);
		}
		$content .= getBlock($subcontent, true);
	}
	echo getBlock($content, $unfold);

	// ============================ Wie Kies Jij? ============================

	$unfold = ($part == PART_VOORKEURSSTEMMENWIJZER);
	echo getHeader("wie kies jij?", $unfold);
	$content  = getItem("introductie","Wat zijn Primaries?",$dom_wiekiesjij."index_vrk.php", $page == PAGE_VRK_INTRO);
	$content .= getItem("voorkeurstemhulp","Bepaal uw ideale kandidaat aan de hand van een korte lijst vragen",$dom_wiekiesjij."vrk_stemhulp.php", $page == PAGE_VRK_STEMHULP);
	$content .= getItem("kandidatenbrowser","Daarzoek de kandidaten op allerlei eigenschappen",$dom_wiekiesjij."vrk_wiekiesjij.php", $page == PAGE_VRK_FILTER);
	$content .= getItem("kandidaten zoekmachine","Doorzoek de persoonlijke websites van de kandidaten",$dom_wiekiesjij."vrk_searchres.php", $page == PAGE_VRK_SEARCH);
	$content .= getItem("voorbeeld enquête","Een voorbeeld van de enquete gestuurd aan de kandidaten",$dom_wiekiesjij."vrk_enquete.php", $page == PAGE_VRK_ENQUETE);
	$content .= getItem("in het nieuws","Wie Kies Jij? in het nieuws",$dom_wiekiesjij."vrk_inhetnieuws.php", $page == PAGE_VRK_INHETNIEUWS);
	$content .= getItem("persinformatie","Informatie oor de pers",$dom_wiekiesjij."vrk_pers.php", $page == PAGE_VRK_PERSINFO);
	$content .= getItem("colofon","Colofon",$dom_wiekiesjij."vrk_colofon.php", $page == PAGE_VRK_COLOFON);

	echo getBlock($content, $unfold);

	// ============================ Voorkeurstemmert ============================

	$unfold = ($part == PART_VOORKEURSSTEMMER);
	echo getHeader("voorkeurstemmer", $unfold);
	$content  = getItem("introductie","De voorkerustemmer",$dom_primaries."index_vrkst.php", $page == PAGE_VRKST_INTRO);
	$content .= getItem("stemmenoverzicht","Stemmenoverzicht",$dom_primaries."vrkst_overzicht.php", $page == PAGE_VRKST_OVERZICHT);
	$content .= getItem("stempagina","Stempagina",$dom_primaries."vrkst_vote.php", $page == PAGE_VRKST_VOTE);

	echo getBlock($content, $unfold);

	// ============================ Voorkeurstemmert ============================

	$unfold = ($part == PART_FORMATEUR);
	echo getHeader("de gekozen formateur", $unfold);
	$content  = getItem("introductie","De gekozen formateur",$dom_primaries."index_form.php", $page == PAGE_INTRO);

	echo getBlock($content, $unfold);

	echo "</div>";

?>


