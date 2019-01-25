<?php
ntsView::setTitle( M('Locations') );
require_once( dirname(__FILE__) . '/_common.php' );

/* NO LOCATIONS - REDIRECT BACK TO SERVICE SELECTION */
if( ! count($allLocationIds) ){
	if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] ){
		ntsView::setAnnounce( ntsView::objectTitle($NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']) . ': ' . M('Not Available Now'), 'error' );
		}
	$forwardTo = ntsLink::makeLink( '-current-/../select_service' );
	ntsView::redirect( $forwardTo );
	exit;
	}

$NTS_VIEW['selectionMode'] = 'manual';
$confFlow = $conf->get('appointmentFlow');
reset( $confFlow );
foreach( $confFlow as $f ){
	if( $f[0] == 'location' ){
		$NTS_VIEW['selectionMode'] = $f[1];
		break;
		}
	}

/* ONLY ONE LOCATION - REDIRECT */
if( (count($allLocationIds) == 1) || ($NTS_VIEW['selectionMode'] == 'auto') ){
	$locId = ntsLib::pickRandom( $allLocationIds );
	$location = ntsObjectFactory::get( 'location' );
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
	}

/* OR CHOOSE LOCATION */
reset( $allLocationIds );
$entries = array();
foreach( $allLocationIds as $lid ){
	$location = new ntsObject( 'location' );
	$location->setId( $lid );
	$entries[] = $location;
	}

/* sort by show order */
usort( $entries, create_function('$a, $b', 'return ntsLib::numberCompare($a->getProp("show_order"), $b->getProp("show_order"));' ) );

$NTS_VIEW['entries'] = $entries;
?>