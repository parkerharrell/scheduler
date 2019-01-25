<?php
global $NTS_CURRENT_REQUEST, $NTS_CURRENT_REQUEST_INDEX, $NTS_CURRENT_REQUEST_WHAT;

$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();

/* check if we have any problems with resources - no schedules or no administrative users */
global $NTS_SKIP_RESOURCES;
$NTS_SKIP_RESOURCES = array();
$allResources = ntsObjectFactory::getAll( 'resource' );
reset( $allResources );
foreach( $allResources as $res ){
	$restrictions = $res->getProp('_restriction');
	if( in_array('suspended', $restrictions) ){
		$NTS_SKIP_RESOURCES[] = $res->getId();
		continue;
		}

	$resId = $res->getId();
	// schedules
	$sql =<<<EOT
SELECT 
	COUNT(id) AS count 
FROM 
	{PRFX}schedules
WHERE
	resource_id = $resId
EOT;
	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$i = $result->fetch();
		$schedulesCount = $i['count'];
		}
	if( $schedulesCount <= 0 ){
		$NTS_SKIP_RESOURCES[] = $resId;
		continue;
		}
	// admins	
	list( $appsAdmins, $scheduleAdmins ) = $res->getAdmins( true );
	if( ! ( count($appsAdmins) && count($scheduleAdmins) ) ){
		$NTS_SKIP_RESOURCES[] = $resId;
		continue;
		}
	}

$NTS_CURRENT_REQUEST = array();
$NTS_CURRENT_REQUEST_WHAT = '';
$NTS_VIEW['RESCHEDULE'] = null;
$NTS_VIEW['PACK'] = null;

$t = new ntsTime();
$t->setTimezone( $NTS_CURRENT_USER->getTimezone() );
$NTS_VIEW['t'] = $t;

$req = new ntsRequest();

$total = $req->getParam( 'total' );
if( ! $total )
	$total = 1;

$NTS_CURRENT_REQUEST_INDEX = $req->getParam( 'cri' );
if( ! $NTS_CURRENT_REQUEST_INDEX )
	$NTS_CURRENT_REQUEST_INDEX = 0;

/* initiate request array */
for( $i = 0; $i < $total; $i++ ){
	$NTS_CURRENT_REQUEST[ $i ] = array();
	$NTS_CURRENT_REQUEST[ $i ]['location'] = null;
	$NTS_CURRENT_REQUEST[ $i ]['resource'] = null;
	$NTS_CURRENT_REQUEST[ $i ]['service'] = null;
	$NTS_CURRENT_REQUEST[ $i ]['ghost'] = null;
	$NTS_CURRENT_REQUEST[ $i ]['time'] = 0;
	$NTS_CURRENT_REQUEST[ $i ]['recurring'] = 0;
	$NTS_CURRENT_REQUEST[ $i ]['cal'] = 0;
	$NTS_CURRENT_REQUEST[ $i ]['seats'] = 0;
	}

/* CHECK IF PACK */
$packId = $req->getParam( 'pack' );
if( $packId ){
	$pack = new ntsObject( 'pack' );
	$pack->setId( $packId );

	$NTS_VIEW['PACK'] = $pack;
	}

/* CHECK IF RESCHEDULE */
$reschId = $req->getParam( 'reschedule' );
if( $reschId ){
	$reschedule = ntsObjectFactory::get( 'appointment' );
	$reschedule->setId( $reschId );
	$rcustomerId = $reschedule->getProp('customer_id');

	if( $NTS_CURRENT_USER->getId() > 0 )
		$currentCustomerId = $NTS_CURRENT_USER->getId();
	elseif( isset($_SESSION['temp_customer_id']) )
		$currentCustomerId = $_SESSION['temp_customer_id'];
	else
		$currentCustomerId = 0;

	if( $rcustomerId != $currentCustomerId ){
		ntsView::setAnnounce( M('Access Denied'), 'error' );
		$forwardTo = ntsLink::makeLink();
		ntsView::redirect( $forwardTo );
		exit;
		}

	global $NTS_SKIP_APPOINTMENTS;
	$NTS_SKIP_APPOINTMENTS = array( $reschId );
	$total = 1;

	$NTS_VIEW['RESCHEDULE'] = $reschedule;
	}

/* ghost apps */
$ghostId = $req->getParam( 'ghost' );
if( $ghostId ){
	$ghostId = explode( '-', $ghostId );

	for( $i = 0; $i < $total; $i++ ){
		if( isset($ghostId[$i]) && $ghostId[$i] ){
			$ghostApp = ntsObjectFactory::get( 'appointment' );
			$ghostApp->setId( $ghostId[$i] );
			$ghostApp->setProp( 'ghost_last_access', time() );
			$cm->runCommand( $ghostApp, 'update' );
			$NTS_CURRENT_REQUEST[ $i ]['ghost'] = $ghostApp;
			}
		}
	}
else {
	$ghostId = array();
	}

