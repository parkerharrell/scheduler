<?php
/* check what's choosing now */
$conf =& ntsConf::getInstance();
$flow = $conf->get('appointmentFlow');

/* if recurrent needed */
$displayRecurrent = 0;
if(
	($NTS_CURRENT_REQUEST_INDEX == 0)
	&& (! $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['ghost'] ) 
	&& ( ! $NTS_VIEW['RESCHEDULE'] )
	){
	if( $NTS_CURRENT_REQUEST[0]['service'] ){
		$recurTotal = $NTS_CURRENT_REQUEST[0]['service']->getProp('recur_total');
		if( $recurTotal > 1 )
			$displayRecurrent = 1;
		}
	}
if( $displayRecurrent ){
	$flow[] = array( 'recurring', 'manual' );
	}

$NTS_CURRENT_REQUEST_WHAT = '';

$i = 0;
$foundEmpty = false;
reset( $NTS_CURRENT_REQUEST );
foreach( $NTS_CURRENT_REQUEST as $cr ){
	reset( $flow );
	foreach( $flow as $ff ){
		if( ! $cr[$ff[0]] ){
			$NTS_CURRENT_REQUEST_WHAT = $ff[0];
			$NTS_CURRENT_REQUEST_INDEX = $i;
			$foundEmpty = true;
			break;
			}
		}
	if( $foundEmpty )
		break;
	$i++;
	}

$saveOn = array(
	'cri'		=> $NTS_CURRENT_REQUEST_INDEX,
	);
ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );

//echo "crw = $NTS_CURRENT_REQUEST_WHAT, cri = $NTS_CURRENT_REQUEST_INDEX<br>";

if( ! $NTS_CURRENT_REQUEST_WHAT ){
	$NTS_CURRENT_REQUEST_WHAT = 'confirm';
	}

/* CHECK BEFORE SUBMIT ACTION */
if( $NTS_CURRENT_REQUEST_WHAT == 'confirm' ){
	/* it may redirect and exit in this file */
	require( dirname(__FILE__) . '/before-confirm.php' );
	}

$panelPrefix = 'customer/appointments/request';
switch( $NTS_CURRENT_REQUEST_WHAT ){
	case 'confirm':
		$nextPanel = $panelPrefix . '/confirm';
		break;
	case 'location':
		$nextPanel = $panelPrefix . '/select_location';
		break;
	case 'recurring':
		$nextPanel = $panelPrefix . '/select_recurring';
		break;
	case 'seats':
		$nextPanel = $panelPrefix . '/select_seats';
		break;
	case 'time':
		$nextPanel = $panelPrefix . '/select_time';
		break;
	case 'resource':
		$nextPanel = $panelPrefix . '/select_resource';
		break;
	case 'service':
		$nextPanel = $panelPrefix . '/select_service';
		break;
	}

if( ! (isset($noForward) && $noForward) ){
	$forwardTo = ntsLink::makeLink( $nextPanel );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	ntsView::setNextAction( $nextPanel );
	return;
	}
?>