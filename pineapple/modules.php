<?php

if(isset($_GET[getModule], $_GET[moduleVersion])){

	exec("mkdir -p /tmp/modules");
	exec("wget -O /tmp/modules/mk4-module-".$_GET[getModule]."-".$_GET[moduleVersion].".tar.gz \"http://cloud.wifipineapple.com/mk4/downloads.php?downloadModule=".$_GET[getModule]."&moduleVersion=".$_GET[moduleVersion]."\"");
        $path = "/tmp/modules/mk4-module-".$_GET[getModule]."-".$_GET[moduleVersion].".tar.gz";
        $cmd = "tar -xzf ".$path." -C /tmp/modules/";
        exec($cmd);
        $configArray = explode("\n", trim(file_get_contents("/tmp/modules/mk4-module-".$_GET[getModule]."-".$_GET[moduleVersion]."/module.conf")));

        $name = explode("=", $configArray[0]);
        $version = explode("=", $configArray[1]);
        $author = explode("=", $configArray[2]);
        $destination = explode("=", $configArray[3]);
        $depends = explode("=", $configArray[4]);
        $startPage = explode("=", $configArray[5]);

        if($destination[1] == "usb") echo "Instalación en USB no soportada. AUN ( No ha habido tiempo :'( )";
        elseif(is_dir("/www/pineapple/modules/".$name[1]))
        {
                echo "Ya instalado";

        }else
        {
                if($depends[1] != ""){
                        #descargar+ínstalar dependencias
                        $dependsArray = explode(",", $depends[1]);
                        exec("opkg update");
                        foreach($dependsArray as $dep){
                                exec("opkg install ".$dep);
                        }
                }
                #Instalar el módulo
                exec("mv ".substr_replace($path, "", -7)."/$name[1] /www/pineapple/modules/");
                exec("echo '".$name[1]."|".$version[1]."|".$startPage[1]."' >> /www/pineapple/modules/moduleList");
        }


}


?>
<html>
<head>
<title>Centro de Control Pi&ntilde;a WiFi</title>
<script  type="text/javascript" src="jquery.min.js"></script>
</head>
<body bgcolor="black" text="white" alink="green" vlink="green" link="green">

<?php require('includes/navbar.php'); ?>
<pre>
<?php

if(isset($_GET[remove]) && $_GET[remove] != ""){
exec("rm -rf modules/".$_GET[remove]);
$cmd = "sed '/".$_GET[remove]."/{x;/^$/d;x}' modules/moduleList > modules/moduleListtmp && mv modules/moduleListtmp modules/moduleList";
exec($cmd);
echo "Borrado ".$_GET[remove];
}

?>
<center>
<font color="yellow"><b>Bar de la Pi&ntilde;a WiFi</b></font>
Venga y tome algunas infusiones para su cocktail de pi&ntilde;a
</center>
<b>Infusiones Instaladas:</b>
<?php
#obtener una lista de los módulos actuales:
$moduleArray = explode("\n", trim(file_get_contents("modules/moduleList")));
?>

<table cellpadding=5px><tr>
<tr><td>M&oacute;dulo </td><td> Versi&oacute;n </td></tr>
<?php
foreach($moduleArray as $module){
$moduleArray = explode("|", $module);
if($moduleArray[0] == ""){ echo "No hay m&oacute;dulos instalados."; break;}
echo "<tr><td><font color=lime>".$moduleArray[0]." </td><td> ".$moduleArray[1]."<td><a href='modules/".$moduleArray[0]."/".$moduleArray[2]."'>Lanzar</a></td><td><font color=red><a href='?remove=".$moduleArray[0]."' )'>Borrar</a></td></tr>";
}
?>
</table>


<b>Infusiones Disponibles: <a href="?show">Mostrar</a></b>
<font color=red>Advertencia: Esto establecer&aacute; una
conexi&oacute;n a wifipineapple.com</font>


<?php
if(isset($_GET[show])){
$moduleListArray = explode("#", file_get_contents("http://cloud.wifipineapple.com/mk4/downloads.php?moduleList"));
if($moduleListArray[0] != " "){
echo "<table cellpadding=5px><tr>
<tr><td>M&oacute;dulo</td><td>Versi&oacute;n</td><td>Autor</td><td>Descripci&oacute;n</td></tr>";
foreach($moduleListArray as $moduleArr){

$nameVersion = explode("|", $moduleArr);
if($nameVersion[0] != "\n" && $nameVersion[0] != ""){
echo "<tr><td><font color=lime>".$nameVersion[0]."</td><td>".$nameVersion[1]."</td><td>".$nameVersion[2]."</td><td>".$nameVersion[3]."</td><td><a href='modules.php?getModule=".$nameVersion[0]."&moduleVersion=".$nameVersion[1]."'>Instalar</a></td></tr><br />";
}
}
echo "</table>";
}else{
echo "No se han encontrado m&oacute;dulos";
}
}
?>
</pre>
</body>
</html>
