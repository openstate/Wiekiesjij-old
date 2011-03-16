<?php
	require_once "def/globals.php";
	require_once("classes/dbquery_class.php");
	$part = PART_VOORKEURSSTEMMENWIJZER;
//	$page = PAGE_VRK_WAT_VIND_JIJ;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>

		<title>Stichting Het Nieuwe Stemmen: Mail een kandidaat</title>
	</head>

	<body id="base">

		<div id="container">

			<?php include "layout/page_header.php"; ?>

			<?php include "layout/menu_top.php"; ?>

			<div id="col_left">
				<?php include "layout/menu_left.php"; ?>
			</div>

			<div id="col_mid">
<?php
if ($HTTP_GET_VARS['mailcode']) {
  $sql = "SELECT email, achternaam, tussenvoegsel, voornaam, voorletters FROM ns_groslijst WHERE writetothem=1 AND mailcode='".$HTTP_GET_VARS['mailcode']."'";
  $q = new DBQuery($sql);
  if ( $q->numRows() != 1) {
    $error = "Kandidaat wenst geen mail te ontvangen";
  } else {
    $r = $q->fetch();

			if($r['voornaam'] != '') {
				$naam = $r['voornaam'];
			}
			else {
				$naam = $r['voorletters'];
			}
			
			if($r['tussenvoegsel']) {
			    $naam .= " ".$r['tussenvoegsel'];
			}

			$naam .= " ".$r['achternaam'];

    if (!preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_.\-]*@([a-zA-Z0-9]+\.)*[a-zA-Z0-9][a-zA-Z0-9\-]+\.([a-zA-Z]{2,6})$/", $r['email'])) {
      	$error = "$naam wenst geen mail te ontvangen";
    }

  }
} else {
  $error = "Kandidaat wenst geen mail te ontvangen";
}

if ($error) {
  echo "<h1>Error</h1>\n";
  echo "<p>$error</p>\n";
  echo "$sql\n";
} else {
  ?>
<h1>Mail <?php echo $naam; ?></h1>

<form action="vrk_mailform.php" method="post">
<input type="hidden" name="mailcode" value="<?php echo $HTTP_GET_VARS['mailcode']; ?>">
<table>
  <tr><td>Naam:</td><td> <input type="text" name="naam" value="" style="border: 1px solid black" size=60> </td></tr>
  <tr><td>Email:</td><td> <input type="text" name="email" value="" style="border: 1px solid black" size=60> </td></tr>
  <tr><td valign="top">Omschrijving:</td><td> <textarea name="omschrijving" cols=45 rows=20 style="border: 1px solid black"></textarea></td></tr>
  <tr><td colspan=2 style="text-align:center;"> <input type="submit" value="Versturen" style="border: 1px solid black"></td></tr>
</table>

<?php
}
?>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
