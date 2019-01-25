<?php
if( ! $return )
	return;
if( $this->dayMode )
	return;

reset( $return );
$timestamps = array_keys( $return );

$startTs = $timestamps[ 0 ];
if( count($timestamps) == 1 ){
	$endTs = $timestamps[ 0 ];
	}
else {
	$endTs = $timestamps[ count($timestamps) - 1 ];
	}

$t = new ntsTime( $startTs );
$startDate = $t->formatDate_Db();
$t = new ntsTime( $endTs );
$endDate = $t->formatDate_Db();

$deleteTs = array();
$rexDate = $startDate;
while( $rexDate <= $endDate ){
	$dayOn = false;
	$t = new ntsTime();
	$t->setDateDb( $rexDate );
	$endDay = $t->getEndDay(); 
	$t->setDateDb( $rexDate );
	$startDay = $t->getStartDay();

	reset( $timestamps );
	foreach( $timestamps as $ts ){
		if( $ts >= $endDay ){
			break;
			}
		if( $ts < $startDay ){
			continue;
			}
		if( $dayOn ){
			$deleteTs[] = $ts;
			}
		else {
			$dayOn = true;
			}
		}
	$t->modify( '+1 day' );
	$rexDate = $t->formatDate_Db();
	}

reset( $deleteTs );
foreach( $deleteTs as $ts ){
	unset( $return[$ts] );
	}
?>