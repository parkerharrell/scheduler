<?php
$ntsdb =& dbWrapper::getInstance();
$locationId = $object->getId();

$t = new ntsTime();
$today = $t->formatDate_Db();
$todayTimestamp = $t->timestampFromDbDate( $today );

/* delete blocks where this location is the only one */
$sql =<<<EOT
SELECT
	obj_id,
	(
	SELECT 
		COUNT(*)
	FROM
		{PRFX}objectmeta AS meta1 
	WHERE
		meta1.obj_class = "schedule" AND
		meta1.meta_name = "_location" AND
		meta2.obj_id = meta1.obj_id
	) AS loc_count
FROM
	{PRFX}objectmeta AS meta2
WHERE
	obj_class = "schedule" AND
	meta_name = "_location" AND
	meta_value = $locationId
HAVING 
	(loc_count = 1)
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['obj_id'];
		$subObject = new ntsObject( 'schedule' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}

/* reject appointments */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	location_id = $locationId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );

		$params = array(
			'reason' => 'Location closed',
			);
		/* silent if app is earlier than today */
		if( $subObject->getProp('starts_at') < $todayTimestamp ){
			$params['_silent'] = true;
			}
		$this->runCommand( $subObject, 'reject', $params );
		}
	}

?>