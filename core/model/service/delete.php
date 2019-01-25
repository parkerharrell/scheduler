<?php
$ntsdb =& dbWrapper::getInstance();
$serviceId = $object->getId();

/* delete schedules where this service is the only one */
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
		meta1.meta_name = "_service" AND
		meta2.obj_id = meta1.obj_id
	) AS ser_count
FROM
	{PRFX}objectmeta AS meta2
WHERE
	obj_class = "schedule" AND
	meta_name = "_service" AND
	meta_value = $serviceId
HAVING 
	(ser_count = 1)
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
	
$t = new ntsTime();
$today = $t->formatDate_Db();
$todayTimestamp = $t->timestampFromDbDate( $today );

/* reject appointments */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	service_id = $serviceId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );

		$params = array(
			'reason' => 'Service no longer offered',
			);
	/* silent if app is earlier than today */
		if( $subObject->getProp('starts_at') < $todayTimestamp ){
			$params['_silent'] = true;
			}
		$this->runCommand( $subObject, 'reject', $params );
		}
	}

/* delete in packs as well */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}packs
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$packId = $e['id'];
		$pack = new ntsObject( 'pack' );
		$pack->setId( $packId );
		$servicesString = $pack->getProp( 'services' );
		$packs = ntsLib::splitPackServicesString( $servicesString );

		reset( $packs );
		$finalPacks = array();
		foreach( $packs as $pa ){
			$newPa = array_diff( $pa, array($serviceId) );
			$finalPacks[] = $newPa;
			}

		$sessionsString = ntsLib::makePackSessionsString( $finalPacks );
		$pack->setProp( 'services', $sessionsString );

		$this->runCommand( $pack, 'update' );
		}
	}
?>