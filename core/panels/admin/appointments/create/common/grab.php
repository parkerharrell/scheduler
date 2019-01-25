<?php
global $PANEL_PREFIX;

if( isset($PANEL_PREFIX) && $PANEL_PREFIX ){
	if( substr($PANEL_PREFIX, - strlen('/create')) != '/create' ){
		$PANEL_PREFIX = $PANEL_PREFIX . '/create';
		}
	}
else {
	$PANEL_PREFIX = 'admin/appointments/create';
	}

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$ntsdb =& dbWrapper::getInstance();

/* this part will set $NTS_VIEW['CURRENT_REQUEST'] being used by other parts */
$NTS_VIEW['CURRENT_REQUEST'] = array();
$NTS_VIEW['CURRENT_REQUEST']['service'] = null;
$NTS_VIEW['CURRENT_REQUEST']['resource'] = array();
$NTS_VIEW['CURRENT_REQUEST']['location'] = array();
$NTS_VIEW['CURRENT_REQUEST']['customer'] = array();
$NTS_VIEW['CURRENT_REQUEST']['time'] = array();
$NTS_VIEW['CURRENT_REQUEST']['seats'] = array();

/* CHECK IF RESCHEDULE */
$NTS_VIEW['RESCHEDULE'] = array();
$reschId = $req->getParam( 'reschedule' );
if( $reschId ){
	global $NTS_SKIP_APPOINTMENTS;
	$NTS_SKIP_APPOINTMENTS = array($reschId);

	$reschedule = ntsObjectFactory::get( 'appointment' );
	$reschedule->setId( $reschId );
	$NTS_VIEW['RESCHEDULE']['obj'] = $reschedule;

	$rservice = ntsObjectFactory::get( 'service' );
	$rservice->setId( $reschedule->getProp('service_id') );
	$NTS_VIEW['RESCHEDULE']['service'] = $rservice;

	$rcustomer = new ntsUser();
	$rcustomer->setId( $reschedule->getProp( 'customer_id' ) );
	$NTS_VIEW['RESCHEDULE']['customer'] = $rcustomer;

	$rresource = ntsObjectFactory::get( 'resource' );
	$rresource->setId( $reschedule->getProp( 'resource_id' ) );
	$NTS_VIEW['RESCHEDULE']['resource'] = $rresource;

	$rlocation = new ntsObject('location');
	$rlocation->setId( $reschedule->getProp( 'location_id' ) );
	$NTS_VIEW['RESCHEDULE']['location'] = $rlocation;

	$rtime = $reschedule->getProp( 'starts_at' );
	$NTS_VIEW['RESCHEDULE']['time'] = $rtime;

	$NTS_VIEW['RESCHEDULE']['seats'] = $reschedule->getProp( 'seats' );
	$NTS_VIEW['RESCHEDULE']['duration'] = $reschedule->getProp( 'duration' );;

// set for current request as well
	$NTS_VIEW['CURRENT_REQUEST']['seats'] = array( $reschedule->getProp('seats') );
	$NTS_VIEW['CURRENT_REQUEST']['customer'] = array( $rcustomer );
	}

/* NOW BUILD CURRENT REQUEST */
if( ! $NTS_VIEW['CURRENT_REQUEST']['service'] ){
	$serviceId = $req->getParam( 'service' );
	if( $serviceId )
		$serviceId = explode( '-', $serviceId );

	if( $serviceId ){
		$serviceId = $serviceId[ 0 ];
		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $serviceId );
		$NTS_VIEW['CURRENT_REQUEST']['service'] = $service;
		}
	}

if( ! $NTS_VIEW['CURRENT_REQUEST']['seats'] ){
	$seats = $req->getParam( 'seats' );
	if( $seats ){
		$seats = explode( '-', $seats );
		$NTS_VIEW['CURRENT_REQUEST']['seats'] = $seats;
		}
	}

if( ! $NTS_VIEW['CURRENT_REQUEST']['customer'] ){
	$customerId = $req->getParam( 'customer' );
	if( $customerId ){
		$customer = new ntsUser();
		$customer->setId( $customerId );
		$NTS_VIEW['CURRENT_REQUEST']['customer'] = array( $customer );
		}
	}

/* GET LIST OF AVAILABLE resources AND LOCATIONS */
$NTS_VIEW['resources'] = array();
$NTS_VIEW['locations'] = array();

