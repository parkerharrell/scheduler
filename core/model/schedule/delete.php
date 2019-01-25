<?php
$ntsdb =& dbWrapper::getInstance();

$scheduleId = $object->getId();
/* delete timeblocks */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}timeblocks
WHERE
	schedule_id = $scheduleId
EOT;

$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = new ntsObject( 'timeblock' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}
?>