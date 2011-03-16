<?php
/*include("../classes/dbquery_class.php");

function d($m) {
	//echo "<pre style=\"padding: 0; margin: 0px\">".$m."</pre>";
	echo $m."\n";
}

$fr["VVD"] = 1;
$fr["PvdA"] = 2;
$fr["CDA"] = 3;
$fr["SP"] = 4;
$fr["GroenLinks"] = 5;
$fr["D66"] = 6;
$fr["LPF"] = 7;
$fr["ChristenUnie"] = 8;
$fr["SGP"] = 9;
$fr["EenNL"] = 10;
$fr["PVV-Wilders"] = 11;
$fr["Partij voor de Dieren"] =  12;


$qstr = "SELECT ns_fracties.naam as frac, code, voornaam, voorletters, tussenvoegsel, achternaam FROM `ns_groslijst` left join ns_fracties ON fractie_id = ns_fracties.id";
$q = new DBQuery($qstr);
while($r = $q->fetch()) {
	$s = $r['frac'].";";
	$s .= $r['code'] .";";
	$s .= $r['voornaam'] .";";
	$s .= $r['voorletters'] .";";
	$s .= $r['tussenvoegsel'] .";";
	$s .= $r['achternaam'] .";";

	d($s);
}

d("EenNL");
echo "\n";
$qstr = "SELECT * FROM ns_groslijst WHERE fractie_id=10";
$q = new DBQuery($qstr);

while($r = $q->fetch()) {
	$s = $r['code'].";";

	if($r['voornaam'] != '') {
		$s .= $r['voornaam'];
	}
	else {
		$s .= $r['voorletters'];
	}

	if($r['tussenvoegsel'] != '') {
		$s .= " ".$r['tussenvoegsel'];
	}
	$s .= " ".$r['achternaam'];

	$s .= ";".$r['email'];

	d($s);
}


echo "\n";echo "\n";

d("PvdA");
echo "\n";
$qstr = "SELECT * FROM ns_groslijst WHERE fractie_id=2";
$q = new DBQuery($qstr);

while($r = $q->fetch()) {
	$s = $r['code'].";";

	if($r['email'] != '') {
		if($r['voornaam'] != '') {
			$s .= $r['voornaam'];
		}
		else {
			$s .= $r['voorletters'];
		}

		if($r['tussenvoegsel'] != '') {
			$s .= " ".$r['tussenvoegsel'];
		}
		$s .= " ".$r['achternaam'];

		$s .= ";".$r['email'];

		d($s);
	}
}

echo "\n";echo "\n";

d("Gristenen");
echo "\n";
$qstr = "SELECT * FROM ns_groslijst WHERE fractie_id=8";
$q = new DBQuery($qstr);

while($r = $q->fetch()) {
	$s = $r['code'].";";

	if($r['email'] != '') {
		if($r['voornaam'] != '') {
			$s .= $r['voornaam'];
		}
		else {
			$s .= $r['voorletters'];
		}

		if($r['tussenvoegsel'] != '') {
			$s .= " ".$r['tussenvoegsel'];
		}
		$s .= " ".$r['achternaam'];

		$s .= ";".$r['email'];

		d($s);
	}
}

echo "\n";echo "\n";

d("LPF");
echo "\n";
$qstr = "SELECT * FROM ns_groslijst WHERE fractie_id=7";
$q = new DBQuery($qstr);

while($r = $q->fetch()) {
	$s = $r['code'].";";

	if($r['email'] != '') {
		if($r['voornaam'] != '') {
			$s .= $r['voornaam'];
		}
		else {
			$s .= $r['voorletters'];
		}

		if($r['tussenvoegsel'] != '') {
			$s .= " ".$r['tussenvoegsel'];
		}
		$s .= " ".$r['achternaam'];

		$s .= ";".$r['email'].";".$r['ipadres'];

		d($s);
	}
}*/



?>