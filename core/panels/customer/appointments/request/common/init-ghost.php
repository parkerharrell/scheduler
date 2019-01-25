<?php
$cm =& ntsCommandManager::getInstance();

for( $i = 0; $i < $total; $i++ ){
	/* SAVE CHOST APP */
	if( $NTS_CURRENT_REQUEST[$i]['ghost'] ){
		$ghostApp = $NTS_CURRENT_REQUEST[$i]['ghost'];
		}
	else {
		$ghostApp = ntsObjectFactory::get( 'appointment' );
		}

	if( isset($NTS_CURRENT_REQUEST[$i]['service']) && $NTS_CURRENT_REQUEST[$i]['service'] ){
		$ghostApp->setProp( 'service_id',	$NTS_CURRENT_REQUEST[$i]['service']->getId() );
		$ghostApp->setProp( 'duration',		$NTS_CURRENT_REQUEST[$i]['service']->getProp('duration') );
		$ghostApp->setProp( 'lead_in',		$NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_in') );
		$ghostApp->setProp( 'lead_out',		$NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_out') );
		$ghostApp->setProp( 'seats',		$NTS_CURRENT_REQUEST[$i]['seats'] );
		if( $NTS_VIEW['PACK'] ){
			}
		else {
			$thisPrice = ntsLib::getServicePrice( $NTS_CURRENT_REQUEST[$i]['service'], $NTS_CURRENT_REQUEST[$i]['seats'] );
			$ghostApp->setProp( 'price', $thisPrice );
			}
		}
	else {
		$ghostApp->setProp( 'session_id',	0 );
		$ghostApp->setProp( 'duration',		0 );
		$ghostApp->setProp( 'lead_in',		0 );
		$ghostApp->setProp( 'lead_out',		0 );
		$ghostApp->setProp( 'seats',		0 );
		$ghostApp->setProp( 'price',		0 );
		}

	if( isset($NTS_CURRENT_REQUEST[$i]['time']) && $NTS_CURRENT_REQUEST[$i]['time'] ){
		$ghostApp->setProp( 'starts_at', $NTS_CURRENT_REQUEST[$i]['time'] );
		}
	else {
		$ghostApp->setProp( 'starts_at', 0 );
		}

	if( $NTS_CURRENT_REQUEST[$i]['resource'] )
		$ghostApp->setProp( 'resource_id',	$NTS_CURRENT_REQUEST[$i]['resource']->getId() );
	else
		$ghostApp->setProp( 'resource_id',	0 );

	if( $NTS_CURRENT_REQUEST[$i]['location'] )
		$ghostApp->setProp( 'location_id',	$NTS_CURRENT_REQUEST[$i]['location']->getId() );
	else
		$ghostApp->setProp( 'location_id',	0 );

	if( NTS_CURRENT_USERID ){
		$ghostApp->setProp( 'customer_id', NTS_CURRENT_USERID );
		}
	elseif( isset($_SESSION['temp_customer_id']) ){
		$ghostApp->setProp( 'customer_id', $_SESSION['temp_customer_id'] );
		}
	else {
		$ghostApp->setProp( 'customer_id', 0 );
		}

	if( $NTS_CURRENT_REQUEST[$i]['ghost'] ){
		$cm->runCommand( $ghostApp, 'update' );
		}
	else {
		$cm->runCommand( $ghostApp, 'init' );
		if( ! $cm->isOk() ){
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		}
	$NTS_CURRENT_REQUEST[$i]['ghost'] = $ghostApp;
	}

$saveIds = array();
foreach( $NTS_CURRENT_REQUEST as $cr ){
	$saveIds[] = $cr['ghost'] ? $cr['ghost']->getId() : 0;
	}
$saveOn = array(
	'ghost'	=> join( '-', $saveIds ),
	'service'	=> null,
	'location'	=> null,
	'resource'	=> null,
	);

//ntsView::resetPersistentParams( 'customer/appointments/request' );
ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );
?>