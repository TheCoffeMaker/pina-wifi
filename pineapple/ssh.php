<?php 

if (isset($_GET[deletekey])) {
	exec("rm -rf /etc/dropbear/id_rsa");
	echo "<pre>Par de Llaves SSH borradas de /etc/dropbear/id_rsa</pre>";
	}

if (isset($_GET[generatekey])) {
	exec("rm -rf /etc/dropbear/id_rsa");
	exec("dropbearkey -t rsa -f /etc/dropbear/id_rsa");
	echo "<pre>Par de Llaves SSH generadas y guardadas en /etc/dropbear/id_rsa</pre>";
	}

if (isset($_GET[connect])) {
	if (exec("ps aux | grep [s]sh | grep -v -e ssh.php | grep -v grep") == "") {
		echo "<pre>Iniciando sesi&oacute;n SSH.</pre>";
		exec("echo /www/pineapple/ssh/ssh-connect.sh | at now");
		sleep(2);
	} else {
		echo "<pre>Una imagen del proceso reporta que SSH ya se encuentra en ejecuci&oacute;n. Pruebe desconectando y luego reconectando.</pre>";
	}
}

if (isset($_GET[disconnect])) {
	if (exec("ps aux | grep [s]sh | grep -v -e ssh.php | grep -v grep") == "") {
		echo "<pre>Una imagen del proceso reporta que no hay sesiones SSH en ejecuci&oacute;n. No hay sesiones a desconectar.</pre>";
	} else {
		echo "<pre>Terminando la sesi&oacute;n SSH</pre>";
		exec("kill `ps aux | grep -v -e ssh.php | awk '/[s]sh/{print $1}'`");
		sleep(2);
	}
}

if (isset($_GET[enablekeepalive])) {
	if (exec("grep ssh-keepalive.sh /etc/crontabs/root") == "") {
		exec("echo '*/5 * * * * /www/pineapple/ssh/ssh-keepalive.sh' >> /etc/crontabs/root");
		echo "<pre>Script de conectividad SSH persistente agregado a Tareas Cron. Aseg&uacute;rese de habilitar el servicio Cron en <a href='jobs.php'><b>Tareas</b></a>.</pre>";
	} else {
		echo "<pre>Script de conectividad SSH persistente ya se encuentra en Crontab. Revise <a href='jobs.php'><b>Tareas</b></a> para solucionar problemas.</pre>";
	}
}

if (isset($_GET[disablekeepalive])) {
	exec("sed -i '/ssh-keepalive.sh/d' /etc/crontabs/root");
	echo "<pre>Script de conectividad SSH persistente borrado de Tareas Cron. Revise <a href='jobs.php'><b>Tareas</b></a></pre>";
}



$sshonboot = (exec("grep ssh-connect.sh /etc/rc.local"));

if (isset($_GET[enable])) {
	if (exec("grep ssh-connect.sh /etc/rc.local") == "") {
		exec("sed -i '/exit 0/d' /etc/rc.local"); 
		exec("echo /www/pineapple/ssh/ssh-connect.sh >> /etc/rc.local");
		exec("echo exit 0 >> /etc/rc.local");
		echo "<pre>Conexi&oacute;n SSH en el arranque activa.</pre>";
		$sshonboot = "true";
	} else {
		echo "<pre>Conexi&oacute;n SSH en el arranque ya se encuentra activa, no se modifica rc.local</pre>";
	}
}                                

if (isset($_GET[disable])) {
	exec("sed -i '/ssh-connect.sh/d' /etc/rc.local");
	echo "<pre>Conexi&oacute;n SSH en el arranque inactiva.</pre>";
	$sshonboot = "";                  
}

$filename = "/root/.ssh/known_hosts";
if (!file_exists($filename)) {
exec("echo ' ' > /root/.ssh/known_hosts");
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
 $fw = fopen($filename, 'w') or die('No se puede abrir el archivo! Vac&iacute;o?');
 $fb = fwrite($fw,stripslashes($newdata)) or die('No se puede modificar el archivo');
 fclose($fw);
 echo "Actualizado " . $filename . "<br /><br />";
} 


