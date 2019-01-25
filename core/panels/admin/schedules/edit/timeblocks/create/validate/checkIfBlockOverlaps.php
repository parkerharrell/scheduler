<?php
$scheduleId = $validationParams['schedule_id'];
$appliedOn = $validationParams['applied_on'];

$start = $formValues['starts_at'];
$end = $formValues['ends_at'];

$ntsdb =& dbWrapper::getInstance();

$sql =<<<EOT
SELECT
	COUNT(*) AS count 
FROM 
	{PRFX}timeblocks
WHERE
	schedule_id = $scheduleId AND 
	applied_on = $appliedOn AND 
	( ends_at > $start AND starts_at < $end )
EOT;

$result = $ntsdb->runQuery( $sql );
if( $i = $result->fetch() ){
	if( $i['count'] )
		$validationFailed = 1;
	}
?>