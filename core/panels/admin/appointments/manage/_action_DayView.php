<?php
$howManySlots = ( 24 * 60 ) / NTS_TIME_UNIT;

$fullStart = $t->getStartDay();
$thisDate = $t->formatDate_Db();
$APPS_BY_DATE[ $thisDate ] = array();

for( $s = 0; $s < $howManySlots; $s++ ){
	$startTime = $t->getTimestamp();
	$t->modify( '+' . NTS_TIME_UNIT . ' minutes' );
	$endTime = $t->getTimestamp();
	$SLOTS[] = array( $startTime, $endTime );
	}
$fullEnd = $t->getTimestamp();

$DATES[ $thisDate ] = array( $fullStart, $fullEnd );
?>