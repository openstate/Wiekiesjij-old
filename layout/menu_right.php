<?php

  	require_once("classes/dbquery_class.php");
	require_once("def/globals.php");

	if (isset($HTTP_COOKIE_VARS[COOKIE_NAME])) {
	  $region = $HTTP_COOKIE_VARS[COOKIE_NAME];
	  if (is_numeric($region)) {
	    $sql = "SELECT content FROM `cache_percentages` WHERE provincie=$region";
	    $q = new DBQuery($sql);
	    $r = $q->fetch();
	    $cnt = $r['content'];
	  }
       }

?>

    <div class="blokr">
      <img src="images/percentages.gif" alt="" />
        <?php /*echo $cnt*/ ?>
      <p>&nbsp;</p>
    </div>
		    
    <div class="blokr">
      <img src="images/nieuws.gif" alt="" />
      <p>Om de onderstaande bestanden te bekijken, is Microsoft Powerpoint nodig.</p>
      <ul style="margin: 15px; padding: 0px;"> <!-- list-style: none; -->
        <li style="margin: 0px; padding: 0px;"><a href="./content/Resultaten%20analyse%20enquetes%20PS-1.ppt">Diverse statistieken per partij</a></li>
        <li style="margin: 0px; padding: 0px;"><a href="./content/Resultaten%20Eerste%20Kamer.ppt">Een nieuwe toekomst voor de Eerste Kamer: een voorspelling van de kandidaat Statenleden</a></li>
      </ul> 

    </div>
				
    <div class="blokr">
      <img src="images/statistieken.gif" alt="" />
      <p>&nbsp;</p>
    </div>