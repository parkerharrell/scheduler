<?php
$NTS_VIEW['skipMenu'] = true;
$resourceId = $req->getParam( 'resource' );
$locationId = $req->getParam( 'location' );
$customerId = $req->getParam( 'customer' );
$ts = $req->getParam( 'time' );

$t = new ntsTime;
$t->setTimestamp( $ts );
$NTS_VIEW['t'] = $t;
$NTS_VIEW['ts'] = $ts;

$tm = new haTimeManager();

if( $resourceId ){
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $resourceId );
	$tm->setResource( $resource );
	$NTS_VIEW['resource'] = $resource;
	}

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

/* all services */
if( ! $NTS_VIEW['RESCHEDULE'] ){
	$allServices = ntsObjectFactory::getAll( 'service' );
	}
else {
	$allServices = array( $NTS_VIEW['RESCHEDULE']['service'] );
	}
$NTS_VIEW['allServices'] = $allServices;

$selectableTimes = $tm->getSelectableTimes_Internal( $ts, $ts + 24 * 60 * 60 );
$selectableServices = array();
if( isset($selectableTimes[$ts]) ){
	reset( $selectableTimes[$ts] );
	foreach( $selectableTimes[$ts] as $tts => $tArray ){
		$selectableServices[] = $tArray[ $tm->SLT_INDX['service_id'] ];
		}
	}
$NTS_VIEW['SELECTABLE_SERVICES'] = $selectableServices;
//_print_r( $selectableTimes );

/* check categories */
$cat2service = array();
$allCats = array();
reset( $allServices );
foreach( $allServices as $service ){
	$thisCats = $service->getProp( '_service_cat' );
	if( ! $thisCats )
		$thisCats = array( 0 );

	reset( $thisCats );
	foreach( $thisCats as $catId ){
		if( ! isset($cat2service[$catId]) )
			$cat2service[$catId] = array();
		$cat2service[$catId][] = $service;
		}
	$allCats = array_merge( $allCats, $thisCats );
	}
$allCats = array_unique( $allCats );

if( count($allCats) < 2 ){
	$showCats = false;
	}
else {
	$showCats = true;
	}
	
if( $showCats ){
	$idsIn = join( ',', $allCats );
	$sql =<<<EOT
SELECT
	id, title, description
FROM
	{PRFX}service_cats
WHERE
	id IN ($idsIn)
ORDER BY
	show_order ASC
EOT;

	$showCats = array();
	$result = $ntsdb->runQuery( $sql );
	while( $c = $result->fetch() ){
		$showCats[] = array( $c['id'], $c['title'], $c['description'] );
		}
	if( in_array(0, $allCats) )
		$showCats[] = array( 0, M('Uncategorized'), '' );
	}
$NTS_VIEW['showCats'] = $showCats;
$NTS_VIEW['cat2service'] = $cat2service;
?>