#!/bin/sh
# ---------------------------------------------------------
# Script de Conexión para la Piña WiFi.
# 
# Versión: 2012-05-15 (No-Oficial)
# Soporta:
# 
# ZTE MF591 (T-Mobile) -dkitchen 
# Novatel MC760 (Virgin) -dkitchen
# Novatel MC760 (Ting) -dkitchen
# Sierra 598u (Ting) -brianzimm
# ZTE MF616 (TIGO-COL) -D4rkOperat0r
# ZTE MF100 (TIGO-COL) -D4rkOperat0r
# ZTE MF668A(Comcel-COL) -D4rkOperat0r
# Huawei E173s-6 (Movistar-COL) -DragonJAR
# 
# Actualizado: wifipineapple.com
# ---------------------------------------------------------


# -------------------------------------------------------------
# Configura /etc/ppp/options con configuraciones que funcionan
# -------------------------------------------------------------
echo "
logfile /dev/null
noaccomp
nopcomp
nocrtscts
lock
maxfail 0" > /etc/ppp/options

# ----------------------------------------------------------------------------------------
# Verifica los VID/PID USB, luego cambia el modo de almacenamiento por el de modem serial
# ----------------------------------------------------------------------------------------
echo "Buscando Modems 3G conectados"
logger "3G: Ejecutando script de conexión, buscando modems"
MODEM=$(lsusb | awk '{ print $6 }')
echo $MODEM

case "$MODEM" in

*19d2:1523*)    echo "ZTE MF591 (T-Mobile) detectado. Cambiando el modo de operación"
                uci delete network.wan2         
                uci set network.wan2=interface  
                uci set network.wan2.ifname=ppp0           
                uci set network.wan2.proto=3g           
                uci set network.wan2.service=umts       
                uci set network.wan2.device=/dev/ttyUSB0     
                uci set network.wan2.apn=epc.tmobile.com     
                uci set network.wan2.username=internet       
                uci set network.wan2.password=internet
                uci set network.wan2.defaultroute=1    
                uci commit network 
				usb_modeswitch -v 19d2 -p 1523 -V 19d2 -P 1525 -M 5553424312345678000000000000061b000000020000000000000000000000 -n 1 -s 20
				sleep 10; rmmod usbserial
				sleep 3; insmod usbserial vendor=0x19d2 product=0x1525
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT
		
		;;
*1410:6002* | *1410:5031*) echo "Novatel MC760 (Virgin Mobile) detectado. Cambiando el modo de operación"
				uci delete network.wan2
				uci set network.wan2=interface
				uci set network.wan2.ifname=ppp0
				uci set network.wan2.proto=3g
				uci set network.wan2.service=cdma
				uci set network.wan2.device=/dev/ttyUSB0
				uci set network.wan2.username=internet
				uci set network.wan2.password=internet
				uci set network.wan2.defaultroute=1
				uci set network.wan2.ppp_redial=persist
				uci set network.wan2.peerdns=0
				uci set network.wan2.dns=8.8.8.8
				uci set network.wan2.keepalive=1
				uci set network.wan2.pppd_options=debug
				uci set network.wan2.pppd_options=noauth
				uci commit network
				usb_modeswitch -v 1410 -p 5031 -V 1410 -P 6002 -M 5553424312345678000000000000061b000000020000000000000000000000 -n 1 -s 20
				sleep 10; rmmod usbserial
				sleep 3; insmod usbserial vendor=0x1410 product=0x6002
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

		;;
*1410:5030*)	echo "Novatel MC760 (Ting) detectado. Cambiando el modo de operación"
				uci delete network.wan2
				uci set network.wan2=interface
				uci set network.wan2.ifname=ppp0
				uci set network.wan2.proto=3g
				uci set network.wan2.service=cdma
				uci set network.wan2.device=/dev/ttyUSB0
				uci set network.wan2.username=internet
				uci set network.wan2.password=internet
				uci set network.wan2.defaultroute=1
				uci set network.wan2.ppp_redial=persist
				uci set network.wan2.peerdns=0
				uci set network.wan2.dns=8.8.8.8
				uci set network.wan2.keepalive=1
				uci set network.wan2.pppd_options=debug
				uci set network.wan2.pppd_options=noauth
				uci commit network
				usb_modeswitch -v 1410 -p 5030 -V 1410 -P 6000 -M 5553424312345678000000000000061b000000020000000000000000000000 -n 1 -s 20
				sleep 10; rmmod usbserial
				sleep 3; insmod usbserial vendor=0x1410 product=0x6000
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

		;;
