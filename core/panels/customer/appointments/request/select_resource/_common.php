<?php
$conf =& ntsConf::getInstance();
require_once( dirname(__FILE__) . '/../common/grab.php' );

$tm = new haTimeManager();
$tm->setService( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] );
if( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] ){
	$tm->setLocation( $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['location'] );
	}

$allResourceIds = array();
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
			$allResourceIds[] = $slot[$tm->SLT_INDX['resource_id']];
			}
		$allResourceIds = array_unique( $allResourceIds );
		}
	}
// NO TIME
else {
	$availability = $tm->check();
	$NTS_VIEW['availability'] = $availability;
	$allResourceIds = array_keys( $availability['resources'] );
	}
?>