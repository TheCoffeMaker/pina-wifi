<?php

putenv('LD_LIBRARY_PATH='.getenv('LD_LIBRARY_PATH').':/usb/lib:/usb/usr/lib');   
putenv('PATH='.getenv('PATH').':/usb/usr/bin:/usb/usr/sbin');

if(!file_exists("/etc/ssmtp/") && file_exists("/usb/etc/ssmtp/"))
{
	exec("ln -s /usb/etc/ssmtp/ /etc/ssmtp");
}

$module_name = "logcheck";
$module_path = "/www/pineapple/modules/logcheck/";
$module_version = "1.1";

$match_path = $module_path."rules/match";
$ignore_path = $module_path."rules/ignore";
$smtp_path = "/etc/ssmtp/ssmtp.conf";

$is_ssmtp_installed = exec("which ssmtp") != "" ? 1 : 0;
$is_daemon_installed = exec("cat /etc/crontabs/root | grep logcheck") != "" ? 1 : 0;
$daemon_update = exec("logread | grep logcheck_report.sh | tail -1 | awk '{print $1\" \"$2\" \"$3;}'");

$is_logcheck_running = exec("ps auxww | grep logread | grep -v -e grep") != "" ? 1 : 0;
$is_logcheck_onboot = exec("cat /etc/rc.local | grep logcheck") != "" ? 1 : 0;

$configArray = explode("\n", trim(file_get_contents($module_path."logcheck.conf")));
$To = $configArray[0];
$From = $configArray[1];
$Subject = $configArray[2];

$cron_time = "*/30 * * * *";
$cron_task = $module_path."logcheck_report.sh";

?>