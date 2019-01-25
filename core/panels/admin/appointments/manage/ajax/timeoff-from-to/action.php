<?php
$NTS_VIEW['skipMenu'] = true;
$from = $req->getParam( 'from' );
$to = $req->getParam( 'to' );
$resourceId = $req->getParam( 'resource' );
$resWhere = $resourceId ? "resource_id = $resourceId" : '1';

$NTS_VIEW['entries'] = array();	

$sql =<<<EOT
SELECT
	*
FROM
	{PRFX}timeoffs
WHERE
$resWhere AND ends_at > $from AND starts_at < $to
ORDER BY
	starts_at ASC, resource_id ASC
EOT;

$result = $ntsdb->runQuery( $sql );
while( $v = $result->fetch() )
	$NTS_VIEW['entries'][] = $v;	
?>