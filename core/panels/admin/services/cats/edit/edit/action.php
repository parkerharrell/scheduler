<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

/* service info */
$sql =<<<EOT
SELECT
	*
FROM 
	{PRFX}service_cats
WHERE
	{PRFX}service_cats.id = $id
EOT;
$result = $ntsdb->runQuery( $sql );
$catInfo = $result->fetch();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $catInfo );
?>