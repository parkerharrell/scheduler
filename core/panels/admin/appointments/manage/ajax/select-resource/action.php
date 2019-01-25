<?php
$NTS_VIEW['skipMenu'] = true;
$locationId = $req->getParam( 'location' );
$customerId = $req->getParam( 'customer' );
$ts = $req->getParam( 'time' );

$t = new ntsTime;
$t->setTimestamp( $ts );
$NTS_VIEW['t'] = $t;
$NTS_VIEW['ts'] = $ts;

$tm = new haTimeManager();

if( $locationId ){
	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $locationId );
	$tm->setLocation( $location );
	$NTS_VIEW['location'] = $location;
	}

$NTS_VIEW['customer'] = null;
if( $customerId ){
	$customer = new ntsUser;
	$customer->setId( $customerId );
	$NTS_VIEW['customer'] = $customer;
	}

$NTS_VIEW['RESCHEDULE'] = array();
$reschId = $req->getParam( 'reschedule' );
if( $reschId ){
	$saveOn[ 'reschedule' ] = $reschId;
	global $NTS_SKIP_APPOINTMENTS;
	$NTS_SKIP_APPOINTMENTS = array( $reschId );

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
	}

$selectableTimes = $tm->getSelectableTimes_Internal( $ts, $ts + 24 * 60 * 60 );
$selectableResources = array();
if( isset($selectableTimes[$ts]) ){
	reset( $selectableTimes[$ts] );
	foreach( $selectableTimes[$ts] as $tts => $tArray ){
		$selectableResources[] = $tArray[ $tm->SLT_INDX['resource_id'] ];
		}
	}
$NTS_VIEW['SELECTABLE_RESOURCES'] = $selectableResources;
//_print_r( $selectableTimes );
?>