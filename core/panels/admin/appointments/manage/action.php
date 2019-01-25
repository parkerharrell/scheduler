<?php
$saveOn = array();

$viewPeriod = $req->getParam( 'viewPeriod' );
if( ! $viewPeriod )
	$viewPeriod = 'month';
$NTS_VIEW['viewPeriod'] = $viewPeriod;
$saveOn['viewPeriod'] = $viewPeriod;

$viewSplit = $req->getParam( 'viewSplit' );
if( $viewSplit ){
	$saveOn['viewSplit'] = $viewSplit;
	}
if( ! $viewSplit )
	$viewSplit = 'together';
$NTS_VIEW['viewSplit'] = $viewSplit;

if( isset($NTS_VIEW['fix']) && ($NTS_VIEW['fix'] == 'resource') && (! $NTS_VIEW['fixId']) ){
	$NTS_VIEW['displayFile'] = NTS_APP_DIR . '/panels/admin/appointments/manage/index-no-resources.php';
	return;
	}

global $PANEL_PREFIX, $NTS_CURRENT_USER;
if( ! $PANEL_PREFIX ){
	$PANEL_PREFIX = 'admin/appointments/manage';
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

$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$params = array( 'service', 'resource', 'location', 'viewPeriod', 'viewSplit' );
$presets = array();
reset( $params );
foreach( $params as $p ){
	$presets[ $p ] = $req->getParam($p);
	}
$NTS_VIEW['selectorForm'] =& $ff->makeForm( dirname(__FILE__) . '/selectorForm', $presets );

$tm = new haTimeManager();

/* customer */
$NTS_VIEW[ 'customer' ] = null;
$customerId = $req->getParam('customer');
if( $customerId ){
	$saveOn[ 'customer' ] = $customerId;
	$customer = new ntsUser;
	$customer->setId( $customerId );
	$NTS_VIEW[ 'customer' ] = $customer;
	}

/* location */
if( NTS_SINGLE_LOCATION )
	$locationId = NTS_SINGLE_LOCATION;
else {
	$locationId = $req->getParam('location');
	if( ! $locationId && count($NTS_VIEW['allLocations']) ){
		$locationId = $NTS_VIEW['allLocations'][0]->getId();
		}
	$saveOn[ 'location' ] = $locationId;
	}
if( $locationId ){
	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $locationId );
	$tm->setLocation( $location );
	$NTS_VIEW[ 'location' ] = $location;
	}

/* service */
$serviceId = $req->getParam('service');
if( $serviceId ){
	$saveOn[ 'service' ] = $serviceId;

	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $serviceId );
	$tm->setService( $service );
	}

$selectableTimes = array();
$workingTimes = array();
$timeoffs = array();

/* calendar */
$t = new ntsTime();
$NTS_VIEW['selectedDay'] = $t->formatDate_Db(); // today

$cal = $req->getParam( 'cal' );	
if( $cal ){
	$t->setDateDb( $cal );
	$saveOn['cal'] = $cal;
	}

$NTS_VIEW['calYear'] = $t->getYear();
$NTS_VIEW['calMonth'] = $t->getMonth();
$NTS_VIEW['calDay'] = $t->getDay();
$NTS_VIEW['t'] = $t;

$SLOTS = array();
$DATES = array();
$APPS_BY_DATE = array();

/* PREPARE SLOTS */
switch( $viewPeriod ){
	case 'month':
		require( dirname(__FILE__) . '/_action_MonthView.php' );
		break;
	case 'day':
		require( dirname(__FILE__) . '/_action_DayView.php' );
		break;
	case 'week':
		require( dirname(__FILE__) . '/_action_WeekView.php' );
		break;
	}

$WORKING_TIMES = array();
$TIMEOFFS = array();
$SELECTABLE_TIMES = array();
$APPS = array();
$APPS_BY_SERVICE = array();

