	<?php

		if(strpos($_SERVER['HTTP_HOST'], "wiekiesjij") !== false) {
			$background = "background-image: url('layout/background_header_ipp.png');";
		}

		echo "<div id=\"header\" ondblclick=\"toggleanim()\" style=\"$background\">";

		if ($part == PART_PRIMARIES) {
			echo "<img onclick=\"location.href='index.php'\" style=\"cursor: pointer; position: absolute; left: 45px; top: 70px;\" id=\"phone\" alt=\"phone\" src=\"layout/phone.png\"/>";
			echo "<h1>one mobile one vote</h1>";
			echo "<h2>Primaries</h2>";
		}
		elseif ($part == PART_VOORKEURSSTEMMENWIJZER) {
			echo "<img onclick=\"location.href='index.php'\" style=\"cursor: pointer; position: absolute; left: 45px; top: 70px;\" id=\"phone\" alt=\"phone\" src=\"layout/phone.png\"/>";
			//echo "<img onclick=\"location.href='index.php'\" style=\"cursor: pointer; position: absolute; left: 10px; top: 75px;\" id=\"phone\" alt=\"phone\" src=\"layout/people.png\"/>";
			echo "<h1>voorkeurstemhulp</h1>";
			echo "<h2>Wie Kies Jij?</h2>";
		}
		else {
			echo "<img onclick=\"location.href='index.php'\" style=\"cursor: pointer; position: absolute; left: 45px; top: 70px;\" id=\"phone\" alt=\"phone\" src=\"layout/phone.png\"/>";
			echo "<h1>one mobile one vote</h1>";
			echo "<h2>Stichting Het Nieuwe Stemmen</h2>";
		}

		echo "</div>";
		

	?>
