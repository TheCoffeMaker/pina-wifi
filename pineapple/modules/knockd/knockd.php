<html><head>
<?php
$module_name = "Knockd";
$module_path = "/www/pineapple/modules/knockd/";
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
if(isset($_GET[installKNOCKD])){
	exec("opkg update");
	echo "Updated opkg<br><br>";
	exec("opkg install knockd");
	echo "Installed knockd<br><br>";
}
if(isset($_GET[removeKNOCKD])){
        exec("opkg remove knockd");
        echo "Removed knockd<br><br>";
}

if(isset($_GET[runKNOCKD])){

  $filename = "portsopen";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $portso = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);

  $filename = "portsclose";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $portsc = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);

  $filename = "cmdopen";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $cmdo = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);

  $filename = "cmdclose";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $cmdc = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);

exec("echo '
 [options]
        logfile = /var/log/knockd.log

  [openSSH]
        sequence    = $portso
        seq_timeout = 10
        tcpflags    = syn
        command     = $cmdo

  [closeSSH]
        sequence    = $portsc
        seq_timeout = 10
        tcpflags    = syn
        command     = $cmdc' > knockd.config");
echo "Config generated<br><br>";
exec("knockd -d -c /www/pineapple/modules/knockd/knockd.config -i br-lan");
echo "Running knockd<br><br>";
sleep(3);
}

if(isset($_GET[stopKNOCKD])){
        echo "Running: killall knockd<br><br>";
        exec("killall knockd");
	sleep(3);
}
?>

<?php
echo "Knockd  ";
$is_knockd = file_exists("/usr/sbin/knockd") ? 1 : 0;
if($is_knockd)
{
        echo "&nbsp;";
	$is_knockdrun = file_exists("/tmp/run/knockd.pid") ? 1 : 0;
	if($is_knockdrun){
		echo " <font color=\"lime\"><strong>ACTIVE</strong></font></span>";
		echo " | <a href=\"knockd.php?stopKNOCKD\">Stop</a>";
	} else {
		echo " <font color=\"red\"><strong>Stoped</strong></font></span>";
		echo " | <a href=\"knockd.php?runKNOCKD\">Start</a> | <a href=\"knockd.php?removeKNOCKD\">Remove</a>";
	}
}
else
{
        echo "&nbsp;<span id=\"knockd_status\"><font color=\"red\"><strong>not installed</strong></font></span>";
	echo " | <a href=\"knockd.php?installKNOCKD\">Install</a>";
}
?>
<?php
$data ="aaa";
if($is_knockd){
echo "<table border='0' width='100%' valign='top'>
<tr><td><br><br>
<b>Open</b><br>
<form action='knockd.php' method= 'post' >";
  $filename = "portsopen";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
echo "<div style='vertical-align:top;display:inline;'>Ports &nbsp;&nbsp;&nbsp;</div><textarea name='newdata' cols='40' rows='1' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='portsopen'>
<input type='submit' value='Save'>
</form>
</td></tr>
<tr><td>
<form action='knockd.php' method= 'post' >";
  $filename = "cmdopen";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
echo "<div style='vertical-align:top;display:inline;'>Commands </div><textarea name='newdata' cols='80' rows='4' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='cmdopen'>
<input type='submit' value='Save'>
</form></td></tr>
<tr><td><br><br>
<b>Close</b><br>
<form action='knockd.php' method= 'post' >";
  $filename = "portsclose";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
echo "<div style='vertical-align:top;display:inline;'>Ports &nbsp;&nbsp;&nbsp;</div><textarea name='newdata' cols='40' rows='1' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='portsclose'>
<input type='submit' value='Save'>
</form></td></tr>
<tr><td>
<form action='knockd.php' method= 'post' >";
  $filename = "cmdclose";
  $fh = fopen($filename, "r") or die("Could not open file!");
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  fclose($fh);
echo "<div style='vertical-align:top;display:inline;'>Commands </div><textarea name='newdata' cols='80' rows='4' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='cmdclose'>
<input type='submit' value='Save'>
</form>
</td></tr>";
}
?>
</pre>
</body>
</html>