for( $i = 0; $i < $total; $i++ ){
/* service */
	if( $NTS_VIEW['RESCHEDULE'] || isset($NTS_CURRENT_REQUEST[$i]['ghost']) ){
		if( $NTS_VIEW['RESCHEDULE'] ){
			$prefillApp = $NTS_VIEW['RESCHEDULE'];
			}
		elseif( isset($NTS_CURRENT_REQUEST[$i]['ghost']) ){
			$prefillApp = $NTS_CURRENT_REQUEST[$i]['ghost'];
			}

		$serviceId = $prefillApp->getProp('service_id');
		$seats = $prefillApp->getProp('seats');

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $serviceId );
		$serviceId = 0;

//		$setProps = array('duration', 'lead_in', 'lead_out', 'until_closed', 'price');
		$setProps = array('duration', 'lead_in', 'lead_out', 'until_closed');
		foreach( $setProps as $prop )
			$service->setProp( $prop, $prefillApp->getProp($prop) ); 

		$NTS_CURRENT_REQUEST[ $i ]['service'] = $service;
		}
	else {
		$serviceId = $req->getParam( 'service' );
		$serviceId = explode( '-', $serviceId );
		
		$serviceId = isset($serviceId[$i]) ? $serviceId[$i] : $serviceId[0];

		$seats = $req->getParam( 'seats' );
		$seats = explode( '-', $seats );
		$seats = isset($seats[$i]) ? $seats[$i] : $seats[0];

		if( $serviceId ){
			$service = ntsObjectFactory::get( 'service' );
			$service->setId( $serviceId );
			$NTS_CURRENT_REQUEST[ $i ]['service'] = $service;
			}
		}

	if( $seats ){
		$NTS_CURRENT_REQUEST[ $i ]['seats'] = $seats;
		}

/* resource */
	if( $NTS_VIEW['RESCHEDULE'] )
		$resourceId = $NTS_VIEW['RESCHEDULE']->getProp('resource_id');
	elseif( isset($NTS_CURRENT_REQUEST[$i]['ghost']) )
		$resourceId = $NTS_CURRENT_REQUEST[$i]['ghost']->getProp('resource_id');
	else {
		$resourceId = $req->getParam( 'resource' );
		$resourceId = explode( '-', $resourceId );
		$resourceId = isset($resourceId[$i]) ? $resourceId[$i] : $resourceId[0];
		}
	if( $resourceId ){
		$thisRes = ntsObjectFactory::get('resource');
		$thisRes->setId( $resourceId );
		$NTS_CURRENT_REQUEST[ $i ]['resource'] = $thisRes;
		}

/* location */
	if( $NTS_VIEW['RESCHEDULE'] )
		$locationId = $NTS_VIEW['RESCHEDULE']->getProp('location_id');
	elseif( isset($NTS_CURRENT_REQUEST[$i]['ghost']) )
		$locationId = $NTS_CURRENT_REQUEST[$i]['ghost']->getProp('location_id');
	else {
		$locationId = $req->getParam( 'location' );
		$locationId = explode( '-', $locationId );
		$locationId = isset($locationId[$i]) ? $locationId[$i] : $locationId[0];
		}
	if( $locationId ){
		$thisLoc = new ntsObject('location');
		$thisLoc->setId( $locationId );
		$NTS_CURRENT_REQUEST[ $i ]['location'] = $thisLoc;
		}

/* time */
	if( isset($NTS_CURRENT_REQUEST[$i]['ghost']) )
		$time = $NTS_CURRENT_REQUEST[$i]['ghost']->getProp('starts_at');
	else
		$time = $req->getParam( 'time' );
	$NTS_CURRENT_REQUEST[ $i ]['time'] = $time;
	}
	
/* calendar */
$cal = $req->getParam( 'cal' );
if( $cal ){
	$cal = explode( '-', $cal );
	for( $i = 0; $i < $total; $i++ ){
		if( isset($cal[$i]) && $cal[$i] ){
			$NTS_CURRENT_REQUEST[ $i ]['cal'] = $cal[$i];
			}
		}
	}
else {
	$cal = array();
	}

/* save on */
$saveOn = array(
	'cal'		=> join( '-', $cal ),
	'total'		=> $total,
	'cri'		=> $NTS_CURRENT_REQUEST_INDEX,
	'ghost'		=> join( '-', $ghostId )
	);
if( $packId ){
	$saveOn['pack'] = $packId;
	}

if( ! $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['ghost'] ){
	$saveOn['service'] = $serviceId;
	$saveOn['seats'] = $seats;
	$saveOn['resource'] = $resourceId;
	$saveOn['location'] = $locationId;
	$saveOn['time'] = $time;
	}

if( $reschId )
	$saveOn['reschedule'] = $reschId;

ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );

/* check what's choosing now */
$conf =& ntsConf::getInstance();
$confFlow = $conf->get('appointmentFlow');

$i = 0;
$foundEmpty = false;
reset( $NTS_CURRENT_REQUEST );
foreach( $NTS_CURRENT_REQUEST as $cr ){
	reset( $confFlow );
	foreach( $confFlow as $f ){
		if( ! $cr[$f[0]] ){
			$NTS_CURRENT_REQUEST_WHAT = $f[0];
			$foundEmpty = true;
			break;
			}
		}
	if( $foundEmpty )
		break;
	$i++;
	}

if( ! $NTS_CURRENT_REQUEST_WHAT ){
	$NTS_CURRENT_REQUEST_WHAT = 'confirm';
	}

if( preg_match('/select_recurring/', $NTS_CURRENT_PANEL) ){
	$NTS_CURRENT_REQUEST_WHAT = 'recurring';
	}
if( preg_match('/select_seats/', $NTS_CURRENT_PANEL) ){
	$NTS_CURRENT_REQUEST_WHAT = 'seats';
	}

if( isset($NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['ghost']) ){
	global $NTS_SKIP_APPOINTMENTS;
	$NTS_SKIP_APPOINTMENTS = array( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['ghost']->getId() );
	}
?>