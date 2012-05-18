<html>
<head>
<title>Pineapple Control Center</title>
<script  type="text/javascript" src="includes/jquery.min.js"></script>
<style>
textarea
{
    border:none;
    width:99%;
    height:90%
}
</style>

</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('/www/pineapple/includes/navbar.php'); ?>
<textarea>
<?php
exec("grep -E 'GET|POST|User|Host|Cookie' /tmp/cookielog.txt > /tmp/cookielogclean.txt");
$cmd = "cat /tmp/cookielogclean.txt";
exec ($cmd, $output);                                                              
foreach($output as $outputline) {
echo ("$outputline\n");
}
?>
</textarea>
<a name="bottom"></a>
<a href="javascript:window.location.reload()">Refresh Log</a></pre>
</body>
</html>
