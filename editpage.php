<?php
	require_once "def/globals.php";
	require_once("classes/dbquery_class.php");
	$part = PART_VOORKEURSSTEMMENWIJZER;
	$page = PAGE_VRK_WAT_VIND_JIJ;
       $time = time();

  if(is_numeric($_POST['page'])) {
    $p = md5("95043198fdbe18fdcd65406132512d3d" . $_POST['t']);
    if ($p == $_POST['pwd']) {
      $cnt = str_replace("\\\"","\"", $_POST['content']);
      $cnt = str_replace("\\'","'", $cnt);
      $foobar = new DBQuery("UPDATE ns_static SET content='".base64_encode($cnt)."' WHERE id=".$_POST['page']);
      $page = $_POST['page'];
      echo "Opgeslagen<br />\n";
    } else {
      echo "Wachtwoord was verkeerd!<br />\n";
    }
  } 


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CMS-je</title>
		<script src="javascript/md5.js" type="text/javascript"></script>
		<script language="JavaScript" type="text/javascript">

<!--
function checkform ( form )
{
  form.pwd.value = hex_md5(form.pwd.value + '<?php echo $time ?>');
  return true ;
}
//-->

		</script>
	</head>

	<body id="base">
<h1>Simple CMS</h1>
<p>Deze pagina is bedoelt voor admins van wiekiesjij.nl, als je dat niet bent, wordt je verzocht deze pagina te verlaten.</p>

Welk tekst-stuk wil je aanpassen?</p>
<form action="editpage.php" method="get">
 <select name="page">
<?php

$sql = "SELECT id,naam FROM ns_static";
$q = new DBQuery($sql);
while($r = $q->fetch()) {
  echo "<option value=".$r['id'].">".$r['naam']."</option>\n";
}

?>
 </select>
 <input type="submit" value="Openen">
</form>

<hr>

<?php

 if (is_numeric($_GET['page'])) {
   $q = new DBQuery("SELECT naam, content FROM ns_static WHERE id=".$_GET['page']);
   $r = $q->fetch();
   $naam = $r['naam'];
   $cnt = base64_decode($r['content']);
 }

 if (isset($_GET['page'])) { $page = $_GET{'page'}; }

?>


<strong>De pagina(<?php echo $naam; ?>):</strong>

<form action="editpage.php" method="post" onsubmit="return checkform(this);">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="t" value="<?php echo $time; ?>">
<textarea cols=120 rows=30 name="content"><?php echo $cnt?></textarea><br />
Wachtwoord: <input type="text" name="pwd" value="" size="50"><br />
<input type="submit" value="opslaan!">

<hr>
<p><strong>Huidige pagina:</strong></p>
<?php

 if (is_numeric($_GET['page'])) {
   $q = new DBQuery("SELECT content FROM ns_static WHERE id=$page");
   $r = $q->fetch();
   $cnt = base64_decode($r['content']);
   echo $cnt;
 }

?>
</form>

	</body>
</html>
