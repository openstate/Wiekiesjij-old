<div id="footer">
<img style="margin: 2px; float: right;" alt="W3C CSS" src="layout/w3c_css.gif"/>
<img style="margin: 2px; float: right;" alt="W3C XHTML 1.1" src="layout/w3c_xhtml.gif"/>
<a href="http://www.elit.nl"><img alt="Elit" style="margin: 2px; float: right" src="layout/elit.gif"/></a>
<p>©
<?php
	$startyear = 2006;
	$year = date('Y');

	if ($startyear == $year) {
		echo $year;
	}
	else {
		echo $startyear . " - " . $year;
	}
?>
 Stichting Het Nieuwe Stemmen - Alle rechten voorbehouden</p>
</div>