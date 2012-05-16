#!/bin/sh
# ---------------------------------------------------------------------
# Verifica si la conexión 3G / WAN está activa y la sube si no lo está.
# ---------------------------------------------------------------------

SERVER="8.8.8.8" # Este es el servidor DNS de Google's DNS server - si está caido tenemos graves problemas
logger "3G: Script de Persistencia Ejecutado."

if ! ( ifconfig 3g-wan2); then
	logger "3G: La interfaz 3g-wan2 parece inactiva. Ejecutando de nuevo script de conexión."
	/www/pineapple/3g/3g.sh
else
	logger "3G: La interfaz 3g-wan2 parece activa."

	if ! ( ping -q -c 1 -W 10 $SERVER > /dev/null || ping -q -c 1 -W 10 $SERVER > /dev/null || ping -q -c 1 -W 10 $SERVER > /dev/null ); then
		logger "3G: Interfaz 3g-wan2 activa, sin embargo la conexión a Internet parece haberse caido. Verifique si el modem está activo. Desconecte y conecte de nuevo el modem a la piña."
		logger "3G: Ejecutando ifup wan2. Esperando que esto resulva el problema."
		ifup wan2
		
	else
		logger "3G: Interfaz 3g-wan2 activa y la conexión a Internet parece estar arriba. woot"
	fi
fi
