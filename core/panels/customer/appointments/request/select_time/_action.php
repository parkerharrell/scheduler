<?php
ntsView::setTitle( M('Date and Time') );

require_once( dirname(__FILE__) . '/../common/grab.php' );
if( ! $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] ){
	$forwardTo = ntsLink::makeLink( '-current-/..' );
	ntsView::redirect( $forwardTo );
	exit;
	}

if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
	global $NTS_SKIP_APPOINTMENTS;
	$NTS_SKIP_APPOINTMENTS = array( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost']->getId() );
	}

$ntsConf =& ntsConf::getInstance();
$showMonths = $ntsConf->get('monthsToShow');
$showDays = $ntsConf->get('daysToShowCustomer');

$t = $NTS_VIEW['t'];

$tm = new haTimeManager();
$tm->setService( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] );
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] ){
	$tm->setLocation( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] );
	}
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] ){
	$tm->setResource( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] );
	}

$requestedCal = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['cal']; // like 200903

$START_CHECK = 0;
$END_CHECK = 0;
/* IF CALENDAR IS SUPPLIED THEN START CHECK AT THIS DATE MONTH'S */
if( $requestedCal ){
	$t->setDateDb( $requestedCal );
	$thisDay = $t->getDay();

	$t->setStartMonth();
	$START_CHECK = $t->getTimestamp();

	if( $showMonths == 1 ){
		$t->setEndMonth();
		$endOfMonthDay = $t->getDay();
		if ( ($endOfMonthDay - $thisDay + 1) < $showDays ){
			$deltaDays = $showDays - ($endOfMonthDay - $thisDay + 1);
			$t->modify( '+' . $deltaDays . ' days' );
			}
		}
	else {
		$t->modify( '+' . $showMonths . ' months' );
		}
	$END_CHECK = $t->getTimestamp();
	}
/* OTHERWISE LET TIME MANAGER DECIDE */
else {
//	$tm->limitFrame = $showMonths * 31 * 24 * 60 * 60;
	$tm->limitFrame = 2 * 31 * 24 * 60 * 60;
	}

$tm->t = $t;
$tm->dayMode = true;
$tm->dayMode = false;

$times = $tm->getSelectableTimes( 
	$START_CHECK,
	$END_CHECK,
	$seats
	); 

if( (! $cal) && (! $times) ){
	// no times found
	ntsView::setAnnounce( ntsView::objectTitle($NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']) . ': ' . M('Not Available Now'), 'error' );
	$forwardTo = ntsLink::makeLink( '-current-/../select_service', '', array('service' => 0) );
	ntsView::redirect( $forwardTo );
	exit;
	}

$times = array_keys( $times ); 
reset( $times );
$dates = array();
foreach( $times as $ts ){
	$t->setTimestamp( $ts );
	$thisDate = $t->formatDate_Db();
	$dates[ $thisDate ] = 1;
	}

$NTS_VIEW['dates'] = $dates;

/* check how many dates for detailed display */
$timeStartDate = 0;
$timeEndDate = 0;
if( $requestedCal ){
	$shown = 0;
	foreach( $dates as $date => $one ){
		if( (! $shown) && ($requestedCal != $date)  ){
			continue;
			}
		if( ! $shown ){
			$timeStartDate = $date;
			}

		$timeEndDate = $date;
		$shown++;
		if( $shown >= $showDays ){
			break;
			}
		}
	}
else {
	$shown = 0;
	foreach( $dates as $date => $one ){
		if( ! $shown ){
			$timeStartDate = $date;
			}
		$timeEndDate = $date;
		$shown++;
		if( $shown >= $showDays ){
			break;
			}
		}
	}
	
if( ! $timeStartDate ){
	$allDates = array_keys( $dates );
	$timeStartDate = $allDates[ 0 ];
	$timeEndDate = $allDates[ count($allDates) - 1 ];
	}

$t->setDateDb( $timeStartDate );
$timesStartCheck = $t->getTimestamp();
$t->setDateDb( $timeEndDate );
$timesEndCheck = $t->getEndDay();

$tm->dayMode = false;
$times = $tm->getSelectableTimes( 
	$timesStartCheck,
	$timesEndCheck,
	$seats
	);
$times = array_keys( $times ); 

$selectFirstTimeAvailable = false;

$t->setTimestamp( $times[0] );
if( $requestedCal ){
	$cal = $requestedCal;
	}
else {
	$cal = $t->formatDate_Db();
	}

$NTS_VIEW['cal'] = $cal;

reset( $times );
$NTS_VIEW['times'] = $times;
?>