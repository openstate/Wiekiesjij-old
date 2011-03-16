<?php

	require_once("../classes/dbquery_class.php");

	function dump($message, $color = "black")
	{
		echo "\n<pre style=\"white-space: normal; margin: 0; padding: 0; color: ".$color."\">".$message."</pre>";
	}


	if(!isset($_GET['supergeheimwachtwoord'])){
		die();
	}

	dump("Sind 2006-10-20 13:55<br/>");
	$starttime = mktime(13, 55, 0, 10, 20, 2006);
	$timediff = mktime() - $starttime;
	$hourssince = $timediff/60/60;


	$q = new DBQuery("SELECT COUNT(*) AS numcache FROM cache_profile");
	$r = $q->fetch();
	$numcache = $r['numcache'];

	$q = new DBQuery("SELECT SUM(hits) AS numhits FROM hits_profile");
	$r = $q->fetch();
	dump("Aantal bekeken profiles: " . $r['numhits'] . " ($numcache cached, ".floor($r['numhits']/$hourssince)." per uur, ".floor($r['numhits']/$hourssince*24)." per etmaal)");

	$q = new DBQuery("SELECT COUNT(*) AS numcache FROM cache_query");
	$r = $q->fetch();
	$numcache = $r['numcache'];

	$q = new DBQuery("SELECT SUM(hits) AS numhits FROM hits_query");
	$r = $q->fetch();
	dump("Aantal uitgevoerde queries: " . $r['numhits'] . " ($numcache cached, ".floor($r['numhits']/$hourssince)." per uur, ".floor($r['numhits']/$hourssince*24)." per etmaal)");

	$q = new DBQuery("SELECT SUM(hits) AS numhits FROM hits_votehelp");
	$r = $q->fetch();
	dump("Aantal uitgevoerde stemhulp: " . $r['numhits']);

	dump("<a href=\"http://www.wiekiesjij.nl/def/rinze.php?supergeheimwachtwoord&kandidaten\">toon kandidaten</a>");
	dump("<a href=\"http://www.wiekiesjij.nl/def/rinze.php?supergeheimwachtwoord&ranking\">toon ranking</a>");
	dump("<a href=\"http://www.primaries.nl/def/rinze.php?supergeheimwachtwoord&votes\">toon stemmen</a>");

	if(isset($_GET['ranking'])) {
		$q = new DBQuery("SELECT hits, voornaam, voorletters, tussenvoegsel, achternaam FROM hits_profile LEFT JOIN ns_groslijst ON ns_groslijst.id = hits_profile.id ORDER BY hits DESC LIMIT 50");
		while($r = $q->fetch()){
			if($r['voornaam'] == '') {
				$naam = $r['voorletters'];
			}
			else {
				$naam = $r['voornaam'];
			}

			if($r['tussenvoegsel'] != '') {
				$naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];

			dump($r['hits'] .": ".$naam);
		}
	}

	if(isset($_GET['kandidaten'])) {
		echo "<table style=\"margin: 20px; width: 1000px; border: 1px solid black; border-collapse: collapse\">";
		$q = new DBQuery("SELECT ns_groslijst.*, ns_fracties.naam FROM ns_groslijst LEFT JOIN ns_fracties ON(ns_fracties.id=ns_groslijst.fractie_id) WHERE 1 ORDER BY lastupdate DESC");
		while($r = $q->fetch()){
			if($r['voornaam'] == '') {
				$naam = $r['voorletters'];
			}
			else {
				$naam = $r['voornaam'];
			}

			if($r['tussenvoegsel'] != '') {
				$naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];

			if($r['foto'] == '') {
				$r['foto'] = "_dummy.jpg";
			}

			echo "<tr>";

			echo "<td style=\"padding: 5px; vertical-align: top; border: 1px solid gray\">";
			echo "<a href=\"../aanmelden.php?code=".$r['code']."\" style=\"border: none\" ><img style=\"border: none\" src=\"../voorkeurstemmer/50/".$r['foto']."\"/></a>";
			echo "</td>";

			echo "<td style=\"padding: 5px; vertical-align: top; border: 1px solid gray\">";
			dump(substr($r['naam'],0, 5));
			echo "</td>";

			echo "<td style=\"width: 200px; padding: 5px; vertical-align: top; border: 1px solid gray\">";
			dump("<a href=\"mailto:".$r['email']."\">".$naam."</a>");
			echo "</td>";

			echo "<td style=\"padding: 5px; vertical-align: top; border: 1px solid gray\">";
			dump($r['lastupdate']);
			//dump(substr($r['lastupdate'],0,4)."-".substr($r['lastupdate'],5,2)."-".substr($r['lastupdate'],8,2)."\n".substr($r['lastupdate'],8,2).":".substr($r['lastupdate'],10,2).":".substr($r['lastupdate'],12,2));
			echo "</td>";

			echo "<td style=\"padding: 5px; vertical-align: top; border: 1px solid gray\">";
			dump($r['feedback']);
			echo "</td>";
		}

		echo "</table>";
	}

	if(isset($_GET['votes'])) {
		$q = new DBQuery("SELECT COUNT(*) AS aantal FROM ns_stemmen WHERE pollid = 666");
		$r = $q->fetch();
		dump("Aantal stemmen: ".$r['aantal']);

		$q = new DBQuery("SELECT ns_stemmen.*,voornaam, voorletters, tussenvoegsel, achternaam  FROM ns_stemmen LEFT JOIN ns_groslijst ON ns_stemmen.antwoordid = ns_groslijst.id WHERE pollid = 666 ORDER BY ts DESC LIMIT 20");
		while($r = $q->fetch()){
			if($r['voornaam'] == '') {
				$naam = $r['voorletters'];
			}
			else {
				$naam = $r['voornaam'];
			}

			if($r['tussenvoegsel'] != '') {
				$naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];
			dump($r['ts']." : ". str_pad($r['stemmerid'],10)." : ".$naam);
		}

		$q = new DBQuery("SELECT COUNT(antwoordid) AS aantal, voornaam, voorletters, tussenvoegsel, achternaam  FROM ns_stemmen LEFT JOIN ns_groslijst ON ns_stemmen.antwoordid = ns_groslijst.id WHERE pollid = 666 GROUP BY antwoordid ORDER BY aantal DESC LIMIT 20");
		while($r = $q->fetch()){
			if($r['voornaam'] == '') {
				$naam = $r['voorletters'];
			}
			else {
				$naam = $r['voornaam'];
			}

			if($r['tussenvoegsel'] != '') {
				$naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];
			dump($r['aantal']." : ".$naam);
		}
	}


?>