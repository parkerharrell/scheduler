<?php
$conf =& ntsConf::getInstance();
require_once( dirname(__FILE__) . '/../common/grab.php' );

$tm = new haTimeManager();
$tm->setService( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] );
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] ){
	$tm->setResource( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['resource'] );
	}

$allLocationIds = array();
// WITH TIME
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['time'] ){
	$thisTs = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['time'];
	$startCheck = $thisTs - $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']->getProp('lead_in');
	$endCheck = $thisTs + $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']->getProp('duration') + $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service']->getProp('lead_out');
	$seats = $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['seats'] ? $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['seats'] : 1;

	$times = $tm->getSelectableTimes( 
		$startCheck,
		$endCheck,
		$seats
		);

	if( isset($times[$thisTs]) ){
		reset($times[$thisTs]);
		foreach( $times[$thisTs] as $slot ){
			$allLocationIds[] = $slot[$tm->SLT_INDX['location_id']];
			}
		$allLocationIds = array_unique( $allLocationIds );
		}
	}
// NO TIME
else {
	$availability = $tm->check();
	$NTS_VIEW['availability'] = $availability;
	$allLocationIds = array_keys( $availability['locations'] );
	}
?>