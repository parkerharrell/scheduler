<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$scheduleId = $req->getParam( '_id' );

/* schedule info */
$schedule = new ntsObject( 'schedule' );
$schedule->setId( $scheduleId );
$scheduleInfo = $schedule->getByArray();

// check if the schedule is less than 1 week then we can show dates and hide some days //
$t = new ntsTime();
$validFrom = $t->timestampFromDbDate( $schedule->getProp('valid_from') );
$validTo = $t->timestampFromDbDate( $schedule->getProp('valid_to') );
$duration = $validTo - $validFrom;
$limitDays = array();

if( $duration <= 7 * 24 * 60 * 60 ){
	$t->setTimestamp( $validFrom );
	while( $t->getTimestamp() <= $validTo ){
		$limitDays[ $t->getWeekday() ] = $t->formatDate();
		$t->modify( '+1 day' );
		}
	}

$NTS_VIEW['limitDays'] = $limitDays;
$NTS_VIEW['scheduleInfo'] = $scheduleInfo;

/* timeblocks */
$sql =<<<EOT
SELECT
	id
FROM 
	{PRFX}timeblocks
WHERE
	schedule_id = $scheduleId
ORDER BY
	applied_on ASC, starts_at ASC
EOT;
$timeblocksInfo = array(); 
$result = $ntsdb->runQuery( $sql );
while( $s = $result->fetch() ){
	$tb = ntsObjectFactory::get( 'timeblock' );
	$tb->setId( $s['id'] );
	$timeblocksInfo[] = $tb->getByArray();
	}
$NTS_VIEW['timeblocksInfo'] = $timeblocksInfo;
?>