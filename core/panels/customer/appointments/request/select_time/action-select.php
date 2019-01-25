<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$timeSelected = $req->getParam( 'id' );
$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['time'] = $timeSelected;

if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
	$ghostApp = $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'];
	$ghostApp->setProp( 'starts_at', $timeSelected );

	$cm->runCommand( $ghostApp, 'update' );
	$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] = $ghostApp;
	}
else {
	$saveId = array();
	reset( $NTS_CURRENT_REQUEST );
	foreach( $NTS_CURRENT_REQUEST as $cr ){
		if( $cr['time'] )
			$saveId[] = $cr['time'];
		else
			$saveId[] = 0;
		}

	/* set selected location to session */
	ntsView::setPersistentParams( array('time' => join( '-', $saveId) ), $req, 'customer/appointments/request' );
	}

/* forward to dispatcher to see what's next? */
$noForward = true;
require( dirname(__FILE__) . '/../common/dispatcher.php' );
return;
?>