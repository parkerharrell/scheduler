<?php
$ntsdb =& dbWrapper::getInstance();
$NTS_VIEW['newTimeoffs'] = array();
$NTS_VIEW['oldTimeoffs'] = array();

if( $NTS_VIEW['FIXED_RESOURCE'] ){
	$resourceSchedules = array( $NTS_VIEW['FIXED_RESOURCE'] );
	}
else {
	$resourceSchedules = array_merge( $NTS_VIEW['RESOURCE_SCHEDULE_VIEW'], $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'] );
	}

$now = time();
if( $resourceSchedules ){
	$resWhere = "WHERE resource_id IN (" . join( ',', $resourceSchedules) . ')';;

/* upcoming */
	$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}timeoffs
$resWhere AND
ends_at >= $now
ORDER BY
	starts_at ASC, resource_id ASC
EOT;

	$result = $ntsdb->runQuery( $sql );
	$newTimeoffs = array();
	while( $v = $result->fetch() )
		$newTimeoffs[] = $v;
	$NTS_VIEW['newTimeoffs'] = $newTimeoffs;

/* old */
	$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}timeoffs
$resWhere AND
ends_at < $now
ORDER BY
	starts_at DESC, resource_id ASC
EOT;

	$result = $ntsdb->runQuery( $sql );
	$oldTimeoffs = array();
	while( $v = $result->fetch() )
		$oldTimeoffs[] = $v;
	$NTS_VIEW['oldTimeoffs'] = $oldTimeoffs;
	}
?>