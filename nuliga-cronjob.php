<?php
# Currently the script assumes it is invoked daily once per hour by the cron service. To minimize the load on the nuLiga servers but keep the 
# information updated on an ongoing basis, a balanced approach was chosen. E.g. once per hour for ranking updates on the weekends
# A different execution theme can be chosen by changing the if conditions below, but please do not overload the servers

$nuligadir = "nuliga" # folder where PHP scripts are stored 

chdir( $nuligadir );

date_default_timezone_set( "Europe/Berlin" );

$now = time();
$hour = date("G", $now );
$weekday = date("N", $now );

file_put_contents( "nuliga-cron-status", "Cron Start ". date( "d.m.Y H:i" )." ----\n", FILE_APPEND);

# updates to teams list and mapping, done every month (at minimum once at the start of the season)
if (( time() - filemtime( $nuligawebdir ."/nuliga_teams.json" )) >  30*24*60*60 ) {
	file_put_contents( "nuliga-cron-status", "teams ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getteams.php";
}

# updates to current ranking, done only on the weekend between 6:00 and 22:00
if (( $weekday >= 6 ) && ( $hour >= 6 ) && ( $hour <= 22 )) {
	file_put_contents( "nuliga-cron-status", "ranking ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getranking.php";
}

# updates to current match schedule, done only once at 6:00, 12:00 and 18:00 every day
if (( $hour == 6 )||( $hour == 12 )||( $hour == 18 )) {
	file_put_contents( "nuliga-cron-status", "schedule ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getschedule.php";
}

# updates to the complete spielplan once per day
if (( $hour == 0 )) {
	file_put_contents( "nuliga-cron-status", "getspielplan ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getspielplan.php";
}

file_put_contents( "nuliga-cron-status", "Cron End ". date( "d.m.Y H:i" ) ." ----\n", FILE_APPEND);
?>