if ($sshonboot != ""){
echo "&nbsp;SSH en arranque actualmente <font color=\"lime\"><b>activo</b></font>.&nbsp; | <a href=\"ssh.php?disable&disablekeepalive\"><b>Desactivar</b></a><br />";
} else { echo "&nbsp;SSH en arranque actualmente <font color=\"red\"><b>inactivo</b></font>. | <a href=\"ssh.php?enable\"><b>Activar</b></a><br />"; }
if (exec("grep ssh-keepalive.sh /etc/crontabs/root") == "") {
echo "Persistencia SSH actualmente <font color='red'><b>inactiva</b></font>. | <a href='ssh.php?enablekeepalive&enable'><b>Activar</b></a><br />";
} else { echo "Persistencia SSH actualmente <font color='lime'><b>activa</b></font>.&nbsp; | <a href='ssh.php?disablekeepalive'><b>Desactivar</b></a><br />"; }


// debug: echo "<font color='pink'>" . exec("ps aux | grep [s]sh | grep -v -e ssh.php") . "</font>";
// 
// seccion actualmente comentada ya que autossh hace un buen trabajo manteniendo conexiones persistentes. El script de tarea cron ssh-keepalive.sh no es necesario.
// 

if (exec("ps aux | grep [s]sh | grep -v -e ssh.php") == "") {
	 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sesi&oacute;n SSH actualmente <font color=\"red\"><b>desconectada</b></font> | <a href=\"ssh.php?connect\"><b>Conectar</b></a><br /><br />";
} else {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sesi&oacute;n SSH actualmente <font color=\"lime\"><b>conectada</b></font>. | <a href=\"ssh.php?disconnect\"><b>Desconectar</b></a><br /><br />";
}


$filename = "/www/pineapple/ssh/ssh-connect.sh";
  $fh = fopen($filename, "r") or die("No se puede abrir el archivo! Vacio?");
  $data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
  fclose($fh);
 echo "<b>Comando de conexi&oacute;n SSH:</b><form action='$_SERVER[php_self]' method= 'post' ><input type='hidden' name='filename' value='/www/pineapple/ssh/ssh-connect.sh'>
<input type='text' name='newdata' size='85' style='font-family:courier; font-weight:bold; background-color:black; color:white; border-style:dotted;' value='$data' /><input type='submit' value='Save'></form>";


echo "<br /><b>Llave P&uacute;blica:</b> &nbsp; &nbsp; <font color='gray'><small>Esto normalmente va en %h/.ssh/authorized_keys en el host remoto</small></font><br /><br />";
	echo "<textarea rows='7' cols='89' style='background-color:black; color:white; border-style:dashed;'>";
	$cmd="dropbearkey -f /etc/dropbear/id_rsa -y";
	exec ($cmd, $output);
	foreach($output as $outputline) {
	echo ("$outputline\n");}
	echo "</textarea>";
?>
<br /><br />No hay llave? <a href="ssh.php?generatekey"><b>Generar</b></a> una</a> | <a href="ssh.php?deletekey"><b>Borrar</b></a> par de llaves SSH RSA</a><br />
<?php

$filename = "/root/.ssh/known_hosts";
$fh = fopen($filename, "r") or die("No se puede abrir el archivo!");
$data = fread($fh, filesize($filename)) or die("No se puede leer el archivo!");
fclose($fh);
echo "<b>Hosts Conocidos:</b>
<form action='$_SERVER[php_self]' method= 'post' >
<textarea name='newdata' cols='90' rows='8' style='background-color:black; color:white; border-style:dashed;'>$data</textarea>
<input type='hidden' name='filename' value='/root/.ssh/known_hosts'>
<input type='submit' value='Actualizar Hosts'>
</form>";
?>       
<b>Ayuda:</b>
<font color='green'>-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-</font>

<b>En el host local (esta pi&ntilde;a)</b>
 - Generar un par de llaves RSA. La llave privada ser&aacute; guardada en /etc/dropbear/id_rsa.
 - Observe la llave p&uacute;blica RSA mostrada arriba. Ud. necesitar&aacute; el desde "ssh-rsa" a "root@Pineapple".     
 - Agregue la llave p&uacute;blica ssh-rsa de los Clientes (no la llave p&uacute;blica de arriba) a ~/.ssh/known_hosts.      
   - Esto se logra m&aacute;s f&aacute;cilmente mediante la ejecuci&oacute;n de 'ssh user@host' y presionando 'y' cuando se le solicite guardar la llave
   - Esto se debe realizar de forma interactiva (a trav&eacute;s de una shell en este dispositivo) ya que AutoSSH no pasa la opci&oacute;n '-y'.
                                                              
