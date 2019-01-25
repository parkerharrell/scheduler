<?php
$ntsdb =& dbWrapper::getInstance();

/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}resources
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}

if( ! $totalCount ){
/* continue create resource */
	$forwardTo = ntsLink::makeLink( '-current-/../create' );
	ntsView::redirect( $forwardTo );
	exit;
	}

/* entries */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}resources
ORDER BY
	show_order ASC, title ASC
EOT;

$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result ){
	while( $i = $result->fetch() ){
		$e = ntsObjectFactory::get( 'resource' );
		$e->setId( $i['id'] );
		$NTS_VIEW['entries'][] = $e;
		}
	}
?>