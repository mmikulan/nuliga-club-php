<?php

require_once "credentials.php";
require_once "functions.php";
require_once "accesstoken.php";

$result = getResource("/2014/federations/BHV/clubs/". $nuligateamid, $scope, [ "maxResults" => 300 ] );

echo $result;

exit;
?>
