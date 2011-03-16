<?php

	require_once("../classes/dbquery_class.php");
	require_once("../def/globals.php");

	$myDirectory = opendir("a50/");
	    
	while($fname = readdir($myDirectory)) {
	    if($fname !="." && $fname!=".." && $fname!="_dummy.jpg" && strlen($fname) == 12 ) {
		echo "$fname, ";
		preg_match("/^(.*)\.([^.]*)$/", $fname, $match);
		$nw_fname1 = $match[1];
		$ext = $match[2];
		echo "$nw_fname1 -- $ext, ";
		$nw_fname = md5($nw_fname1);
		echo "$nw_fname -- $ext, ";
		$nw_f = $nw_fname .".".$ext;
		echo "$nw_f <br />\n";

/*		$sql = "SELECT * FROM ns_groslijst WHERE code='".$nw_fname1."'";
		$q = new DBQuery($sql);
		$res = $q->fetch(); 
		$sql = "UPDATE ns_groslijst SET nw_foto='".$nw_fname.".jpg' WHERE code='".$nw_fname1."'";
		echo "Code : $nw_fname1, Naam : ". $res['achternaam'] . ", Q = $sql<br />\n";

		$q = new DBQuery($sql);

		if(rename("a25/".$fname,"25/".$nw_f)) {
			print $fname." rename sucessfully to ".$nw_f."<br>";
		} else {
			print $fname." Failed to rename ".$nw_f."<br>";
		} 
		if(rename("a50/".$fname,"50/".$nw_f)) {
			print $fname." rename sucessfully to ".$nw_f."<br>";
		} else {
			print $fname." Failed to rename ".$nw_f."<br>";
		}
		if(rename("a100/".$fname,"100/".$nw_f)) {
			print $fname." rename sucessfully to ".$nw_f."<br>";
		} else {
			print $fname." Failed to rename ".$nw_f."<br>";
		}
		if(rename("a150/".$fname,"150/".$nw_f)) {
			print $fname." rename sucessfully to ".$nw_f."<br>";
		} else {
			print $fname." Failed to rename ".$nw_f."<br>";
		}  */
	    }
	}
	closedir($myDirectory);
	print "<hr>";
?>