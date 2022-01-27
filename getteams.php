<?php

# unterstützt aktuell nur maximal 2 Teams pro Altersklasse also zB. mC1 und mC2, aber keine mC3

require_once "functions.php";
require_once "credentials.php";
require_once "accesstoken.php";

authentificationRefresh( $scope );

$jsondata = getResource("/2014/federations/BHV/clubs/". $nuligateamid ."/teams", $scope, array() );

$data = json_decode( $jsondata, true );
echo "nuliga id = ". $nuligateamid ."\n";


$mapping = array();

if ( date( "m" ) >= 7 ) { $nuligaseason =  date("Y") ."/". sprintf( "%02d", date("y")+1 ); }
else 					{ $nuligaseason = (date("Y")-1) ."/". date("y"); }

echo $nuligaseason;

foreach ( $data['teamSeason'] as $season ) {
	if ( $season['season']['name'] == $nuligaseason ) {
		foreach ( $season['teamChampionship'] as $champ ) {
			$region = $champ['championship']['name'];
			echo "  ". $champ['championship']['name']. "\n";
			foreach ( $champ['team'] as $team ) {
				
				$rr = ( strpos( $team['group'], "Rück" ) > 0 ) || ( strpos( $team['group'], "RR " ) > 0 );

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
				
				echo $team['teamId'] ." ". $team['contestTypeNickname'];
				echo " ". $team['name'] ." - ". $team['group'] ."\n";

				if ( array_key_exists( $nick, $mapping )) {
					echo "!! $nick exists ";
					if ( $team['teamId'] == $mapping[ $nick ] ) {
							echo "same team (". $mapping[ $nick ] .")";
					} else {
						$mapping[ $nick .'1' ] = $mapping[ $nick ];
						$mapping[ $nick .'2' ] = $team['teamId'];
						unset( $mapping[ $nick ] );
					}
					echo "\n";
				} else {
					if ( array_key_exists( $nick .'1', $mapping )) {
						echo "same team (". $mapping[ $nick .'1' ] .")";
					} elseif ( array_key_exists( $nick .'2', $mapping )) {
						echo "same team (". $mapping[ $nick .'2' ] .")";
					} else {
						$mapping[ $nick ] = $team['teamId'];
					}
					echo "\n";
				}
			}
		}
	}
}
file_put_contents( $nuligawebdir ."/nuliga_teams.json", json_encode( $entry, JSON_PRETTY_PRINT));
file_put_contents( $nuligawebdir ."/nuliga_mapping.json", json_encode( $mapping, JSON_PRETTY_PRINT));
exit;