<b>En el host remoto (el servidor)</b>
 - Agregue la llave p&uacute;blica RSA mostrada arriba al archivo authorized_keys. Este se encuentra regularmente en ~/.ssh/
 - Las siguientes opciones de configuraci&oacute;n de opensshd son &uacute;tiles. El archivo de configuraci&oacute;n se encuentra regularmente en /etc/ssh/sshd_config
        AllowTcpForwarding   yes
        GatewayPorts         yes
        RSAAuthentication    yes
        PubkeyAuthentication yes
        
<b>Ejemplo de Uso</b>

<b>Servidor de Relay Simple</b>
Con la llave de intercambio mostrada arriba y una completa configuraci&oacute;n SSH crear una sesi&oacute;n SSH a través de un servidor de relay
 - Comando SSH de la Pi&ntilde;a: autossh -M 20000 -f -N -R 4255:localhost:22 user@relayserver -i /etc/dropbear/id_rsa
 - Comando SSH de otra aplicaci&oacute;n: ssh pineappleuser@relayserver -p 4255
   - El usuario de la pi&ntilde;a es normalmente root
   - Si el servidor de relay no soporta reenv&iacute;o de puerto remoto, primero inicie la sesi&oacute;n SSH usual en el servidor de relay, luego:
     ssh pineappleuser@localhost -p 4255

<b>SSH</b>
Uso: ssh [opciones] [user@]host[/puerto] [comando]
Las opciones son:
-p &lt;remoteport&gt;
-l &lt;username&gt;
-t    Allocate a pty
-T    Don't allocate a pty
-N    Don't run a remote command
-f    Run in background after auth
-y    Always accept remote host key if unknown
-s    Request a subsystem (use for sftp)
-i &lt;identityfile&gt;   (multiple allowed)
-A    Enable agent auth forwarding
-L <[listenaddress:]listenport:remotehost:remoteport> Local port forwarding
-g    Allow remote hosts to connect to forwarded ports
-R <[listenaddress:]listenport:remotehost:remoteport> Remote port forwarding
-W &lt;receive_window_buffer&gt; (default 24576, larger may be faster, max 1MB)
-K &lt;keepalive&gt;  (0 is never, default 0)
-I &lt;idle_timeout&gt;  (0 is never, default 0)
-J &lt;proxy_program&gt; Use program pipe rather than TCP connection

<b>AutoSSH</b>
uso: autossh [-V] [-M monitor_port[:echo_port]] [-f] [SSH_OPTIONS]

    -M specifies monitor port. May be overridden by environment
       variable AUTOSSH_PORT. 0 turns monitoring loop off.
       Alternatively, a port for an echo service on the remote
       machine may be specified. (Normally port 7.)
    -f run in background (autossh handles this, and does not
       pass it to ssh.)
    -V print autossh version and exit.

Las variables de entorno son:
    AUTOSSH_GATETIME    - how long must an ssh session be established
                          before we decide it really was established
                          (in seconds)
    AUTOSSH_LOGFILE     - file to log to (default is to use the syslog
                          facility)
    AUTOSSH_LOGLEVEL    - level of log verbosity
    AUTOSSH_MAXLIFETIME - set the maximum time to live (seconds)
    AUTOSSH_MAXSTART    - max times to restart (default is no limit)
    AUTOSSH_MESSAGE     - message to append to echo string (max 64 bytes)
    AUTOSSH_PATH        - path to ssh if not default
    AUTOSSH_PIDFILE     - write pid to this file
    AUTOSSH_POLL        - how often to check the connection (seconds)
    AUTOSSH_FIRST_POLL  - time before first connection check (seconds)
    AUTOSSH_PORT        - port to use for monitor connection
    AUTOSSH_DEBUG       - turn logging to maximum verbosity and log to
                          stderr

</pre></body></html>
