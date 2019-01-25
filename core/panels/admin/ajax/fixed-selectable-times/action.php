<?php
$NTS_VIEW['skipMenu'] = true;
$current = $req->getParam( 'current' );
if( strlen($current) )
	$current = explode( '||', $current );
else
	$current = array();
	
$minValue = $req->getParam( 'min' );
$maxValue = $req->getParam( 'max' ); 

$startTs = 0;
$endTs = 24 * 60 * 60;

$NTS_VIEW['entries'] = array();	
$ts = $startTs - 60 * NTS_TIME_UNIT;
while( $ts <= $endTs ){
	$ts += 60 * NTS_TIME_UNIT;

	if( $ts < $minValue )
		continue;
	if( $ts >= $maxValue )
		continue;
	if( in_array($ts, $current ) )
		continue;

	$NTS_VIEW['entries'][] = $ts;
	}
?>