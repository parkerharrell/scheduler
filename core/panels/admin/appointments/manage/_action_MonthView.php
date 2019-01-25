<?php
$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');

$t->setEndMonth();
$endMonth = $t->formatDate_Db();
$fullEnd = $t->getTimestamp();
$t->setStartMonth();
$startMonth = $t->formatDate_Db();
$fullStart = $t->getTimestamp();

// build slots
$thisDate = $startMonth;
do {
	$startDay = $t->getStartDay();
	$thisDate = $t->formatDate_Db();
	$t->modify( '+1 day' );
	$endDay = $t->getTimestamp();
	$SLOTS[] = array( $startDay, $endDay, $thisDate );

	$DATES[ $thisDate ] = array( $startDay, $endDay );
	$APPS_BY_DATE[ $thisDate ] = array();
	}
while ($thisDate < $endMonth );
?>