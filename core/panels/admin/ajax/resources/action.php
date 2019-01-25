<?php
$NTS_VIEW['skipMenu'] = true;
$ntsdb =& dbWrapper::getInstance();

$whereString = '';
$current = $req->getParam( 'current' );
if( $current ){
	$current = explode( '||', $current );
	$currentIdsString = join( ', ', $current );
	$whereString = "WHERE id NOT IN ($currentIdsString)";
	}

$sql =<<<EOT
SELECT
	id
FROM	
	{PRFX}resources
$whereString
ORDER BY 
	show_order ASC
EOT;

$NTS_VIEW['entries'] = array();	
$result = $ntsdb->runQuery( $sql );
while( $i = $result->fetch() ){
	$object = ntsObjectFactory::get( 'resource' );
	$object->setId( $i['id'] );
	$NTS_VIEW['entries'][] = $object;
	}
?>