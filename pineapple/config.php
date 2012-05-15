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
} ?>

<?php
if(isset($_POST[newSSID])){
	if(isset($_POST[newSSIDPersistent])){
		exec("echo \"$(sed 's/option ssid.*/option ssid ".$_POST[newSSID]."/g' /etc/config/wireless)\" > /etc/config/wireless");
		echo "Los cambios al SSID se han hecho persistentes. <br />";
	}
exec("hostapd_cli -p /var/run/hostapd-phy0 karma_change_ssid \"".$_POST[newSSID]."\"");
echo "SSID Karma cambiado a \"".$_POST[newSSID]."\" exitosamente. <br /><br />";

}

if(isset($_POST[ssidBW])){
	if(isset($_POST[addSSID])){
		exec("hostapd_cli -p /var/run/hostapd-phy0 karma_add_ssid ".$_POST[ssidBW]);
		echo "Se ha agregado \"".$_POST[ssidBW]."\" a la lista. <br /><br />";
	}
        if(isset($_POST[removeSSID])){
                exec("hostapd_cli -p /var/run/hostapd-phy0 karma_del_ssid ".$_POST[ssidBW]);
                echo "Se ha borrado \"".$_POST[ssidBW]."\" de la lista. <br /><br />";
        }

}

if(isset($_POST[macBW])){
	if(isset($_POST[addMAC])){
		exec("hostapd_cli -p /var/run/hostapd-phy0 karma_add_black_mac  ".$_POST[macBW]);
		echo "Se ha agregado \"".$_POST[macBW]."\" a la lista. <br /><br />";
	}
        if(isset($_POST[removeMAC])){
                exec("hostapd_cli -p /var/run/hostapd-phy0 karma_add_white_mac ".$_POST[macBW]);
                echo "Se ha borrado \"".$_POST[macBW]."\" de la lista. <br /><br />";
        }

}
?>

<?php
if(isset($_GET[resetButton])){

if($_GET[resetButton] == "enable"){
echo "Bot&oacute;n Reset habilitado.";
exec("sh config/resetButton.sh enable");
exec("echo enabled > config/resetButtonStatus");

}
if($_GET[resetButton] == "disable"){
echo "Bot&oacute;n Reset deshabilitado.";
exec("sh config/resetButton.sh disable");
exec("echo disabled > config/resetButtonStatus");

}

}
$resetButton = trim(file_get_contents("config/resetButtonStatus"));
?>
<table border="0" width="100%">
<tr><td width="700">
<td valign="top" align="left">
Configuraci&oacute;n del Bot&oacute;n WPS.
</tr></td>
<tr><td>
Bot&oacute;n Reset <?php if($resetButton == "enabled") echo "<font color=lime>habilitado</font>"; else echo "<font color=red>deshabilitado</font>" ?>. | <?php if($resetButton == "enabled") echo "<a href=\"$_SERVER[PHP_SELF]?resetButton=disable\">Deshabilitar</a>"; else echo "<a href=\"$_SERVER[PHP_SELF]?resetButton=enable\">Habilitar</a>"; ?>
<br /><br />
<?php
$filename = "/www/pineapple/config/wpsScript.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<a name='wpsScript'><b>Script personalizado que se ejecuta al presionar el bot&oacute;n WPS</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/config/wpsScript.sh'>
<br><input type='submit' value='Actualizar script WPS'>
</form>";
?>

</tr></td>

<tr><td>
<tr><td width="700">
<td valign="top" align="left">
Configuraci&oacute;n Karma.
</tr></td>
<tr><td>
<b>Cambiar SSID Karma</b><br />
<form action='<?php echo $_SERVER[php_self] ?>' method= 'post' >
<input type="text" name="newSSID" size='25' value="Nuevo SSID" onFocus="if(this.value == 'Nuevo SSID') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'Nuevo SSID';}" size="70" style='font-family:courier;  font-weight:bold; background-color:black; color:gray; border-style:dotted;' >
<br>Persistente?:<input type="checkbox" name="newSSIDPersistent">
<br><input type='submit' value='Cambiar SSID'>
</form>
</tr></td>
<tr><td>

