<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$serviceId = $req->getParam( 'id' );

/* service info */
$service = ntsObjectFactory::get( 'service' );
$service->setId( $serviceId );
if( $service->notFound() ){
	$forwardTo = ntsLink::makeLink( '-current-' );
	ntsView::redirect( $forwardTo );
	exit;
	}
$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['service'] = $service;

if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
	$ghostApp = $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'];
	$ghostApp->setProp( 'service_id',	$serviceId );
	$ghostApp->setProp( 'duration',		$service->getProp('duration') );
	$ghostApp->setProp( 'lead_in',		$service->getProp('lead_in') );
	$ghostApp->setProp( 'lead_out',		$service->getProp('lead_out') );

	$cm->runCommand( $ghostApp, 'update' );
	$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] = $ghostApp;
	}
else {
	$saveId = array();
	reset( $NTS_CURRENT_REQUEST );
	foreach( $NTS_CURRENT_REQUEST as $cr ){
		if( $cr['service'] )
			$saveId[] = $cr['service']->getId();
		else
			$saveId[] = 0;
		}

	/* set selected service to session */
	ntsView::setPersistentParams( array('service' => join( '-', $saveId)), $req, 'customer/appointments/request' );

	$app = ntsObjectFactory::get( 'appointment' );
	$app->setProp( 'service_id', $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['service']->getId() );
	$stored = serialize( $app );
	}

/* forward to dispatcher to see what's next? */
$noForward = true;
require( dirname(__FILE__) . '/../common/dispatcher.php' );
return;
?>