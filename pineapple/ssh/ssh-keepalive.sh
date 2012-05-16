#!/bin/sh
# ---------------------------------------------------------------------------------------
# Script simple que verifica si SSH está conectado y lo reinicia en caso que no lo esté.
# ---------------------------------------------------------------------------------------
logger "SSH: Script de Persistencia Ejecutado"
if ! ( pidof autossh); then
	/www/pineapple/ssh/ssh-connect.sh &
	logger "SSH: La conexión parece inactiva. Se ha ejecutado /www/pineapple/ssh/ssh-connect.sh"
else
	logger "SSH: La conexión parece activa."
fi
