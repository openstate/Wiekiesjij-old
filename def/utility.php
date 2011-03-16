<?php

	/*require_once "../classes/questionaire_class.php";
	require_once("../classes/dbquery_class.php");
	$ques = new Questionaire("wiekiesjij");
	$ques->questionaireConstructor();
	*/

	require_once("../classes/dbquery_class.php");

	function dump($message, $color = "black")
	{
		echo "\n<pre style=\"white-space: normal; margin: 0; padding: 0; color: ".$color."\">".$message."</pre>";
	}

	if(isset($_GET['leeftijden'])) {
		dump("Setting ages");
		$q = new DBQuery("UPDATE ns_groslijst SET leeftijd= DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(geboortedatum)), '%Y')+0");
	}


?>