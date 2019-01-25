<?php
$ntsdb =& dbWrapper::getInstance();
$userId = $object->getId();

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
	customer_id = $userId
EOT;
$result = $ntsdb->runQuery( $sql );

if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );
		
		$params = array(
			'reason' => 'Customer account deleted',
			);

	/* silent if app is earlier than today */
		if( $subObject->getProp('starts_at') < $todayTimestamp ){
			$params['_silent'] = true;
			}

		$this->runCommand( $subObject, 'reject', $params );
		}
	}
?>