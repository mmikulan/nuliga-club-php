<?php

# Achtung!
# Das Mapping von mehreren Teams pro Altersgruppe wurde noch nicht vollständig getestet. 
# Mit maximal zwei Teams pro Altersgruppe funktioniert es, als zB. mC1 und mC2.

require_once "functions.php";
require_once "credentials.php";
require_once "accesstoken.php";

# authentificationRequest();
authentificationRefresh( $scope );

$jsondata = getResource("/2014/federations/BHV/clubs/". $nuligateamid ."/teams", $scope, array() );

$data = json_decode( $jsondata, true );
if ( $debug ) echo "nuliga id = ". $nuligateamid ."\n";
// print_r( $data );
// exit;

$debug = false;

$mapping = array();

if ( date( "m" ) >= 7 ) { $nuligaseason =  date("Y") ."/". sprintf( "%02d", date("y")+1 ); }
else 					{ $nuligaseason = (date("Y")-1) ."/". date("y"); }

if ( $debug ) echo $nuligaseason;

$nicklist = Array();

foreach ( $data['teamSeason'] as $season ) {
	if ( $season['season']['name'] == $nuligaseason ) {
		foreach ( $season['teamChampionship'] as $champ ) {
			$region = $champ['championship']['name'];
			if ( $debug ) echo "  ". $champ['championship']['name']. "\n";
			foreach ( $champ['team'] as $team ) {
				
				$rr = ( strpos( $team['group'], "Rück" ) > 0 ) || ( strpos( $team['group'], "RR " ) > 0 );
				// if ( $rr ) { if ( $debug ) echo "TRUE\n"; } else { if ( $debug ) echo "FALSE\n"; }

				if (( ! isset( $entry[ $team['teamId'] ] )) || $rr ) {
					$entry[ $team['teamId'] ] = array( 
						"name" => $team['group'],
						"klasse" => $team['name'],
						"klasse2" => $team['contestTypeNickname'],
						"region" => $champ['championship']['name'] );
				}				
				$nick = $team['contestTypeNickname'];
				if ( strlen( $nick ) > 1 ) {
					$nick = lcfirst( $nick );
				} else {
					if ( $nick == "F" ) { $nick = "Damen"; }
					if ( $nick == "M" ) { $nick = "Herren"; }
				}
				
				if ( array_key_exists( $nick, $mapping )) {
					if ( $debug ) echo "!! $nick exists ";
					if ( $team['teamId'] == $mapping[ $nick ] ) {
						if ( $debug ) echo "same team (". $mapping[ $nick ] .")";
					} else {
						# more than one team in a single age range
						$newnum = 2;
						if ( array_key_exists( $nick, $nicklist )) {
							$newnum = $nicklist[ $nick ] + 1;
						}
						$mapping[ $nick . $newnum ] = $team['teamId'];
						$nicklist[ $nick ] = $newnum;
						if ( $debug ) echo " - added $newnum";
					}
					if ( $debug ) echo "\n";
				} else {
					$mapping[ $nick ] = $team['teamId'];
				}
				if ( $debug ) echo $team['teamId'] ." [". $nick ."] ";
				if ( $debug ) echo $team['contestTypeNickname'];
				if ( $debug ) echo " ". $team['name'] ." - ". $team['group'] ."\n";
			}
		}
	}
}
foreach ( $nicklist as $nick => $id ) {
	$mapping[ $nick .'1' ] = $mapping[ $nick ];
	unset( $mapping[ $nick ] );
}
ksort( $mapping );

# print_r( $nicklist );
# print_r( $mapping );

file_put_contents( $nuligawebdir ."/nuliga_teams.json", json_encode( $entry, JSON_PRETTY_PRINT));
file_put_contents( $nuligawebdir ."/nuliga_mapping.json", json_encode( $mapping, JSON_PRETTY_PRINT));
exit;

?>
