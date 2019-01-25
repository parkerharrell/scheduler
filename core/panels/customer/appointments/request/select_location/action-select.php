<?php
require_once( dirname(__FILE__) . '/_common.php' );

$locId = $req->getParam( 'id' );
if( $locId == 'auto' ){
	$locId = ntsLib::pickRandom( $allLocationIds );
	}

$location = new ntsObject( 'location' );
$location->setId( $locId );
$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['location'] = $location;

if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
	$ghostApp = $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'];
	$ghostApp->setProp( 'location_id', $locId );

	$cm->runCommand( $ghostApp, 'update' );
	$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] = $ghostApp;
	}
else {
	$saveId = array();
	reset( $NTS_CURRENT_REQUEST );
	foreach( $NTS_CURRENT_REQUEST as $cr ){
		if( $cr['location'] )
			$saveId[] = $cr['location']->getId();
		else
			$saveId[] = 0;
		}
	/* set selected location to session */
	ntsView::setPersistentParams( array('location' => join( '-', $saveId) ), $req, 'customer/appointments/request' );
	}

/* forward to dispatcher to see what's next? */
$noForward = true;
require( dirname(__FILE__) . '/../common/dispatcher.php' );
return;
?>