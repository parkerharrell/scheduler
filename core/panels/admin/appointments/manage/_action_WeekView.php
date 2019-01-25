<?php
$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');

$daysToShow = 7;
$NTS_VIEW['daysToShow'] = $daysToShow;

$howManySlots = ( $daysToShow * 24 * 60 ) / NTS_TIME_UNIT;

$fullStart = $t->getStartDay();
for( $s = 0; $s < $howManySlots; $s++ ){
	$startTime = $t->getTimestamp();
	$t->modify( '+' . NTS_TIME_UNIT . ' minutes' );
	$endTime = $t->getTimestamp();
	$SLOTS[] = array( $startTime, $endTime );
	}
$fullEnd = $t->getTimestamp();

$t->setTimestamp( $fullStart );
for( $d = 0; $d < $daysToShow; $d++ ){
	$startDay = $t->getStartDay();
	$thisDate = $t->formatDate_Db();
	$t->modify( '+1 day' );
	$endDay = $t->getTimestamp();

	$DATES[ $thisDate ] = array( $startDay, $endDay );
	$APPS_BY_DATE[ $thisDate ] = array();
	}
?>