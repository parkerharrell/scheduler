<?php
$resourceId = $validationParams['resource_id'];
if( $resourceId == 'all' )
	return;

$startsAtDate = $formValues['starts_at_date'];
$endsAtDate = $formValues['ends_at_date'];
$startsAtTime = $formValues['starts_at_time'];
$endsAtTime = $formValues['ends_at_time'];

$t = new ntsTime();
$startsAt = $t->timestampFromDbDate( $startsAtDate ) + $startsAtTime;
$endsAt = $t->timestampFromDbDate( $endsAtDate ) + $endsAtTime;

$ntsdb =& dbWrapper::getInstance();

$sql =<<<EOT
SELECT
	COUNT(*) AS count 
FROM 
	{PRFX}timeoffs
WHERE
	resource_id = $resourceId AND
	( ends_at > $startsAt AND starts_at < $endsAt )
EOT;

$result = $ntsdb->runQuery( $sql );
if( $i = $result->fetch() ){
	if( $i['count'] )
		$validationFailed = 1;
	}
?>