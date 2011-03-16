<?php
	require_once "def/globals.php";
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_FAQ;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php include "layout/xhtml_header.php"; ?>

		<title>Stichting Het Nieuwe Stemmen: Vragen over Wie Kies Jij</title>
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
 require_once("classes/dbquery_class.php");
 $id = 3;

 $q = new DBQuery("SELECT content FROM ns_static WHERE id=$id");
 $r = $q->fetch();
 echo base64_decode($r['content']);
?>
			</div>

			<div id="col_right">
				<?php include "layout/menu_right.php"; ?>
			</div>

			<?php include "layout/page_footer.php"; ?>

		</div>

	</body>
</html>
