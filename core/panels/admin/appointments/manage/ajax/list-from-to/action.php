<?php
$NTS_VIEW['skipMenu'] = true;
$from = $req->getParam( 'from' );
$to = $req->getParam( 'to' );
$resourceId = $req->getParam( 'resource' );
$locationId = $req->getParam( 'location' );

// get this admin resources
$myResourcesIds = array();
reset( $NTS_VIEW['allResources'] );
foreach( $NTS_VIEW['allResources'] as $res ){
	$myResourcesIds[] = $res->getId();
	}

$tm = new haTimeManager();

if( $resourceId ){
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $resourceId );
	$tm->setResource( $resource );
	}
if( $locationId ){
	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $locationId );
	$tm->setLocation( $location );
	}

$apps = $tm->getAppointments( $from, $to );

$NTS_VIEW['entries'] = array();	
reset( $apps );
foreach( $apps as $i ){
	$appointment = ntsObjectFactory::get( 'appointment' );
	$appointment->setId( $i['id'] );
	
// check if accessible by this admin
	$thisResId = $appointment->getProp( 'resource_id' );
	if( ! in_array($thisResId, $myResourcesIds) ){
		continue;
		}
	$NTS_VIEW['entries'][] = $appointment;
	}
?>