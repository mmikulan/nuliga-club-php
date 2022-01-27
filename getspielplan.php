<?php

// chdir( "nuliga" );

date_default_timezone_set( "Europe/Berlin" );

require_once "credentials.php";
require_once "functions.php";
require_once "accesstoken.php";

if ( date("m") > 6 ) {
	$fromDate = date("Y"). "-09-01";
	$toDate =   date("Y-m-d", strtotime("+10 months", strtotime( $fromDate )));
} else {
	$toDate =   date("Y"). "-07-01";
	$fromDate = date("Y-m-d", strtotime("-10 months", strtotime( $toDate )));
}
					 
$result = getResource("/2014/federations/BHV/clubs/". $nuligateamid ."/meetings", $scope, [ "fromDate" => $fromDate, "toDate" => $toDate, "maxResults" => 500 ] );

if ( $data = json_decode( $result, true )) {
	$fh = fopen( $nuligawebdir ."/spielplan.".  date("ymd") .".csv", "w" );
	foreach( $data['meetings']['meetingAbbr'] as $m ) {
		fputs( $fh, date( "d.m.Y H:i", strtotime( $m['scheduled'] )) .";".
					$m['leagueNickname'] .";".
					$m['courtHallNumbers'] .";".
					$m['teamHome']  .";".
					$m['teamGuest'] .";".
					"\n" );
	}
	fclose( $fh );
	file_put_contents( $nuligawebdir ."/nuliga_spielplan.json", json_encode( $data['meetings']['meetingAbbr'] ));
	
} else {
	echo "Keine Termine gefunden";
}

?>
