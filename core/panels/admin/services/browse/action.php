<?php
$ntsdb =& dbWrapper::getInstance();

/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}services
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}

if( ! $totalCount ){
/* continue create service */
	$forwardTo = ntsLink::makeLink( 'admin/services/create' );
	ntsView::redirect( $forwardTo );
	exit;
	}

/* entries */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}services
ORDER BY
	show_order ASC
EOT;

$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result ){
	while( $e = $result->fetch() ){
		$o = ntsObjectFactory::get( 'service' );
		$o->setId( $e['id'] );
		$NTS_VIEW['entries'][] = $o;
		}
	}
?>