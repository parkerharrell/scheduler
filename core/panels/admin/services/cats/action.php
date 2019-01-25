<?php
$ntsdb =& dbWrapper::getInstance();

/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}service_cats
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}

if( ! $totalCount ){
/* continue create service */
	$forwardTo = ntsLink::makeLink( '-current-/create' );
	ntsView::redirect( $forwardTo );
	exit;
	}

/* entries */
$sql =<<<EOT
SELECT
	*,
	( 
	SELECT
		COUNT(*) AS subcount
	FROM 
		{PRFX}objectmeta
	WHERE
		{PRFX}objectmeta.obj_class = 'service' AND
		{PRFX}objectmeta.meta_name = '_service_cat' AND
		{PRFX}objectmeta.meta_value = {PRFX}service_cats.id
	)
	AS count_services
FROM
	{PRFX}service_cats
ORDER BY
	show_order ASC
EOT;

$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result ){
	while( $e = $result->fetch() ){
		$NTS_VIEW['entries'][] = $e;
		}
	}
?>