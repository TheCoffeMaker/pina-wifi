<?php 

if (isset($_GET[force])) {
echo "<pre>Iniciando conexi&oacute;n 3G. Esto puede tomar un minuto y requiere refrescar manualmente esta p&aacute;gina. Revise los <a href=\"logs.php\"><b>Logs</b></a> para obtener detalles.</pre>";
exec("echo /www/pineapple/3g/3g.sh | at now");}

if (isset($_GET[enablekeepalive])) {
	if (exec("grep 3g-keepalive.sh /etc/crontabs/root") == "") {
		exec("echo '*/5 * * * * /www/pineapple/3g/3g-keepalive.sh' >> /etc/crontabs/root");
		echo "<pre>Script de conexi&oacute;n 3G persistente agregado a las Tareas Cron. Aseg&uacute;rese de habilitar el servicio Cron en <a href='jobs.php'><b>Tareas</b></a>.</pre>";
	} else {
		echo "<pre>Script de conexi&oacute;n 3G persistente ya existe en Crontab. Revise <a href='jobs.php'><b>Tareas</b></a> para resolver problemas.</pre>";
	}
}

if (isset($_GET[disablekeepalive])) {
	exec("sed -i '/3g-keepalive.sh/d' /etc/crontabs/root");
	echo "<pre>Script de conexi&oacute;n 3G persistente borrado de las Tareas Cron. Revise <a href='jobs.php'><b>Tareas</b></a></pre>";
}



$auto3g = (exec("grep 3g.sh /etc/rc.local"));

if (isset($_GET[enable])) {

	if (exec("grep 3g.sh /etc/rc.local") == "") {
		exec("sed -i '/exit 0/d' /etc/rc.local");
		exec("echo /www/pineapple/3g/3g.sh >> /etc/rc.local");
		exec("echo exit 0 >> /etc/rc.local");
		echo "<pre>Conexi&oacute;n 3G en el arranque activada.</pre>";
		$auto3g = "true";
	} else {
		echo "<pre>La conexi&oacute;n 3G en el arranque ya se encuentra habilitada en rc.local, no se hacen cambios.</pre>";
	}
}                              

if (isset($_GET[disable])) {
	exec("sed -i '/3g.sh/d' /etc/rc.local");
	echo "<pre>Conexi&oacute;n 3G en el arranque desactivada.</pre>";
	$auto3g = "";                  
}
?>

<html>
<head>
<?php if(isset($_GET[goback])){ 
echo "<meta http-equiv=\"refresh\" content=\"0; url=/pineapple/\">";
} ?>

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


if ($auto3g != ""){
echo "&nbsp;Conexi&oacute;n 3G en el arranque actualmente <font color=\"lime\"><b>activa</b></font>.&nbsp; | <a href=\"3g.php?disable&disablekeepalive\"><b>Desactivar</b></a><br />";
} else { echo "&nbsp;Conexi&oacute;n 3G en el arranque actualmente <font color=\"red\"><b>inactiva</b></font>. | <a href=\"3g.php?enable\"><b>Activar</b></a><br />"; }

if (exec("grep 3g-keepalive.sh /etc/crontabs/root") == "") {
echo "Conectividad 3G persistente actualmente <font color='red'><b>inactiva</b></font>. | <a href='3g.php?enablekeepalive&enable'><b>Activar</b></a><br />";
} else { echo "Conectividad 3G persistente actualmente <font color='lime'><b>activa</b></font>.&nbsp; | <a href='3g.php?disablekeepalive'><b>Desactivar</b></a><br />"; }

echo "<br /><a href=\"3g.php?force\"><b>Forzar</b></a> la conexi&oacute;n 3G: Esto ejecutar&aacute; el script 3G a continuaci&oacute;n ahorrando un posible reinicio de la pi&ntilde;a. <font color='orange'><small>Experimental</small></font><br /><br />";

echo "<b>Conexiones USB:</b> <a href='3g.php'><small>refrescar</small></a><br />";
echo exec("lsusb"); 

?>
<pre>


<?php
$filename = "/www/pineapple/3g/3g.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Configuraci&oacute;n de Banda Ancha M&oacute;vil:</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='140' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/3g/3g.sh'>
<br><input type='submit' value='Actualizar script 3G'>
</form>";
?>

<pre>
<a name="ifconfig">
<b>Configuraci&oacute;n de Interfaz:</b> <a href='3g.php#ifconfig'><small>refrescar</small></a><br />
<? $cmd="ifconfig";
exec ($cmd, $output);
foreach($output as $outputline) {
echo ("$outputline\n");}
?>


<b>Ayuda</b><br />
Si se habilita, este script se ejecuta en el arranque. Tambi&eacute;n se puede forzar su ejecuci&oacute;n.

La conexi&oacute;n de Banda Ancha requiere un m&oacute;dem USB 3G / 4G compatible.

La conexi&oacute;n se realiza en 2 fases. En la primera, el m&oacute;dem debe activarse, luego la configuraci&oacute;n de red establece los par&aacute;metros utilizados pppd y gchat.
Ya que la mayor&iacute;a de m&oacute;dems 3G / 4G se identifican como dispositivos CD-ROM o de Almacenamiento USB, se ejecuta un script de activaci&oacute;n que normalmente usa usb_modeswitch o sdparm.
La activaci&oacute;n fuerza al dispositivo USB a revelar su componente m&oacute;dem.
El componente m&oacute;dem es configurado como un dispositivo USB Serial, normalmente /dev/ttyUSB0, el cual es direccionado por la configuraci&oacute;n de red.

La configuraci&oacute;n de red especifica la interfaz como WAN2. Los protocolos GSM y CDMA est&aacute;n soportados. ifconfig normalmente muestra la interfaz como 3g-wan2.
El programa pppd es responsable por realizar la conexi&oacute;n punto-a-punto con el dispositivo USB Serial device. La configuraci&oacute;n est&aacute; en /etc/ppp/options
Comgt es responsable por hablarle al m&oacute;dem. Los comandos de m&oacute;dem EVDO y 3G (GSM) se encuentran especificados en /etc/chatscripts/
Para la mayor&iacute;a, ninguno de estos archivos requieren modificarse. 

El soporte para los m&oacute;dems aparte de los que se encuentran listados es experimental, aunque se puede encontrar ayuda en los foros de Jasager forums. La mayor&iacute;a de m&oacute;dems USB comparten configuraciones similares.
Scripts de conexi7oacute;n 3G con soporte adicional para otros m&oacute;dems puede encontrarse en wifipineapple.com 

Adicionalmente, se encuentra disponible un script de conectividad persistente (3G-KeepAlive), el cual peri&oacute;dicamente verifica la conectividad a Internet y la restablece si es necesario.
Esta verificaci&oacute;n se hace intentando enviar 3 pings a 8.8.8.8. Si ninguno es exitoso, se ejecuta un "ifup wan".

</td></tr>
<tr><td>

</pre>
</body>
</html>
