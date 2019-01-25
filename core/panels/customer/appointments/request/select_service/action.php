<?php
ntsView::setTitle( M('Services') );
$ntsConf =& ntsConf::getInstance();
$showMonths = $ntsConf->get('monthsToShow');

$tm = new haTimeManager();
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] ){
	$tm->setResource( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] );
	}
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] ){
	$tm->setLocation( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] );
	}

$availability = $tm->check();
$NTS_VIEW['availability'] = $availability;

$limitService = $req->getParam( 'service' );
if( strlen($limitService) && $limitService ){
	if( strpos($limitService, '-') !== false ){
		$limitService = explode( '-', $limitService );
		}
	else {
		$limitService = array( $limitService );
		}
	}
else {
	$limitService = array();
	}

$serviceWhereString = '';
if( $limitService ){
	$serviceWhereString = 'WHERE {PRFX}services.id IN (' . join($limitService, ',')  . ')';
	}

$ntsdb =& dbWrapper::getInstance();

$req->resetParam( 'service' );
require_once( dirname(__FILE__) . '/../common/grab.php' );

if( $NTS_VIEW['RESCHEDULE'] ){
	/* set selected session to session */
	ntsView::setPersistentParams( array('service' => $NTS_VIEW['RESCHEDULE']->getProp('service_id')), $req, 'customer/appointments/request' );

	/* forward to dispatcher to see what's next? */
	require( dirname(__FILE__) . '/../common/dispatcher.php' );
	exit;
	}

/* services */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}services
$serviceWhereString
ORDER BY
	show_order ASC
EOT;

$entries = array();
$result = $ntsdb->runQuery( $sql );

$allServices = array();
if( $result ){
	while( $e = $result->fetch() ){
		$serviceId = $e['id'];
		if( in_array($serviceId, $allServices) )
			continue;
		$allServices[] = $serviceId;

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $serviceId );
		
		$skipService = false;

	/* check if price defined and no payment gateways */
		$pgs = $service->getPaymentGateways();
		$price = $service->getProp( 'price' );
		if( $price && (! count($pgs)) ){
			$skipService = true;
			}

	/* CHECK PERMISSIONS */
		if( ! ( $NTS_CURRENT_USER->hasRole('admin') ) ){
			$groupId = $NTS_CURRENT_USER->getId() ? 0 : -1;
			$permission = $service->getPermissionsForGroup( $groupId );
			switch( $permission ){
				case 'not_allowed':
					$skipService = true;
					break;
				case 'not_shown':
					if( ! ($limitService && in_array($serviceId, $limitService)) )
						$skipService = true;
					break;
				}
			}

		if( $skipService )
			continue;

		$timeBlockResourceWhere = '';
		if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] ){
			$resId = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource']->getId();
			$timeBlockResourceWhere = "AND {PRFX}schedules.resource_id = $resId";
			}

		/* check if available for this resource */
		$sql4 =<<<EOT
SELECT
	COUNT(*) AS subcount
FROM 
	{PRFX}objectmeta AS objm

INNER JOIN
	{PRFX}schedules
ON
	{PRFX}schedules.id = objm.obj_id

WHERE
	objm.obj_class = "schedule" AND 
	objm.meta_name = "_service" AND
	objm.meta_value = $serviceId
	$timeBlockResourceWhere
