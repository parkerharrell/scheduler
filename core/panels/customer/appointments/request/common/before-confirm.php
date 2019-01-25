<?php
/* REDIRECT IF NEEDED */
if( $NTS_CURRENT_USER->hasRole('admin') ){
	$saveId = array();
	$saveId['location'] = array();
	$saveId['resource'] = array();
	$saveId['service'] = array();
	$saveId['time'] = array();

	reset( $NTS_CURRENT_REQUEST );
	foreach( $NTS_CURRENT_REQUEST as $cr ){
		$saveId['location'][] = $cr['location'] ? $cr['location']->getId() : 0;
		$saveId['resource'][] = $cr['resource'] ? $cr['resource']->getId() : 0;
		$saveId['service'][] = $cr['service'] ? $cr['service']->getId() : 0;
		$saveId['time'][] = $cr['time'];
		$saveId['seats'][] = $cr['seats'];
		}

	$params = array(
		'location'	=> join( '-', $saveId['location'] ),
		'resource'	=> join( '-', $saveId['resource'] ),
		'service'	=> join( '-', $saveId['service'] ),
		'time'		=> join( '-', $saveId['time'] ),
		'seats'		=> join( '-', $saveId['seats'] ),
		);

	$forwardTo = ntsLink::makeLink( 'admin/appointments/create/confirm', '', $params );
	ntsView::redirect( $forwardTo );
	exit;
	}
elseif( ($NTS_CURRENT_USER->getId() < 1) && (! isset($_SESSION['temp_customer_id'])) ){
	// redirect to login-register
	$forwardTo = ntsLink::makeLink( '-current-/../register' );
	ntsView::redirect( $forwardTo );
	exit;
	}
elseif( isset($_SESSION['temp_customer_id']) ){
	$customer = new ntsUser();
	$customer->setId( $_SESSION['temp_customer_id'] );
	if( $customer->notFound() ){
		unset($_SESSION['temp_customer_id']);
		// redirect to login & register
		$targetPanel = '-current-/../login';
		$forwardTo = ntsLink::makeLink( $targetPanel );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
?>