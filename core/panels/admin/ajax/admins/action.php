<?php
$NTS_VIEW['skipMenu'] = true;
$ntsdb =& dbWrapper::getInstance();

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$whereString = '';
$current = $req->getParam( 'current' );
if( $current ){
	$current = explode( '||', $current );
	$currentIdsString = join( ', ', $current );
	$admins = $integrator->getUsers( array('_role' => '="admin"', 'id' => " NOT IN ($currentIdsString)") );
	}
else {
	$admins = $integrator->getUsers( array('_role' => '="admin"') );
	}

$NTS_VIEW['entries'] = array();	
$result = $ntsdb->runQuery( $sql );
foreach( $admins as $i ){
	$object = new ntsUser;
	$object->setId( $i['id'] );
	$NTS_VIEW['entries'][] = $object;
	}
?>