<html>
<head>
<?php
##No modifique este archivo, si lo hace DAÑARÁ su Piña WiFi
##Espere que esto se encuentre integrado para auto OTA
##Inciar actualización de firmware
if(isset($_GET[webDownload]) && trim(file_get_contents('webUpgrade.status')) == "idle"){
exec("echo downloading > webUpgrade.status");
exec("rm /tmp/upgrade.bin");
exec("echo 'sh webUpgrade.sh' | at now");
}
##Recargar para verificar terminación de la descarga
if(isset($_GET[webDownload]) && trim(file_get_contents('webUpgrade.status')) == "downloading"){
echo '<meta http-equiv="refresh" content="5">';
}
##si ha terminado 
if(isset($_GET[webDownload]) && trim(file_get_contents('webUpgrade.status')) == "doneDownloading"){
echo '<meta http-equiv="refresh" content="0;url=?webUpgrade">';
}
?>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<script  type="text/javascript" src="includes/jquery.min.js"></script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('includes/navbar.php'); ?>
<pre>
<?php
if(isset($_GET[webDownload])){
echo '<center>Downloading...<br />Por favor sea paciente, esta p&aacute;gina se<br />recargar&aacute; autom&aacute;ticamente.</center>';
}
?>
<?php

if($_FILES[upgrade][error] > 0){
    $error = $_FILES[upgrade][error];
	echo "Error ($error), por favor verifique el archivo especificado.";
}
elseif(isset($_FILES[upgrade]) && $_FILES[upgrade][name] != "upgrade.bin"){
echo "El archivo de actualizaci&oacute;n debe ser renombrado a upgrade.bin";
}
elseif(isset($_FILES[upgrade])){
exec("rm /tmp/upgrade.bin");
move_uploaded_file($_FILES[upgrade][tmp_name], "/tmp/".$_FILES[upgrade][name]);
if(exec("md5sum /tmp/upgrade.bin | grep -w ".$_POST[md5sum]) == ""){
echo "Error, la verificaci&oacute;n MD5Sum no corresponde!";
}else exec('sysupgrade -n /tmp/upgrade.bin');
}

?>
<div align=right>
<?php
if(isset($_GET[checkUpgrade])){
$remoteFile = explode("|", trim(file_get_contents("http://cloud.wifipineapple.com/mk4/downloads.php?currentVersion")));
$remoteMD5 = $remoteFile[1];
$remoteVersion = explode(".", $remoteFile[0]);
$localVersion = explode(".", file_get_contents("includes/fwversion"));

if($remoteVersion[0] > $localVersion[0]){
echo "Actualizaci&oacute;n ($remoteVersion[0].$remoteVersion[1].$remoteVersion[2]) encontrada | <a href=\"http://cloud.wifipineapple.com/mk4/downloads.php?download\">Descargar</a>";
echo "<br />MD5: ".$remoteMD5."<br />";
}else if($remoteVersion[0] == $localVersion[0]){
//Go further
	if($remoteVersion[1] > $localVersion[1]){
		echo "Actualizaci&oacute;n ($remoteVersion[0].$remoteVersion[1].$remoteVersion[2]) encontrada | <a href=\"http://cloud.wifipineapple.com/mk4/downloads.php?download\">Descargar</a>";
		echo "<br />MD5: ".$remoteMD5."<br />";
	}elseif($remoteVersion[1] == $localVersion[1]){
		//Go further
		if($remoteVersion[2] > $localVersion[2]){
			echo "Actualizaci&oacute;n ($remoteVersion[0].$remoteVersion[1].$remoteVersion[2]) encontrada | <a href=\"http://cloud.wifipineapple.com/mk4/downloads.php?download\">Descargar</a>";
			echo "<br />MD5: ".$remoteMD5."<br />";
		}else echo "No se ha encontrado una actualizaci&oacute;n.";
	}else echo "No se ha encontrado una actualizaci&oacute;n.";
}else echo "No se ha encontrado una actualizaci&oacute;n.";
}
?>

Actualización En-L&iacute;nea | <a href="<?php echo $_SERVER[PHP_SELF] ?>?checkUpgrade">Verificar</a>

<font color=red>Advertencia:</font> Esto establecer&aacute; una 
conexi&oacute;n a wifipineapple.com
</div>
<center>La versi&oacute;n actual del firmware es: <?php include('includes/fwversion'); ?>
Seleccione un upgrade.bin y haga click en Actualizar:

<form action="<?php $_SERVER[php_self] ?>" method="post" enctype="multipart/form-data">
<input type="file" value="upgrade.bin" name="upgrade" id="upgrade" /><input type="submit" onclick="alert('Tenga en cuenta: Si la carga es exitosa, la p&aacute;gina mostrar&aacute; un error. Esto es esperado. Por favor espere pacientemente mientras la pi&ntilde;a trabaja. Se reiniciar&aacute; y se actualizar&aacute;.');" value="Actualizar" name="Upgrade">
MD5Sum: <input type="text" name="md5sum">
</form>
<font color='orange' >Al hacer click en Actualizar, rel&aacute;jese. Va a estar bien. <br />El error es esperado. Dele unos cuantos minutos a que la pi&ntilde;a se actualice y reinicie mientras ud. se toma una pi&ntilde;a colada ;-).</font>

Nota: La actualizaci&oacute;n puede tomar varios minutos y reiniciar&aacute; el dispositivo. 
Por favor sea paciente.

<pre>

<b><font color='red'>Advertencia:</font></b>
Desconecte y reconecte la energ&iacute;a de la Pi&ntilde;a WiFi y deshabilite Karma, SSH, 3G y otros servicios no esenciales antes de actualizar el firmware.
Bajo normales circunstancias, una actualizaci&oacute;n de firmware es perfectamente segura, sin embargo tenga en cuenta:
 - Las opciones de restauraci&oacute;n del gestor de arranque (Bootloader) solo son accesibles v&iacute;a cable serial. 
 - No actualice el firmware si tiene conectada la pi&ntilde;a a una bater&iacute;a.
 - No actualice el firmware si la memoria es baja.
 - No actualice el firmware v&iacute;a WiFi.
<!-- No alimentar la Piña después de medianoche -->

</center><pre>
<b>Memoria</b>
<?php
$cmd = "free";
exec ($cmd, $output);
foreach($output as $outputline) {
echo ("$outputline\n");}
?>

</body>
</html>
