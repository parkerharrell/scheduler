<?php
require_once( dirname(__FILE__) . '/_common.php' );

$resId = $req->getParam( 'id' );
if( $resId == 'auto' ){
	$resId = ntsLib::pickRandom( $allResourceIds );
	}

$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resId );
$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['resource'] = $resource;

if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
	$ghostApp = $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'];
	$ghostApp->setProp( 'resource_id', $resId );

	$cm->runCommand( $ghostApp, 'update' );
	$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] = $ghostApp;
	}
else {
	$saveId = array();
	reset( $NTS_CURRENT_REQUEST );
	foreach( $NTS_CURRENT_REQUEST as $cr ){
		if( $cr['resource'] )
			$saveId[] = $cr['resource']->getId();
		else
			$saveId[] = 0;
		}
	/* set selected resource to session */
	ntsView::setPersistentParams( array('resource' => join( '-', $saveId) ), $req, 'customer/appointments/request' );
	}

/* forward to dispatcher to see what's next? */
require( dirname(__FILE__) . '/../common/dispatcher.php' );
?>