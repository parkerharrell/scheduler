<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$packId = $req->getParam( 'id' );

$pack = new ntsObject( 'pack' );
$pack->setId( $packId );
$NTS_VIEW['PACK'] = $pack;

$sessionString = $pack->getProp( 'services' );
$thisPackSessions = ntsLib::splitPackServicesString( $sessionString );

$total = count( $thisPackSessions );

/* set current request */
for( $i = 0; $i < $total; $i++ ){
	$thisIndexSessions = $thisPackSessions[ $i ];
	if( ! isset($NTS_CURRENT_REQUEST[$i]) ){
		$NTS_CURRENT_REQUEST[ $i ] = array();
		$NTS_CURRENT_REQUEST[ $i ]['location'] = null;
		$NTS_CURRENT_REQUEST[ $i ]['resource'] = null;
		$NTS_CURRENT_REQUEST[ $i ]['service'] = null;
		$NTS_CURRENT_REQUEST[ $i ]['ghost'] = null;
		$NTS_CURRENT_REQUEST[ $i ]['time'] = 0;
		$NTS_CURRENT_REQUEST[ $i ]['recurring'] = 0;
		$NTS_CURRENT_REQUEST[ $i ]['cal'] = 0;
		$NTS_CURRENT_REQUEST[ $i ]['seats'] = 0;
		}

	if( count($thisIndexSessions) == 1 ){
		$serviceId = $thisIndexSessions[0];
		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $serviceId );
		$NTS_CURRENT_REQUEST[$i]['service'] = $service;
		}
	}

/* save ghost apps */
require( dirname(__FILE__) . '/../common/init-ghost.php' );

/* set selected location to session */
ntsView::setPersistentParams( 
	array(
		'total' => $total,
		'pack' 	=> $packId,
		),
	$req, 'customer/appointments/request'
	);

$noForward = true;
require( dirname(__FILE__) . '/../common/dispatcher.php' );
return;
?>