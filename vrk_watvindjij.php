<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_WAT_VIND_JIJ;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>

		<title>Stichting Het Nieuwe Stemmen: Wat vind jij</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">
<h1>Wat vind jij?</h1>
<p>Heb je nog vragen of opmerkingen? Of wil je in de toekomst zelf meewerken aan initiatieven van Stichting "Het Nieuwe Stemmen"? Wij zijn
benieuwd naar jouw mening over "Wie kies jij?".</p>

<form action="vrk_mailform.php" method="post">
<input type="hidden" name="mailcode" value="">
<table>
  <tr><td>Naam:</td><td> <input type="text" name="naam" value="" style="border: 1px solid black" size=60> </td></tr>
  <tr><td>Email:</td><td> <input type="text" name="email" value="" style="border: 1px solid black" size=60> </td></tr>
  <tr><td valign="top">Omschrijving:</td><td> <textarea name="omschrijving" cols=45 rows=20 style="border: 1px solid black"></textarea></td></tr>
  <tr><td colspan=2 style="text-align:center;"> <input type="submit" value="Versturen" style="border: 1px solid black"></td></tr>
</table>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
