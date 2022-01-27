<?php
#########################################################################################

function  getResource( $resPath, $scope, $parms ) {
	global $clientid;
	global $clientsecret;
	global $access_token;
	global $refresh_token;
	global $debug;
	
	$url = 'https://hbde-portal.liga.nu/rs'. $resPath;
	
	if ( $debug ) { echo "+ getResource request ( $url ) scope=$scope\n";  }

	$httpcode = 0;
	$refresh  = 0;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,1);
	curl_setopt($ch, CURLOPT_CAINFO,'nuliga-cabundle.crt');
	curl_setopt($ch, CURLOPT_CAPATH,'nuliga-cabundle.crt');
	
	if ( ! empty( $parms )) {
		$url .= "?". http_build_query( $parms );
	}
	curl_setopt($ch, CURLOPT_URL, $url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $access_token,
		'Accept: application/json'
	));      
	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ( $httpCode == 200 ) {
		if ( $debug ) { echo "Return code 200: ok\n";  }
		return( $result );
	}
	if ( $httpCode == 401 ) {
		if ( $debug ) { echo "Return code 401: trying authenticationRefresh\n";  }
		if ( authentificationRefresh( $scope ) != 200 ) {
			if ( $debug ) { echo "authenticationRefresh failed.\n";  }
			return( "error" );
		}
	}
	if ( $debug ) { echo "trying new getResource request\n";  }
	// New access token requested, lets send the new resource request.
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $access_token,
		'Accept: application/json'
	));      
	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ( $httpCode == 200 ) {
		if ( $debug ) { echo "getResource request success\n";  }
		return( $result );
	}
	if ( $debug ) { echo "getResource request failed.\n";  }
	return( "error" );
}

#########################################################################################
#########################################################################################
#########################################################################################

function  authentificationRefresh( $scope ) {
	global $clientid;
	global $clientsecret;
	global $access_token;
	global $refresh_token;
	global $debug;

	if ( $debug ) { echo "+ authentificationRefresh( $scope )\n"; }
	
	$data = array(
		"grant_type" 	=> "refresh_token",
		"refresh_token" => $refresh_token,
		"client_id" 	=> $clientid,
		"client_secret" => $clientsecret,
		"scope" 	=> "nuPortalRS_". $scope
	);
	$rc = sendOAuth( $data );
	if ( $rc == 200 ) { return( $rc ); }
	
	if ( $debug ) { echo "authentificationRefresh failed, trying new authenticationRequest\n"; }
	return( authenticationRequest( $scope ));
}

#########################################################################################
#########################################################################################
#########################################################################################

function  authenticationRequest( $scope ) {
	global $clientid;
	global $clientsecret;
	global $access_token;
	global $refresh_token;
	global $debug;

	if ( $debug ) { echo "+ authenticationRequest( $scope )\n"; }
	$data = array(
		"grant_type" 	=> "client_credentials",
		"client_id" 	=> $clientid,
		"client_secret" => $clientsecret,
		"scope" 	=> "nuPortalRS_". $scope
	);
	return( sendOAuth( $data ));
}

#########################################################################################
#########################################################################################
#########################################################################################

function sendOAuth( $data ) {
	global $access_token;
	global $refresh_token;
	global $debug;

	if ( $debug ) { echo "+ sendOAuth()\n"; }
	
	$url = 'https://hbde-portal.liga.nu/rs/auth/token';
	$postdata = http_build_query( $data );
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,1);
        curl_setopt($ch, CURLOPT_CAINFO,'nuliga-cabundle.crt');
        curl_setopt($ch, CURLOPT_CAPATH,'nuliga-cabundle.crt');
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Cache-control: no-cache',
		'Content-Type: application/x-www-form-urlencoded'
	));   
	curl_setopt($ch, CURLOPT_POST, count($data) );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata );
	
	$result = curl_exec($ch);
	$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);
	
	if ( $httpCode == 200 ) {
		if (( $jsonresult = json_decode( $result, true )) != null ) { 
			if ( isset( $jsonresult['error'] )) { 
				return( $httpCode );
			}
			if ( isset( $jsonresult['access_token'] )) { 
				$access_token  = $jsonresult['access_token'];
				$refresh_token = $jsonresult['refresh_token'];
				$filecontent = "<?php\n\$access_token=\"$access_token\";\n\$refresh_token=\"$refresh_token\";\n\$lastupdate='". date("d.m.Y H:i:s") ."';\n?>\n";
				file_put_contents( "accesstoken.php", $filecontent );
				return( $httpCode );

			} else {
				echo " Error: Unknown JSON result\n";
				foreach ( $jsonresult as $k => $v ) { echo "  $k: $v\n"; }
				return( $httpCode );
				}
		} else {
			echo "*** no JSON string received\n[$result]\n";
		}
		return( $httpCode );
	} 

	echo " Error: http code = $httpCode\n";
	if (( $jsonresult = json_decode( $result, true )) != null ) { 
		if ( isset( $jsonresult['error'] )) { 
			echo " Error: ". $jsonresult['error'] ."\n  description = ". $jsonresult['error_description'] ."\n";
		} else {
			echo " Error: unknown JSON result\n";
			foreach ( $jsonresult as $k => $v ) { echo "  $k: $v\n"; }
		}
	} else {
		echo " Error: no JSON string received\n[$result]\n";
	}
	return( $httpCode );
}
?>
