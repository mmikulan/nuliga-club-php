<?php

date_default_timezone_set( "Europe/Berlin" );

require_once "credentials.php";
require_once "functions.php";
require_once "accesstoken.php";

if (( time() > mktime( 0,0,0, 6,15,date("Y"))) && ( time() < mktime( 0,0,0,9,15,date("Y")))) { 
	$fromDate = date( 'Y-m-d', strtotime( "-2 weeks", mktime( 0,0,0, 9,15,date("Y"))));
	$toDate   = date( 'Y-m-d', strtotime( "+4 weeks", mktime( 0,0,0, 9,15,date("Y"))));
} else {
	$fromDate = date( 'Y-m-d', strtotime( "-2 weeks" ));
	$toDate   = date( 'Y-m-d', strtotime( "+4 weeks" ));
}

# echo "$fromDate $toDate\n";

$all = array();
$perteam = array();

$result = getResource("/2014/federations/BHV/clubs/". $nuligateamid ."/meetings", $scope, [ "fromDate" => $fromDate, "toDate" => $toDate, "maxResults" => 100 ] );

if ( $data = json_decode( $result, true )) {
	foreach( $data['meetings']['meetingAbbr'] as $m ) {
		
		if (( $m['teamHome'] == 'spielfrei*' ) || ( $m['teamGuest'] == 'spielfrei*' )) { continue; }
		
		if ( $m['teamHomeClubNr'] == $nuligateamid ) { $tid = $m['teamHomeId']; $athome = true; } else { $tid = $m['teamGuestId']; $athome = false; }
		$day = date( "Ymd.Hi", strtotime( $m['scheduled'] ));
		$all[ $m['scheduled'] . $tid ] = array( 
			"teamId" => $tid,
			"time"   => $m['scheduled'],
			"liga"   => $m['leagueNickname'],
			"halle"  => $m['courtHallNumbers'],
			"teamA"  => $m['teamHome'],
			"scoreA" => $m['matchesHome'],
			"teamB"  => $m['teamGuest'],
			"scoreB" => $m['matchesGuest'],
			"atHome" => $athome,
			"done"   => $m['isCompleted'] );
		if ( ! isset( $perteam[ $tid ] )) { $perteam[ $tid ] = array(); }
		array_push( $perteam[ $tid ], array( 
			"time"   => $m['scheduled'],
			"liga"   => $m['leagueNickname'],
			"halle"  => $m['courtHallNumbers'],
			"teamA"  => $m['teamHome'],
			"scoreA" => $m['matchesHome'],
			"teamB"  => $m['teamGuest'],
			"scoreB" => $m['matchesGuest'],
			"atHome" => $athome,
			"done"   => $m['isCompleted'] ));
	}
}

# file_put_contents( "result_schedule3.json", $result );

file_put_contents( $nuligawebdir ."/nuliga_schedule.json", json_encode( $all, JSON_PRETTY_PRINT ) );
file_put_contents( $nuligawebdir ."/nuliga_schedule_team.json", json_encode( $perteam, JSON_PRETTY_PRINT ) );

?>
