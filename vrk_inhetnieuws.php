<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_INHETNIEUWS;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>

		<title>In het nieuws</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">

			<h1>In het nieuws</h1>
			<p>Wiekiesjij.nl verscheen o.a. in de volgende media:</p>

			<h2>Radio</h2>

			<ul>
				<li><a href="http://www.bnr.nl/ShowNieuwsArtikel.asp?Context=S|8e3b4952bcabb63e|N|0&src=redactie&id=4152">Business News Radio (BNR)</a> vrijdag 20 oktober 2006, 07:20</li>

				<li><a href="http://cgi.omroep.nl/cgi-bin/streams?/radio1/bnn/bnnunited/vrijdag-21.rm">BNN-United</a> vrijdag 20 oktober 2006, 21:00<br>
				<i>na 4:30 minuten, te beluisteren met RealAudio.</i></li>
			</ul>

			<h2>Web</h2>
			<ul>
				<li><a href="http://www.computertotaal.nl/web/show/id=815339/contentid=63664">Computertotaal - Politiek poppetjes kiezen op wiekiesjij.nl</a> vrijdag 20 oktober 2006</li>
				<li><a href="http://www.sargasso.nl/archief/2006/10/24/wie-kies-jij/">Sargasso - Wie kies jij?</a> dinsdag 24 oktober 2006</li>
			</ul>

			<h2>Kranten</h2>
			<ul>
				<li><a href="http://www.pimaries.nl/doc/inhetnieuws/volkskrant_2006-10-20.pdf">Volkskrant - Het Nieuwe Stemmen zoekt mens achter de kandidaat</a> vrijdag 20 oktober 2006</li>
				<li><a href="http://www.primaries.nl/doc/inhetnieuws/trouw_2006-10-20.pdf">Trouw - Nieuwe website helpt bij bepalen voorkeurstem</a> vrijdag 20 oktober 2006</li>
			</ul>

			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
