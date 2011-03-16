      <div id="footer">
              <p>Wie kies jij is een initiatief van Stichting <a href="http://www.hetnieuwestemmen.nl/">Het Nieuwe Stemmen</a> in samenwerking met 
	        <a href="http://www.publiek-politiek.nl/">Instituut voor Publiek en Politiek (IPP)</a> | 
<?php
	$startyear = 2007;
	$year = date('Y');

	if ($startyear == $year) {
		echo $year;
	}
	else {
		echo $startyear . " - " . $year;
	}
?>

		| <a href="vrk_contact.php">Contact</a> | <a href="vrk_colofon.php">Disclaimer</a></p>
		
		      </div>
		      