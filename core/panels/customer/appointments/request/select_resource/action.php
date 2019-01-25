<?php
ntsView::setTitle( M('Bookable Resources') );
require_once( dirname(__FILE__) . '/_common.php' );

/* NO resources - REDIRECT BACK TO SERVICE SELECTION */
if( ! count($allResourceIds) ){
	ntsView::setAnnounce( ntsView::objectTitle($NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']) . ': ' . M('Not Available Now'), 'error' );
	$forwardTo = ntsLink::makeLink( '-current-/../select_service' );
	ntsView::redirect( $forwardTo );
	exit;
	}

$NTS_VIEW['selectionMode'] = 'manual';
$confFlow = $conf->get('appointmentFlow');
reset( $confFlow );
foreach( $confFlow as $f ){
	if( $f[0] == 'resource' ){
		$NTS_VIEW['selectionMode'] = $f[1];
		break;
		}
	}

/* ONLY ONE resource - REDIRECT */
if( (count($allResourceIds) == 1) || ($NTS_VIEW['selectionMode'] == 'auto') ){
	$resId = ntsLib::pickRandom( $allResourceIds );
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

	$noForward = true;
	/* forward to dispatcher to see what's next? */
	require( dirname(__FILE__) . '/../common/dispatcher.php' );
	return;
	}

/* OR CHOOSE resource */
reset( $allResourceIds );
$entries = array();
foreach( $allResourceIds as $rid ){
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $rid );
	$entries[] = $resource;
	}
$NTS_VIEW['entries'] = $entries;
?>