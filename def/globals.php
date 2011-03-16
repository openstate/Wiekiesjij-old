<?php
	//elitversion = 0008, elitdate  2006-11-23 11:53

	// Site Parts
	$i = 1;

	define("PART_HETNIEUWESTEMMEN", $i++);

	define("PAGE_HNS_INTRO",$i++);
	define("PAGE_HNS_WIE",$i++);
	define("PAGE_HNS_CONTACT",$i++);


	define("PART_PRIMARIES", $i++);

	define("PAGE_PRM_INTRO",$i++);
	define("PAGE_PRM_VRAGEN",$i++);
	define("PAGE_PRM_PRIMARY",$i++);
	define("PAGE_PRM_PROFILE",$i++);


	define("PART_VOORKEURSSTEMMENWIJZER", $i++);

	define("PAGE_VRK_INTRO",$i++);
	define("PAGE_VRK_ENQUETE",$i++);
	define("PAGE_VRK_STEMHULP",$i++);
	define("PAGE_VRK_FILTER",$i++);
	define("PAGE_VRK_SEARCH",$i++);
	define("PAGE_VRK_INHETNIEUWS",$i++);
	define("PAGE_VRK_PERSINFO",$i++);
	define("PAGE_VRK_COLOFON",$i++);

	define("PART_VOORKEURSSTEMMER", $i++);

	define("PAGE_VRKST_INTRO",$i++);
	define("PAGE_VRKST_OVERZICHT",$i++);
	define("PAGE_VRKST_VOTE",$i++);

	define("PART_FORMATEUR", $i++);

	define("PAGE_INTRO",$i++);
	
	define("PAGE_VRK_WIE_ZIJN_WIJ", $i++);
	define("PAGE_VRK_WAAROM_WKJ", $i++);
	define("PAGE_VRK_FAQ", $i++);
	define("PAGE_VRK_WAT_VIND_JIJ", $i++);
	define("PAGE_VRK_CONTACT", $i++);

	define("COOKIE_NAME", "wkj_provincie");


	if(strpos($_SERVER['HTTP_HOST'], "gleufserver") === false && strpos($_SERVER['HTTP_HOST'], "snkd") === false) {
		define("SEARCH_TARGET","http://www.wiekiesjij.nl/vrk_searchres.php");
	}
	else {
		define("SEARCH_TARGET","http://gleufserver.balpol.tudelft.nl/hns/vrk_searchres.php");
	}

?>