*1199:0025*)    echo "Sierra 598u (Ting) detectado. Cambiando el modo de operación"
                uci delete network.wan2
                uci set network.wan2=interface
                uci set network.wan2.ifname=ppp0
                uci set network.wan2.proto=3g
                uci set network.wan2.service=cdma
                uci set network.wan2.device=/dev/ttyUSB0
                uci set network.wan2.username=internet
                uci set network.wan2.password=internet
                uci set network.wan2.defaultroute=1
                uci set network.wan2.ppp_redial=persist
                uci set network.wan2.peerdns=0
                uci set network.wan2.dns=8.8.8.8
                uci set network.wan2.keepalive=1
                uci set network.wan2.pppd_options=debug
                uci set network.wan2.pppd_options=noauth
                uci commit network
                usb_modeswitch -v 1199 -p 0025
                sleep 10; rmmod usbserial
                sleep 3; insmod usbserial vendor=0x1199 product=0x0025
                sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
                logger "3G: firewall detenido"
                iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
                iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
                iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

                ;;
*12d1:1506*)    echo "Huawei E398 detectado. Cambiando el modo de operación"
				uci delete network.wan2         
				uci set network.wan2=interface  
				uci set network.wan2.ifname=ppp0           
				uci set network.wan2.proto=3g           
				uci set network.wan2.service=umts       
				uci set network.wan2.device=/dev/ttyUSB0     
				uci set network.wan2.apn=<CHANGE HERE YOUR APN>
				uci set network.wan2.username=<CHANGE HERE YOUR USERNAME>     
				uci set network.wan2.password=<CHANGE HERE YOUR PASSWORD>
				uci set network.wan2.defaultroute=1    
				uci commit network 
				usb_modeswitch -v 12d1 -p 1506
				sleep 10; rmmod usbserial
				sleep 3; insmod usbserial vendor=0x12d1 product=0x1506
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT
				
				;;
*19d2:0031*)    echo "ZTE MF616 (TIGO-COL) detectado. Cambiando el modo de operación"
                uci delete network.wan2         
                uci set network.wan2=interface  
                uci set network.wan2.ifname=ppp0           
                uci set network.wan2.proto=3g           
                uci set network.wan2.service=umts       
                uci set network.wan2.device=/dev/ttyUSB2     
                uci set network.wan2.apn=web.colombiamovil.com.co     
                uci set network.wan2.defaultroute=1    
                uci commit network 
				sleep 10; rmmod usbserial
				sleep 5; insmod usbserial vendor=0x19d2 product=0x0031
				#sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				#logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

                ;;
*19d2:2003*)    echo "ZTE MF100 (TIGO-COL) detectado. Cambiando el modo de operación"
                uci delete network.wan2         
                uci set network.wan2=interface  
                uci set network.wan2.ifname=ppp0           
                uci set network.wan2.proto=3g           
                uci set network.wan2.service=umts       
                uci set network.wan2.device=/dev/ttyUSB3     
                uci set network.wan2.apn=web.colombiamovil.com.co     
                uci set network.wan2.defaultroute=1    
                uci commit network 
				sleep 10; rmmod usbserial
				sleep 5; insmod usbserial vendor=0x19d2 product=0x2003
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT
		
		;;
*19d2:0117*)    echo "ZTE MF668A (COMCEL-COL) detectado. Cambiando el modo de operación"
                uci delete network.wan2         
                uci set network.wan2=interface  
                uci set network.wan2.ifname=ppp0           
                uci set network.wan2.proto=3g           
                uci set network.wan2.service=umts       
                uci set network.wan2.device=/dev/ttyUSB2     
                uci set network.wan2.apn=internet.comcel.com.co     
                uci set network.wan2.defaultroute=1    
                uci commit network 
				sleep 10; rmmod usbserial
				sleep 5; insmod usbserial vendor=0x19d2 product=0x0117
				#sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				#logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

        ;;
*12d1:1c05*)	echo "Huawei E173s-6 detectado. Cambiando el modo de operación"
				uci delete network.wan2 
				uci set network.wan2=interface 
				uci set network.wan2.ifname=ppp0 
				uci set network.wan2.proto=3g 
				uci set network.wan2.service=umts 
				uci set network.wan2.device=/dev/ttyUSB0
				uci set network.wan2.apn=internet.movistar.com.co
				uci set network.wan2.username=movistar
				uci set network.wan2.password=movistar
				uci set network.wan2.defaultroute=1 
				uci commit network 
				sleep 10; rmmod usbserial
				sleep 3; insmod usbserial vendor=0x12d1 product=0x1c05
				sleep 5; /etc/init.d/firewall disable; /etc/init.d/firewall stop
				logger "3G: firewall detenido"
				iptables -t nat -A POSTROUTING -s 172.16.42.0/24 -o 3g-wan2 -j MASQUERADE
				iptables -A FORWARD -s 172.16.42.0/24 -o 3g-wan2 -j ACCEPT 
				iptables -A FORWARD -d 172.16.42.0/24 -m state --state ESTABLISHED,RELATED -i 3g-wan2 -j ACCEPT

		;;
esac