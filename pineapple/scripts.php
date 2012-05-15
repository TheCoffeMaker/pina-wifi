<html>
<head>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<script  type="text/javascript" src="jquery.min.js"></script>
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
}


$filename = "/www/pineapple/scripts/cleanup.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Script de Limpieza</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='100' rows='14' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/scripts/cleanup.sh'><input type='submit' value='Actualizar Script'>
</form>";

$filename = "/www/pineapple/ssh/ssh-keepalive.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Script de Persistencia SSH</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='100' rows='14' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/ssh/ssh-keepalive.sh'><input type='submit' value='Actualizar Script'>
</form>"; 

$filename = "/www/pineapple/3g/3g-keepalive.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Script de Persistencia 3G</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='100' rows='14' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/3g/3g-keepalive.sh'><input type='submit' value='Actualizar Script'>
</form>"; 

$filename = "/www/pineapple/scripts/user.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Script de Usuario</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='100' rows='14' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/scripts/user.sh'><input type='submit' value='Actualizar Script'>
</form>"; 

?>
</pre>
</body>
</html>
