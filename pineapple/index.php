<html>
<head>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<!--<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">-->
<script type="text/javascript" src="includes/ajax.js"> </script>
<script type="text/javascript" src="logtail/logtail.js"> </script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green" onload="getLog('start');">

<?php require('includes/navbar.php'); ?>
<pre>

<table border="0" width="100%"><tr><td valign="top" align="left" width="350">
<b>Servicios</b><br />
<?php

$iswlanup = exec("ifconfig wlan0 | grep UP | awk '{print $1}'");
if ($iswlanup == "UP") {
echo "&nbsp;&nbsp;&nbsp;Wireless  <font color=\"lime\"><b>activo</b></font> | <a href=\"wlan.php?stop\"><b>Detener</b></a><br />";
} else { echo "&nbsp;&nbsp;&nbsp;Wireless  <font color=\"red\"><b>inactivo</b></font> | <a href=\"wlan.php?start\"><b>Iniciar</b></a><br />"; }

if ( exec("hostapd_cli -p /var/run/hostapd-phy0 karma_get_state | tail -1") == "ENABLED" ){
$iskarmaup = true;
}
if ($iskarmaup != "") {
echo "&nbsp;&nbsp;Karma MK4  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"karma/stopkarma.php\"><b>Detener</b></a><br />";
} else { echo "&nbsp;&nbsp;Karma MK4  <font color=\"red\"><b>inactivo</b></font>. | <a href=\"karma/startkarma.php\"><b>Iniciar</b></a> <br />"; }

$autoKarma = ( exec("if grep -q 'hostapd_cli -p /var/run/hostapd-phy0 karma_enable' /etc/rc.local; then echo 'true'; fi") );
if ($autoKarma != ""){
echo "&nbsp;Autoinicio  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"karma/autoKarmaStop.php\"><b>Detener</b></a><br />";
} else { echo "&nbsp;Autoinicio  <font color=\"red\"><b>inactivo</b></font>. | <a href=\"karma/autoKarmaStart.php\"><b>Iniciar</b></a><br />"; }

$cronjobs = ( exec("ps -all | grep [c]ron"));
if ($cronjobs != ""){
echo "Tareas Cron <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"jobs.php?stop&goback\"><b>Detener</b></a><br />";
} else { echo "Tareas Cron <font color=\"red\"><b>inactivo</b></font>. | <a href=\"jobs.php?start&goback\"><b>Iniciar</b></a> | <a href=\"jobs.php\"><b>Edit</b></a><br />"; }

$isurlsnarfup = exec("ps auxww | grep urlsnarf.sh | grep -v -e grep");
if ($isurlsnarfup != "") {
echo "&nbsp;&nbsp;URL Snarf  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"urlsnarf/stopurlsnarf.php\"><b>Detener</b></a><br />";
} else { echo "&nbsp;&nbsp;URL Snarf  <font color=\"red\"><b>inactivo</b></font>. | <a href=\"urlsnarf/starturlsnarf.php\"><b>Iniciar</b></a><br />"; }

$isdnsspoofup = exec("ps auxww | grep dnsspoof.sh | grep -v -e grep");
if ($isdnsspoofup != "") {
echo "&nbsp;&nbsp;Spoof DNS  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"dnsspoof/stopdnsspoof.php\"><b>Detener</b></a><br />";
} else { echo "&nbsp;&nbsp;Spoof DNS  <font color=\"red\"><b>inactivo</b></font>. | <a href=\"dnsspoof/startdnsspoof.php\"><b>Iniciar</b></a> | <a href=\"config.php#spoofhost\"><b>Editar</b></a><br/>"; }

/*$isngrepup = exec("ps auxww | grep ngrep | grep -v -e \"grep ngrep\" | awk '{print $1}'");
if ($isngrepup != "") {
echo "&nbsp;&nbsp;&nbsp;&nbsp;ngrep  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"ngrep/stopngrep.php\"><b>Detener</b></a>";
} else { echo "&nbsp;&nbsp;&nbsp;&nbsp;ngrep  <font color=\"red\"><b>inactivo</b></font>. | <a href=\"ngrep/startngrep.php\"><b>Iniciar</b></a> | <a href=\"config.php#ngrep\"><b>Editar</b></a><br/>"; }
*/

if (exec("grep 3g.sh /etc/rc.local") != ""){                                                         
echo "Arranque 3G  <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"3g.php?disable&disablekeepalive&goback\"><b>Desactivar</b></a><br />";
} else { echo "Arranque 3G <font color=\"red\"><b>inactivo</b></font>. | <a href=\"3g.php?enable&goback\"><b>Activar</b></a><br />"; }              
                                                                                                                                                        
if (exec("grep 3g-keepalive.sh /etc/crontabs/root") == "") {                                                                              
echo "&nbsp;&nbsp;Redial 3G <font color='red'><b>inactivo</b></font>. | <a href='3g.php?enablekeepalive&enable&goback'><b>Activar</b></a><br />";             
} else { echo "&nbsp;&nbsp;Redial 3G <font color='lime'><b>activo</b></font>.&nbsp; | <a href='3g.php?disablekeepalive&goback'><b>Desactivar</b></a><br />"; } 

if (exec("ps aux | grep [s]sh | grep -v -e ssh.php | grep -v grep") == "") {                                                                                             
echo "&nbsp;Sesi&oacute;n SSH <font color=\"red\"><b>inactiva</b></font>. &nbsp;| <a href=\"ssh.php?connect\"><b>Activar</b></a><br /><br />";        
} else {         
echo "&nbsp;Sesi&oacute;n SSH <font color=\"lime\"><b>activa</b></font>. &nbsp; | <a href=\"ssh.php?disconnect\"><b>Desactivar</b></a><br /><br />";
} 

                                                                                                                                                        
echo "<br/><b>Interfaces</b><br />";

echo "&nbsp;Puerto PoE / LAN: " . exec("ifconfig br-lan | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'") . "<br />";
echo "&nbsp;&nbsp; Modem USB 3G: " . exec("ifconfig 3g-wan2 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'") . "<br />";
echo "&nbsp;Puerto WAN / LAN: " . exec("ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'") . "<br />";
echo "Internet P&uacute;blico: "; 
if (isset($_GET[revealpublic])) { 
	echo exec("wget -qO- http://cloud.wifipineapple.com/ip.php") . "<br />"; 
} else { 
	echo "<a href=\"index.php?revealpublic\">mostrar IP p&uacute;blica</a><br />"; 
}

?>

</td><td valign="top" align="left" width="*">



<pre>
<a href="#" onclick="getLog('start');return false"><b>Reanudar Log</b></a> | <a href="#" onclick="stopTail();return false"><b>Pausar Log</b></a> | <?php if (isset($_GET[report])) { echo "<a href='index.php'><b>Descartar Reporte Detallado</b></a>"; } else { echo "<a href='index.php?report'><b>Generar Reporte Detallado</b></a>"; } ?><br />

<?php
if (isset($_GET[report])) {
	echo "<br /><b>Reporte Detallado</b> &nbsp; &nbsp; <small><font color='gray'>Consume alto CPU. No re-ejecute reportes de forma repetida</font></small><br /><br />";
	$cmd="/www/pineapple/karma/karmaclients.sh";
	exec("$cmd 2>&1", $output);                                                                                                                                     
	foreach($output as $outputline) {
		 echo ("$outputline\n");         
	 }
} else {

	echo "<div id='log'>Log Karma:</div>";

}
 
?>


</pre>
</td></tr></table>
</pre><!-- http://www.youtube.com/watch?v=KqL_nsSl_Fs //easter egg -->
</body>
</html>