EOT;
		$result4 = $ntsdb->runQuery( $sql4 );
		$lo = $result4->fetch();
		$locCount = $lo['subcount'];

		if( ! $locCount )
			continue;

		if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] ){
			$locId = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location']->getId();
			/* check if within this location */

			if( ! $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] ){
				$sql3 =<<<EOT

SELECT
	COUNT(*) AS subcount
FROM 
	{PRFX}objectmeta AS objm
INNER JOIN
	{PRFX}objectmeta AS objm2
ON
	objm.obj_id = objm2.obj_id AND
	objm.obj_class = objm2.obj_class
WHERE
	objm.obj_class = "schedule" AND 

	objm.meta_name = "_service" AND
	objm.meta_value = $serviceId AND

	objm2.meta_name = "_location" AND
	objm2.meta_value = $locId
EOT;
				}
			else {
				$resId = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource']->getId();
				$locId = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location']->getId();

				$sql3 =<<<EOT
SELECT
	COUNT(*) AS subcount
FROM 
	{PRFX}objectmeta AS objm

INNER JOIN
	{PRFX}schedules
ON
	{PRFX}schedules.id = objm.obj_id

WHERE
	objm.obj_class = "schedule" AND 
	objm.meta_name = "_location" AND
	objm.meta_value = $locId
	AND 
	{PRFX}schedules.resource_id = $resId
EOT;
				}
			
			$result3 = $ntsdb->runQuery( $sql3 );
			$lo = $result3->fetch();
			$locCount = $lo['subcount'];
			if( ! $locCount )
				continue;
			}

		$entries[] = $service;
		}
	}

$packs = array();

/* if pack already selected */
if( $NTS_VIEW['PACK'] ){
	/* get pack's services */
	$pack = $NTS_VIEW['PACK'] ;
	$serviceString = $pack->getProp('services');
	$thisPackServices = ntsLib::splitPackServicesString( $serviceString );
	if( isset($thisPackServices[$NTS_CURRENT_REQUEST_INDEX]) ){
		$finalEntries = array();
		reset( $entries );
		foreach( $entries as $s ){
			if( ! in_array($s->getId(), $thisPackServices[$NTS_CURRENT_REQUEST_INDEX]) ){
				continue;
				}
			$finalEntries[] = $s;
			}
		$entries = $finalEntries;
		}
	else {
		ntsView::setAnnounce( ntsView::objectTitle($NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']) . ': ' . M('Not Available Now'), 'error' );
		$forwardTo = ntsLink::makeLink( '-current-/../select_service' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
else {
/* if packs available */
	$sql11 =<<<EOT
SELECT
	id, services
FROM
	{PRFX}packs
ORDER BY
	show_order ASC
EOT;

	$result11 = $ntsdb->runQuery( $sql11 );
	while( $p = $result11->fetch() ){
		$string = $p['services'] ;
		$thisPackServices = ntsLib::allPackServices( $string );

	// check if this pack requires services that are not available here
		$missingServices = array_diff( $thisPackServices, $allServices );
		if( $missingServices )
			continue;

		$pack = new ntsObject( 'pack' );
		$pack->setId( $p['id'] );
		$packs[] = $pack;
		}
	}

/* now delete the sessions that are on pack only */
if( ! $NTS_VIEW['PACK'] ){
	$finalEntries = array();
	reset( $entries );
	foreach( $entries as $s ){
		if( $s->getProp('pack_only') )
			continue;

		$finalEntries[] = $s;
		}
	$entries = $finalEntries;
	}

/* ONLY ONE SESSION AND NO PACKS - REDIRECT */
if( 0 && (count($entries) == 1) && (! $packs) ){
	$service = $entries[0];
	$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['service'] = $service;

	if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
		$ghostApp = $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'];
		$ghostApp->setProp( 'service_id', $service->getId() );

		$cm->runCommand( $ghostApp, 'update' );
		$NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] = $ghostApp;
		}
	else {
		$saveId = array();
		reset( $NTS_CURRENT_REQUEST );
		foreach( $NTS_CURRENT_REQUEST as $cr ){
			if( $cr['service'] )
				$saveId[] = $cr['service']->getId();
			else
				$saveId[] = 0;
			}
		/* set selected session to session */
		ntsView::setPersistentParams( array('service' => join( '-', $saveId) ), $req, 'customer/appointments/request' );
		}

	/* forward to dispatcher to see what's next? */
	$noForward = true;
	require( dirname(__FILE__) . '/../common/dispatcher.php' );
	return;
	}

$NTS_VIEW['entries'] = $entries;
$NTS_VIEW['packs'] = $packs;
?>