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
<?php
if ($HTTP_POST_VARS['mailcode']) {
  $sql = "SELECT email FROM ns_groslijst WHERE writetothem=1 AND mailcode='".$HTTP_POST_VARS['mailcode']."'";
  $q = new DBQuery($sql);
  if ( $q->numRows() != 1) {
    $error = "Kandidaat wenst geen mail te ontvangen";
  } else {
    $r = $q->fetch();
    if (preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_.\-]*@([a-zA-Z0-9]+\.)*[a-zA-Z0-9][a-zA-Z0-9\-]+\.([a-zA-Z]{2,6})$/", $r['email'])) {
      $aan = $r['email'];
      $title = "Reactie via mailformulier van www.wiekiesjij.nl";
    } else {
      	$error = "Kandidaat wenst geen mail te ontvangen";
    }
  }
} else {
  $aan = "feedback_kiezer@wiekiesjij.nl, \"Godart van Gendt\" <godartvangendt@gmail.com>";
  $title = "Vraag over wie kies jij?";
}


$naam = $HTTP_POST_VARS["naam"];
$email = $HTTP_POST_VARS["email"];
$omschrijving = $HTTP_POST_VARS["omschrijving"];

$from = "\"$naam\" <$email>";
$date = date( 'r' );
$headers = <<<END
From: $from
Date: $date
X-Mailer: PHP v$phpversion
MIME-Version: 1.0
Content-Type: text/plain; charset="iso-9959-1"
END;

$content = <<<END

$omschrijving

END;

	if (!preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_.\-]*@([a-zA-Z0-9]+\.)*[a-zA-Z0-9][a-zA-Z0-9\-]+\.([a-zA-Z]{2,6})$/", $email)) {
		echo "<h1>Fout bij versturen!</h1>";
		echo "<p>Opgegeven emailadres klopt niet!</p>\n";
	} else if (isset($error)) {
		echo "<h1>Fout bij versturen!</h1>\n";
		echo "<p>$error</p>\n";
	} else {
		mail($aan, $title, $content, $headers);
		$foobar = new DBQuery("UPDATE ns_groslijst SET keergemaild= keergemaild+1 WHERE writetothem=1 AND mailcode='".$HTTP_POST_VARS['mailcode']."'");
		echo "<h1>Wat vind jij?</h1>";
		echo "<p> Uw mail is verstuurd </p>";

		echo "<!--\n";
		echo "$aan \n$headers \n$content \n$title\n";
		echo "-->\n";

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
