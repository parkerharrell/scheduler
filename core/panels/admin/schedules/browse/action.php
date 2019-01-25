<?php
$ntsdb =& dbWrapper::getInstance();
$NTS_VIEW['entries'] = array();

if( $NTS_VIEW['FIXED_RESOURCE'] ){
	$resourceSchedules = array( $NTS_VIEW['FIXED_RESOURCE'] );
	}
else {
	$resourceSchedules = array_merge( $NTS_VIEW['RESOURCE_SCHEDULE_VIEW'], $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'] );
	}

if( $resourceSchedules ){
	$resWhere = "WHERE resource_id IN (" . join( ',', $resourceSchedules) . ')';;

	/* entries */
	$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}schedules
$resWhere
ORDER BY
	resource_id ASC, title ASC
EOT;

	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		while( $e = $result->fetch() ){
			$h = ntsObjectFactory::get( 'schedule' );
			$h->setId( $e['id'] );	
			$NTS_VIEW['entries'][] = $h;
			}
		}
	}
?>