$slotsCount = count( $SLOTS );
$seats = 1;
reset( $NTS_VIEW['resources'] );
foreach( $NTS_VIEW['resources'] as $r ){
	$thisResId = $r->getId();
	$tm->setResource( $r );

	if( $viewPeriod == 'month' ){
		$tm->t = $t;
		$tm->dayMode = true;
		}

	$allSelectableTimes = $tm->getSelectableTimes_Internal( $fullStart, $fullEnd, $seats );
	$allWorkingTimes = $tm->getBlocks( $fullStart, $fullEnd );
	$allTimeoffs = $tm->getTimeoffs( $fullStart, $fullEnd );
	$allApps = $tm->getAppointments( $fullStart, $fullEnd );

	for( $s = 0; $s < $slotsCount; $s++ ){
		$WORKING_TIMES[ $s ][ $thisResId ] = array();

		reset( $allWorkingTimes );
		foreach( $allWorkingTimes as $lrs => $ba ){
			list( $tli, $tri, $tsi ) = explode( '-', $lrs );
			if( $tri != $thisResId ){
				continue;
				}
			foreach( $ba as $b ){
				$subBlocksCount = count($b);
				$blockSeats = $b[0][ $tm->BLK_INDX['seats'] ];
				$blockStartsAt = $b[0][ $tm->BLK_INDX['starts_at'] ];
				$blockEndsAt = $b[$subBlocksCount-1][ $tm->BLK_INDX['ends_at'] ]; 

				if( $blockStartsAt >= $SLOTS[$s][1] )
					break;
				if( $blockEndsAt <= $SLOTS[$s][0] )
					continue;
				$WORKING_TIMES[ $s ][ $thisResId ][ $tsi ] = $blockSeats;
				break;
				}
			}

		$TIMEOFFS[ $s ][ $thisResId ] = 0;
		reset( $allTimeoffs );
		foreach( $allTimeoffs as $to ){
			if( $to['starts_at'] >= $SLOTS[$s][1] )
				break;
			if( $to['ends_at'] <= $SLOTS[$s][0] )
				continue;
			$TIMEOFFS[ $s ][ $thisResId ] = 1;
			break;
			}

		$APPS[ $s ][ $thisResId ] = array( 0, 0 );
		$APPS_BY_SERVICE[ $s ][ $thisResId ] = array();
		reset( $allApps );
		foreach( $allApps as $aa ){
			if( ($aa['starts_at'] - $aa['lead_in']) >= $SLOTS[$s][1] )
				break;
			if( ($aa['starts_at'] + $aa['duration'] + $aa['lead_out']) <= $SLOTS[$s][0] )
				continue;
			if( $aa['approved'] )
				$APPS[ $s ][ $thisResId ][0] += $aa['seats'];
			else
				$APPS[ $s ][ $thisResId ][1] += $aa['seats'];
			if( ! isset($APPS_BY_SERVICE[ $s ][ $thisResId ][ $aa['service_id'] ]) )
				$APPS_BY_SERVICE[ $s ][ $thisResId ][ $aa['service_id'] ] = 0;
			$APPS_BY_SERVICE[ $s ][ $thisResId ][ $aa['service_id'] ] += $aa['seats'];
			}

		$SELECTABLE_TIMES[ $s ][ $thisResId ] = 0;
		reset( $allSelectableTimes );
		$count = 0;

		if( $viewPeriod == 'month' ){
			foreach( $allSelectableTimes as $tsDate => $stArray ){
				if( $tsDate == $SLOTS[$s][2] ){
					$SELECTABLE_TIMES[ $s ][ $thisResId ] = 1;
					break;
					}
				}
			}
		else {
			foreach( $allSelectableTimes as $ts => $stArray ){
				if( $ts >= $SLOTS[$s][1] )
					break;
				if( $ts < $SLOTS[$s][0] )
					continue;
				$SELECTABLE_TIMES[ $s ][ $thisResId ] = 1;
				break;
				}
			}
		}

	reset( $DATES );
	foreach( $DATES as $thisDate => $dateArray ){
		reset( $allApps );
		foreach( $allApps as $aa ){
			if( ! isset($APPS_BY_DATE[ $thisDate ][ $thisResId ]) )
				$APPS_BY_DATE[ $thisDate ][ $thisResId ] = array( 0, 0 );
			if( ($aa['starts_at'] - $aa['lead_in']) >= $dateArray[1] )
				break;
			if( ($aa['starts_at'] + $aa['duration'] + $aa['lead_out']) <= $dateArray[0] )
				continue;
			if( $aa['approved'] )
				$APPS_BY_DATE[ $thisDate ][ $thisResId ][0]++;
			else
				$APPS_BY_DATE[ $thisDate ][ $thisResId ][1]++;
			}
		}
	}

$NTS_VIEW['SELECTABLE_TIMES'] = $SELECTABLE_TIMES;
$NTS_VIEW['APPS'] = $APPS;
$NTS_VIEW['APPS_BY_SERVICE'] = $APPS_BY_SERVICE;
$NTS_VIEW['APPS_BY_DATE'] = $APPS_BY_DATE;
$NTS_VIEW['TIMEOFFS'] = $TIMEOFFS;
$NTS_VIEW['WORKING_TIMES'] = $WORKING_TIMES;
$NTS_VIEW['SLOTS'] = $SLOTS;

ntsView::setPersistentParams( $saveOn, $req, $PANEL_PREFIX );
?>