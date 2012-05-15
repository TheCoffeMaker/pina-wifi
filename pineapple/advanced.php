<html>
<head>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<script  type="text/javascript" src="jquery.min.js"></script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('includes/navbar.php'); ?>

<table border="0" width="100%"><tr><td align="left" valign="top" width="700">
<pre>

<?php

if(isset($_POST['changePass'])){

if($_POST['pass'] == $_POST['pass2'] && $_POST['pass'] != ""){
exec("./advanced/changePass ".$_POST['pass']);
echo "<font color=lime>Contrase&ntilde;a cambiada exitosamente.<br />Nota: Se requiere que reinicie la Pi&ntilde;a para que los cambios tengan efecto en la interfaz de administraci&oacute;n.<br /><br /></font>";
}else echo "<font color=red>Cambio de contrase&ntilde;a fallido. Aseg&uacute;rese de que las contrase&ntilde;as coincidan!<br /><br /></font>";


}

if(isset($_POST['route']) && $_POST['route'] != "") {
exec($_POST['route'], $routeoutput); 
echo "<br /><font color='yellow'>Ejecutando " . $_POST['route'] . "</font><br /><br /><font color='red'><b>";
foreach($routeoutput as $routeoutputline) { echo ("$routeoutputline\n"); }
echo "</b></font><br />"; }

if(isset($_POST['zcommand']) && $_POST['zcommand'] != "") {
$zcommand = $_POST['zcommand'];

$keyarr=explode("\n",$zcommand);
foreach($keyarr as $key=>$value)
{
  $value=trim($value);
  if (!empty($value)) {
      echo "\n<font color='red'>Ejecutando: $value</font>\n";
      $zoutput = "";
      $zoutputline = "";
      exec ($value, $zoutput);
      foreach($zoutput as $zoutputline) {
      echo ("$zoutputline\n");}
  }
}
echo "<br /><br />";
}


$filename =$_POST['filename'];
$newdata = $_POST['newdata'];

if ($newdata != '') {
 $fw = fopen($filename, 'w') or die('No se puede abrir el archivo!');
 $fb = fwrite($fw,stripslashes($newdata)) or die('No se puede modificar el archivo');
 fclose($fw);
 echo "Actualizado " . $filename . "<br /><br />";
}

if(isset($_POST['clearcache']) && $_POST['clearcache'] == "1") {
exec("echo '' > /www/pineapple/logs/associations.log");
exec("echo '' > /www/pineapple/logs/urlsnarf.log");
exec("echo '' > /www/pineapple/logs/urlsnarf-clean.log");
exec("echo '' > /www/pineapple/logs/ngrep.log");
exec("echo '' > /www/pineapple/logs/ngrep-clean.log");
echo "<font color='lime'><b>Cache Borrado</b></font><br />";
}

if(isset($_POST['factoryreset']) && $_POST['factoryreset'] == "1") {
echo "<font color='red'><b>La restauraci&oacute;n de f&aacute;brica ha sido deshabilitada en esta versi&oacute;n del firmware.</b></font><br /><br />";
}

if(isset($_POST['reboot']) && $_POST['reboot'] == "1") {
exec("reboot");
}

?>
<b>Tabla de Enrutamiento de Kernel IP</b>
<?php $cmd = "route | grep -v 'Kernel IP routing table'";
exec("$cmd 2>&1", $output);
foreach($output as $outputline) {echo ("$outputline\n");}?>

<form action='<?php echo $_SERVER[php_self] ?>' method= 'post' >
<input type="text" name="route" value="route " size="70" style='font-family:courier; font-weight:bold; background-color:black; color:gray; border-style:dotted;'> 
<input type='submit' value='Actualizar'> <small><font color="gray">Ejemplo: <i>route add default gw 172.16.42.42 br-lan</i> <br /></font></small></form>
<form method="post" action="advanced/ping.php"><input type="text" name="pinghost" style='font-family:courier; font-weight:bold; background-color:black; color:gray; border-style:dotted;'/> <input type="submit" value="Ping" name="submit"></form>
<form method="post" action="advanced/traceroute.php"><input type="text" name="traceroutehost" style='font-family:courier; font-weight:bold; background-color:black; color:gray; border-style:dotted;'/> <input type="submit" value="Traceroute" name="submit"></form>
<form action='<?php echo $_SERVER[php_self] ?>' method= 'post' ><textarea cols="80" rows="10" name="zcommand" style='font-family:courier; font-weight:bold; background-color:black; color:gray; border-style:dotted;'></textarea>
<input type='submit' value='Ejecutar Comandos'> <small><font color="gray">Se ejecutar&aacute; un comando por l&iacute;nea</font></small></form>


Cambiar la Contrase&ntilde;a de root:
<form action="" method="POST">
<table>
<tr><td><?php echo $passMessage ?></td></tr>
<tr><td>Contrase&ntilde;a:</td><td><input type="password" name="pass"></td></tr>
<tr><td>Repetir Contrase&ntilde;a:</td><td><input type="password" name="pass2"></td></tr>
<tr><td><input type="submit" name="changePass" value="Cambiar Contrase&ntilde;a"></td></tr>
</table>
</form>

</pre>
</td><td valign="top" align="left" width="*">
<pre>

<form method="post" action="<?php echo $_SERVER[php_self]?>"><input type="hidden" name="clearcache" value="1"><input type="submit" value="Borrar Cache" onClick="return confirm('Si el dispositivo es apagado antes de detener servicios tales como Karma o URLSnarf, informaci&oacute;n de sesiones anteriores puede permanecer. Esto se corrige limpiando el cache pineapple. Si a&uacute;n se muestra informaci&oacute;n fantasma, limpie el cache del navegador web.')"></form><form method="post" action="<?php echo $_SERVER[php_self]?>"><input type="hidden" name="factoryreset" value="1"><input type="submit" value="Reset de F&aacute;brica" onClick="return confirm('Est&aacute; seguro de querer restaurar la configuraci&oacute;n predeterminada de f&aacute;brica? Este cambio no se puede deshacer.')"></form><form method="post" action="<?php echo $_SERVER[php_self]?>"><input type="hidden" name="reboot" value="1"><input type="submit" value="Reiniciar" onClick="return confirm('Est&aacute; seguro de querer reiniciar el dispositivo?')"></form>






<font color="white">
                    \               
                  \  \          
                \  \  \</font><font color="green">              
<,  .v ,  // </font><font color="white">) ) )  )  )</font><font color="green">                  
 \\; \// //     </font><font color="white">/  /  /</font><font color="green">                          
  ;\\|||//;       </font><font color="white">/  /</font><font color="yellow">
 ,'<\/><\/`         </font><font color="white">/</font><font color="yellow">                    
,.`X/\><\\>`                      
;>/>><\\><\/`                        
|<\\>>X/<>/\|
`<\/><\/><\\;                            
 '/\<>/\<>/'                       
   `<\/><;`</font><font color="white">pi&ntilde;a_wifi</font>

</pre>
</td></tr></table>
</body>
</html>
