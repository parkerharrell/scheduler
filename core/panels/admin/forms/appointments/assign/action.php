<?php
$ntsdb =& dbWrapper::getInstance();

/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}forms WHERE class="appointment"
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}

if( ! $totalCount ){
/* continue create form */
	$forwardTo = ntsLink::makeLink( '-current-/../create' );
	ntsView::redirect( $forwardTo );
	exit;
	}
?>