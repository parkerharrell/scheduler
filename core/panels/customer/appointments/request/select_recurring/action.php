<?php
ntsView::setTitle( M('Single Or Recurring Appointments?') );

require_once( dirname(__FILE__) . '/../common/grab.php' );

$displayRecurrent = 1;
if( $NTS_CURRENT_REQUEST_INDEX > 0 ){
	$displayRecurrent = 0;
	}
elseif( $NTS_VIEW['RESCHEDULE'] ){
	$displayRecurrent = 0;
	}
else {
	$recurTotal = $NTS_CURRENT_REQUEST[ 0 ]['service']->getProp('recur_total');
	if( $recurTotal <= 1 )
		$displayRecurrent = 0;
	}

if( ! $displayRecurrent ){
	/* forward to dispatcher to see what's next? */
	require( dirname(__FILE__) . '/../common/dispatcher.php' );
	}
?>