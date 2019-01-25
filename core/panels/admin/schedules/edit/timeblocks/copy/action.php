<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$scheduleId = $req->getParam( '_id' );
$dayIndex = $req->getParam( 'day_from' );

/* this day timeblocks */
$sql =<<<EOT
SELECT
	id
FROM 
	{PRFX}timeblocks
WHERE
	schedule_id = $scheduleId AND
	applied_on = $dayIndex
EOT;
$timeblocksInfo = array(); 
$result = $ntsdb->runQuery( $sql );
while( $s = $result->fetch() ){
	$t = new ntsObject( 'timeblock' );
	$t->setId( $s['id'] );
	$timeblocksInfo[] = $t->getByArray();
	}

/* delete other day timeblocks */
$sql =<<<EOT
SELECT
	id
FROM 
	{PRFX}timeblocks
WHERE
	schedule_id = $scheduleId AND
	applied_on <> $dayIndex
EOT;
$result = $ntsdb->runQuery( $sql );
while( $s = $result->fetch() ){
	$t = new ntsObject( 'timeblock' );
	$t->setId( $s['id'] );
	$cm->runCommand( $t, 'delete' );
	}

for( $day = 0; $day <= 6; $day++ ){
	if( $day == $dayIndex )
		continue;
	reset( $timeblocksInfo );
	foreach( $timeblocksInfo as $tbi ){
		unset( $tbi['id'] );
		$tbi['applied_on'] = $day;
		$object = new ntsObject( 'timeblock' );
		$object->setByArray( $tbi );
		$cm->runCommand( $object, 'create' );
		}
	}
ntsView::setAnnounce( M('Time Slots') . ': ' . M('Duplicate') . ': ' . M('OK'), 'ok' );

/* continue to timeblocks list */
$forwardTo = ntsLink::makeLink( '-current-/..', '', array('id' => $scheduleId) );
ntsView::redirect( $forwardTo );
exit;
?>