<?php

require_once "credentials.php";
require_once "functions.php";
require_once "accesstoken.php";

$teams = json_decode( file_get_contents( $nuligawebdir ."/nuliga_teams.json", true ));

foreach ( $teams as $tid => $tdata ) {
	$jsondata = getResource("/2014/federations/BHV/clubs/". $nuligateamid ."/teams/". $tid ."/table", $scope, array() );
	$data = json_decode( $jsondata, true );

	if ( ! isset( $output[ $tid ] )) { $output[ $tid ] = array(); }

	foreach( $data['groupTable'] as $grp ) {
		
		$found = false;
		foreach ( $grp['groupTableTeam'] as $gteam ) {
			if ( $gteam['teamId'] == $tid ) { $found = true; break; }
		}
		if ( ! $found ) { continue; }
		
		$rr = (strpos( $grp['group'], "Rückrunde" ) > 0) || (strpos( $grp['group'], "RR ") > 0);
		if ( isset( $output[ $tid ]['liga'] ) && ( ! $rr )) { continue; }
		
		$output[ $tid ]['liga'] = $grp['group'];
		unset( $output[ $tid ]['tabelle'] );
		foreach ( $grp['groupTableTeam'] as $gteam ) {
			$output[ $tid ]['tabelle'][ $gteam['tableRank'] ] = array (
				"team" 	 => $gteam['team'],
				"own" 	 => ( $gteam['teamId'] == $tid ),
				"matches" => $gteam['meetings'],
				"ptsA" 	 => $gteam['ownPoints'],
				"ptsB" 	 => $gteam['otherPoints'],
				"goalsA" => $gteam['ownMatches'],
				"goalsB" => $gteam['otherMatches'] );
		}
	}
}
file_put_contents( $nuligawebdir ."/nuliga_ranking.json", json_encode( $output, JSON_PRETTY_PRINT ));
?>

