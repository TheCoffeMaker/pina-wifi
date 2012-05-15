<html>
<head>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<script  type="text/javascript" src="includes/jquery.min.js"></script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('includes/navbar.php'); ?>
<pre>
<?php
$filename =$_POST['filename'];
$newdata = $_POST['newdata'];

if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
 $fw = fopen($filename, 'w') or die('No se puede abrir el archivo!');
 $fb = fwrite($fw,stripslashes($newdata)) or die('No se puede modificar el archivo');
 fclose($fw);
 echo "Actualizado " . $filename . "<br /><br />";
} ?>
</pre>
<pre>
<b>salida de lsusb:</b><br />
<?php
$exec = exec("lsusb", $return);
foreach ($return as $line) {
echo("$line <br />");
}
?>
</pre>
<pre>

<table border="0" width="100%" >
<tr><td width="700">
<br /> <br />
<tr><td>

<?php
$filename = "/etc/config/fstab";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Fstab</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/etc/config/fstab'>
<br><input type='submit' value='Actualizar Fstab'>
</form>";
?>
</td><td valign="top" align="left">
Configuraci&oacute;n Fstab. 
</td></tr>
<tr><td>

</pre>
</body>
</html>
