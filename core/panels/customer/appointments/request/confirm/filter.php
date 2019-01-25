<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

/* TO-DO: CHECK ALL THE APPS IF THEY ARE AVAILABLE */
$tm = new haTimeManager();
$t = $NTS_VIEW['t'];

for( $i = 0; $i < count($NTS_CURRENT_REQUEST); $i++ ){
	if( $NTS_CURRENT_REQUEST[ $i ]['ghost'] ){
		$object = $NTS_CURRENT_REQUEST[ $i ]['ghost'];
		$ghostId = $object->getId();
		global $NTS_SKIP_APPOINTMENTS;
		if( ! is_array($NTS_SKIP_APPOINTMENTS) )
			$NTS_SKIP_APPOINTMENTS = array();
		if( ! in_array($ghostId, $NTS_SKIP_APPOINTMENTS) )
			$NTS_SKIP_APPOINTMENTS[] = $ghostId;
	
		$NTS_CURRENT_REQUEST[$i]['time'] = $object->getProp( 'starts_at' );

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $object->getProp( 'service_id' ) );
		$NTS_CURRENT_REQUEST[$i]['service'] = $service;

		$resource = ntsObjectFactory::get( 'resource' );
		$resource->setId( $object->getProp( 'resource_id' ) );
		$NTS_CURRENT_REQUEST[$i]['resource'] = $resource;

		$location = ntsObjectFactory::get( 'location' );
		$location->setId( $object->getProp( 'location_id' ) );
		$NTS_CURRENT_REQUEST[$i]['location'] = $location;
		}
	else {
		}
	$tm->setService( $NTS_CURRENT_REQUEST[$i]['service'] );
	$tm->setResource( $NTS_CURRENT_REQUEST[$i]['resource'] );

	$thisTs = $NTS_CURRENT_REQUEST[$i]['time'];
	$startCheck = $thisTs - $NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_in');
	$endCheck = $thisTs + $NTS_CURRENT_REQUEST[$i]['service']->getProp('duration') + $NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_out');
	$seats = $NTS_CURRENT_REQUEST[$i]['seats'] ? $NTS_CURRENT_REQUEST[$i]['seats'] : 1;

	$times = $tm->getSelectableTimes( 
		$startCheck,
		$endCheck,
		$seats
		);

	if( isset($times[$thisTs]) ){
		// OK
		}
	else {
		$t->setTimestamp( $thisTs );
		$error = M('Not Available Now') . ': ' . $t->formatFull();
		ntsView::setAnnounce( $error, 'error' );

		$NTS_CURRENT_REQUEST[ $i ]['time'] = 0;
		if( $NTS_CURRENT_REQUEST[ $i ]['ghost'] ){
			$object->setProp( 'starts_at', 0 );
			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $object, 'update' );
			}
		else {
			$persistentTime = array();
			reset( $NTS_CURRENT_REQUEST );
			foreach( $NTS_CURRENT_REQUEST as $ri => $ra ){
				$persistentTime[] = $ra['time'];
				}
			$saveOn = array(
				'time'	=> join( '-', $persistentTime ),
				);
			ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );
			}

		$noForward = false;
		require( dirname(__FILE__) . '/../common/dispatcher.php' );
		exit;
		}
	}
?>