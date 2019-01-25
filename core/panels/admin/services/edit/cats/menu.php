<?php
$ntsdb =& dbWrapper::getInstance();

$totalCount = 0;
/* super count */
$sql =<<<EOT
SELECT COUNT(*) AS count FROM {PRFX}service_cats
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	$e = $result->fetch();
	$totalCount = $e['count'];
	}
if( $totalCount ){
	$title = M('Categories');
	$sequence = 3;
	}
?>