$tm = new haTimeManager();
$NTS_VIEW['tm'] = $tm;

$NTS_VIEW['locations'] = ntsObjectFactory::getAll( 'location' ); 

if( ! $NTS_VIEW['CURRENT_REQUEST']['resource'] ){
	$resource = array();
	$resourceId = $req->getParam( 'resource' );
	if( $resourceId ){
		$resourceId = explode( '-', $resourceId );
		reset( $resourceId );
		foreach( $resourceId as $rid ){
			$thisRes = ntsObjectFactory::get( 'resource' );
			$thisRes->setId( $rid );
			$resource[] = $thisRes;
			}
		}
	elseif( count($NTS_VIEW['managedResources']) == 1 ) {
		$resource = array( $NTS_VIEW['managedResources'][0] );
		}
	$NTS_VIEW['CURRENT_REQUEST']['resource'] = $resource;
	}

if( ! $NTS_VIEW['CURRENT_REQUEST']['location'] ){
	$location = array();
	$locationId = $req->getParam( 'location' );
	if( $locationId ){
		$locationId = explode( '-', $locationId );
		reset( $locationId );
		foreach( $locationId as $lid ){
			$thisLoc = new ntsObject( 'location' );
			$thisLoc->setId( $lid );
			$location[] = $thisLoc;
			}
		}
	else {
		if( count($NTS_VIEW['locations']) == 1 ){
			$location = array( $NTS_VIEW['locations'][0] );
			}
		}
	$NTS_VIEW['CURRENT_REQUEST']['location'] = $location;
	}

if( ! $NTS_VIEW['CURRENT_REQUEST']['customer'] ){
	$customer = null;
	}

$t = new ntsTime();
$NTS_VIEW['t'] = $t;

/* SAVE PERSISTENT */
$cal = $req->getParam( 'cal' );

$saveOn = array();

$time = $req->getParam( 'time' );
$saveOn['time'] = $time;

$time = ( $time ) ? explode( '-', $time ) : array();
$NTS_VIEW['time'] = $time;
$NTS_VIEW['CURRENT_REQUEST']['time'] = $time;

$saveOn['cal'] = $cal;
$saveOn['reschedule'] = $reschId;

if( $NTS_VIEW['CURRENT_REQUEST']['service'] )
	$saveOn['service'] = $NTS_VIEW['CURRENT_REQUEST']['service']->getId();

$customerId = array();
if( $NTS_VIEW['CURRENT_REQUEST']['customer'] ){
	reset( $NTS_VIEW['CURRENT_REQUEST']['customer'] );
	foreach( $NTS_VIEW['CURRENT_REQUEST']['customer'] as $cus )
		$customerId[] = $cus->getId();
	$saveOn['customer'] = join( '-', $customerId );
	}

if( $NTS_VIEW['CURRENT_REQUEST']['seats'] ){
	$saveOn['seats'] = join( '-', $NTS_VIEW['CURRENT_REQUEST']['seats'] );
	}

$resourceId = array();
if( $NTS_VIEW['CURRENT_REQUEST']['resource'] ){
	reset( $NTS_VIEW['CURRENT_REQUEST']['resource'] );
	foreach( $NTS_VIEW['CURRENT_REQUEST']['resource'] as $pro )
		$resourceId[] = $pro->getId();
	$saveOn['resource'] = join( '-', $resourceId );
	}

$locationId = array();
if( $NTS_VIEW['CURRENT_REQUEST']['location'] ){
	reset( $NTS_VIEW['CURRENT_REQUEST']['location'] );
	foreach( $NTS_VIEW['CURRENT_REQUEST']['location'] as $loc ){
		$locationId[] = $loc->getId();
		}
	$saveOn['location'] = join( '-', $locationId );
	}

$customerId = array();
if( $NTS_VIEW['CURRENT_REQUEST']['customer'] ){
	reset( $NTS_VIEW['CURRENT_REQUEST']['customer'] );
	foreach( $NTS_VIEW['CURRENT_REQUEST']['customer'] as $cus )
		$customerId[] = $cus->getId();
	$saveOn['customer'] = join( '-', $customerId );
	}

ntsView::setPersistentParams( $saveOn, $req, $PANEL_PREFIX );

$serviceId = $NTS_VIEW['CURRENT_REQUEST']['service'] ? $NTS_VIEW['CURRENT_REQUEST']['service']->getId() : 0;
?>