<b>Listas SSID Blancas/Negras</b><br>
<?php
$BWMode = exec("hostapd_cli -p /var/run/hostapd-phy0 karma_get_black_white");
$changeLink = "<a href='karma/changeBW.php'>cambiar</a>";
?>
<font color='lime' size='2'> Ahora en modo <?php echo $BWMode ?> | <font color='red'><?php echo $changeLink ?></font></font><br>
<form action='<?php echo $_SERVER[php_self] ?>' method= 'post' >
<input type="text" name="ssidBW" size='25' value="SSID" onFocus="if(this.value == 'SSID') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'SSID';}" size="70" style='font-family:courier;  font-weight:bold; background-color:black; color:gray; border-style:dotted;'>
<br><input type='submit' name='addSSID' value='Agregar'><input type='submit' name='removeSSID' value='Borrar'>
</form>
</tr></td>
<tr><td>

<b>Lista Negra de Clientes</b><br>
<form action='<?php echo $_SERVER[php_self] ?>' method= 'post' >
<input type="text" name="macBW" size='25' value="MAC" onFocus="if(this.value == 'MAC') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'MAC';}" size="70" style='font-family:courier;  font-weight:bold; background-color:black; color:gray; border-style:dotted;'>
<br><input type='submit' name='addMAC' value='Agregar'><input type='submit' name='removeMAC' value='Borrar'>
</form>
</td></tr>
<!--<tr><td>

<?php /*
$filename = "/etc/config/wireless";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Wireless</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/etc/config/wireless'>
<br><input type='submit' value='Actualizar Wireless'>
</form>";
*/?>
</td><td valign="top" align="left">
Configuraci&oacute;n Wireless para modo no-karma. 
</td></tr>-->
<!--<tr><td>

<?php /*
$filename = "/etc/config/network";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Red</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/etc/config/network'>
<br><input type='submit' value='Actualizar Red'>
</form>";
*/ ?>
</td><td valign="top" align="left">
Configuraciones LAN. ipaddr especifica la Direcci&oacute;n IP del dispositivo mientras que el gateway especifica la Direcci&oacute;n IP desde la cual se obtiene acceso a Internet. DNS especifica un servidor DNS necesario para la resoluci&oacute;n de nombres.
</td></tr>-->
<!--<tr><td>

<?php /*
$filename = "/etc/config/dhcp";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>DHCP</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/etc/config/dhcp'>
<br><input type='submit' value='Actualizar DHCP'>
</form>";
*/ ?>

</td><td valign="top" align="left"> 
Configuraci&oacute;n DHCP. Entrega informai$oacute;n de IP y DNS a los clientes que se conectan. dhcp_option #3 especifica la Direcci&oacute;n IP del gateway desde el cual se obtiene acceso a Internet. #6 especifica los servidores DNS desde los cuales se resuleven nombres. 
</td></tr>-->
<tr><td>

<?php /*
$filename = "/www/pineapple/ngrep.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<a name='ngrep'><b>Ngrep</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/ngrep.sh'>
<br><input type='submit' value='Actualizar Ngrep'>
</form>";
*/ ?>

<!--</td><td valign="top" align="left">
Configuraci&oacute;n ngrep. Como ngrep, pero para la red.
</td></tr>-->
<tr><td>


<?php
$filename = "/www/pineapple/config/spoofhost";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<a name='spoofhost'><b>Host de Spoof DNS</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/config/spoofhost'>
<br><input type='submit' value='Acrualizar Spoofhost'>
</form>";
?>

</td><td valign="top" align="left">
Archivo Spoofhost utilizado por DNSSPoof. Especifica nueva IP destino para el dominio fuente. Puede contener comodines tales como *.ejemplo.com.
</td></tr>
<tr><td>

<?php
$filename = "/www/index.php";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!!");
  fclose($fh);
 echo "<a name='spoofhost'><b>P&aacute;gina de Aterrizaje (phishing)</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/index.php'>
<br><input type='submit' value='Actualizar P&aacute;gina'>
</form>";
?>

</td><td valign="top" align="left">
P&aacute;gina de Aterrizaje para el servidor web del dispositivo. Puede configurarse como portal captivo o p&aacute;gina de phishing utilizando Spoofhost. Se permite PHP.
</td></tr></table>



</pre>
</body>
</html>
