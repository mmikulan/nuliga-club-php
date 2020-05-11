<?php
$nuligadir = "nuliga" # folder where PHP scripts are stored 

chdir( $nuligadir );

date_default_timezone_set( "Europe/Berlin" );

$now = time();
$hour = date("G", $now );
$weekday = date("N", $now );

file_put_contents( "nuliga-cron-status", "Cron Start ". date( "d.m.Y H:i" )." ----\n", FILE_APPEND);

if (( $weekday >= 6 ) && ( $hour >= 6 ) && ( $hour <= 22 )) {

	file_put_contents( "nuliga-cron-status", "schedule ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getschedule.php";

	file_put_contents( "nuliga-cron-status", "ranking ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getranking.php";

}

if (( $hour == 6 )||( $hour == 12 )||( $hour == 18 )) {
	file_put_contents( "nuliga-cron-status", "schedule ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getschedule.php";
}

if (( $hour == 0 )) {
	file_put_contents( "nuliga-cron-status", "getspielplan ". date( "d.m.Y H:i" )."\n", FILE_APPEND);
	include "getspielplan.php";
}

file_put_contents( "nuliga-cron-status", "Cron End ". date( "d.m.Y H:i" ) ." ----\n", FILE_APPEND);
?>
