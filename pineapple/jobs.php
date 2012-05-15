<?php 
$cronjobs = ( exec("ps -all | grep [c]ron"));
if(isset($_GET[start])){                     
exec("/etc/init.d/cron enable");
exec("/etc/init.d/cron start"); 
$cronjobs = "true";             
}                              
if(isset($_GET[stop])){
exec("/etc/init.d/cron stop");  
exec("/etc/init.d/cron disable");
$cronjobs = "";                  
}

if(isset($_GET[goback])){ header('Location: index.php'); } ?>
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
} 


if ($cronjobs != ""){
echo "Las Tareas Cron est&aacute;n actualmente <font color=\"lime\"><b>activas</b></font>. | <a href=\"jobs.php?stop\"><b>Desactivar</b></a><br />";
} else { echo "Tareas Cron <font color=\"red\"><b>inactivas</b></font>. | <a href=\"jobs.php?start\"><b>Activar</b></a><br />"; }


?>
<pre>

<table border="0" width="100%" >
<tr><td width="700">
<tr><td>

<?php
$filename = "/etc/crontabs/root";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Tareas Cron:</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/etc/crontabs/root'>
<br><input type='submit' value='Actualizar Crontab'>
</form>";
?>
</td><td valign="top" align="left">
<pre>Configuraci&oacute;n de Tareas Cron.

* * * * * comando a ejecutar
- - - - -
| | | | |
| | | | +- - - - d&iacute;a de la semana (0 - 6) (Domingo=0)
| | | +- - - - - mes (1 - 12)
| | +- - - - - - d&iacute;a del mes (1 - 31)
| +- - - - - - - hora (0 - 23)
+- - - - - - - - minuto (0 - 59)

Ejemplos:

Ejecutar mi_script.sh diariamente a las 2:30 AM
30 2 * * * /root/mi_script.sh

Ejecutar mi_script.sh cada 15 minutos
*/15 * * * * /root/mi_script.sh
 
</pre></td></tr>

<tr><td width="700">


<?php
$filename = "/etc/rc.local";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
    $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
      fclose($fh);
       echo "<b>Ejecutar en el Arranque:</b>
       <form action='$_SERVER[php_self]' method= 'post' >
       <textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
       <input type='hidden' name='filename' value='/etc/rc.local'>
       <br><input type='submit' value='Actualizar rc.local'>
       </form>";
?>




</td><td valign="top">
Este script se ejecuta en el arranque.
</td></tr>
</table>


</pre>
</body>
</html>
