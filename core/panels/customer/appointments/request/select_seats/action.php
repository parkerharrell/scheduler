<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$displaySeats = 1;
if( $NTS_VIEW['RESCHEDULE'] ){
	$displaySeats = 0;
	}
elseif ( $NTS_CURRENT_REQUEST_INDEX > 0 ){
	// if pack or recurring apps then seats are displayed in first step only
	$displaySeats = 0;
	}
else {
	if( ! $NTS_CURRENT_REQUEST[$NTS_CURRENT_REQUEST_INDEX]['service'] ){
		// forward back to service selection
		$forwardTo = ntsLink::makeLink( '-current-/../select_service' );
		ntsView::redirect( $forwardTo );
		exit;
		}

	$minSeats =  1;
	$maxSeats = 1;

	if( $maxSeats == $minSeats ){
		$displaySeats = 0;

		$reqCount = count($NTS_CURRENT_REQUEST);
		for( $i = 0; $i < $reqCount; $i++ ){
			$NTS_CURRENT_REQUEST[ $i ]['seats'] = $minSeats;
			}

		if( $NTS_CURRENT_REQUEST[ $NTS_CURRENT_REQUEST_INDEX ]['ghost'] ){
			$reqCount = count($NTS_CURRENT_REQUEST);
			for( $i = 0; $i < $reqCount; $i++ ){
				if( $NTS_CURRENT_REQUEST[ $i ]['ghost'] ){
					$ghostApp = $NTS_CURRENT_REQUEST[ $i ]['ghost'];
					$ghostApp->setProp( 'seats', $NTS_CURRENT_REQUEST[ $i ]['seats'] );

					$cm->runCommand( $ghostApp, 'update' );
					$NTS_CURRENT_REQUEST[ $i ]['ghost'] = $ghostApp;
					}
				}
			}
		else {
			$saveId = array();
			reset( $NTS_CURRENT_REQUEST );
			foreach( $NTS_CURRENT_REQUEST as $cr ){
				if( $cr['seats'] )
					$saveId[] = $cr['seats'];
				else
					$saveId[] = 0;
				}

			/* set selected session to session */
			ntsView::setPersistentParams( array('seats' => join( '-', $saveId)), $req, 'customer/appointments/request' );
			}
		}
	}

if( ! $displaySeats ){
	/* forward to dispatcher to see what's next? */
	$noForward = true;
	require( dirname(__FILE__) . '/../common/dispatcher.php' );
	return;
	}
?>