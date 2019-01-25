<?php
$ntsdb =& dbWrapper::getInstance();
require( dirname(__FILE__) . '/../common/grab.php' );

$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$formParams = array(
	'service_id'	=> $NTS_VIEW['CURRENT_REQUEST']['service']->getId(),
	);
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

if( $form->validate($req) ){
	$formValues = $form->getValues();

	$totalPrice = 0;
	reset( $time );
	$allApps = array();
	for( $i = 0; $i < count($time); $i++ ){
		$tt = $time[ $i ];

		$object = ntsObjectFactory::get( 'appointment' );
		$object->setByArray( $formValues );
		$object->setProp( 'starts_at', $tt );

		$thisCustId = isset($customerId[$i]) ? $customerId[$i] : $customerId[0];
		$thisLocId = isset($locationId[$i]) ? $locationId[$i] : $locationId[0];
		$thisResId = isset($resourceId[$i]) ? $resourceId[$i] : $resourceId[0];
		$thisSeats = isset($seats[$i]) ? $seats[$i] : $seats[0];

		$object->setProp( 'service_id',		$serviceId );
		$object->setProp( 'resource_id',	$thisResId );
		$object->setProp( 'location_id',	$thisLocId );
		$object->setProp( 'customer_id',	$thisCustId );
		$object->setProp( 'seats',			$thisSeats );
		$object->setProp( 'until_closed',	$service->getProp('until_closed') );

		$service = $NTS_VIEW['CURRENT_REQUEST']['service'];
		$thisPrice = ntsLib::getServicePrice( $NTS_VIEW['CURRENT_REQUEST']['service'], $thisSeats );
		$object->setProp( 'price', $thisPrice );

		if( $service->getProp('until_closed') ){
			$tm = new haTimeManager();
			$tm->allowEarlierThanNow = true;
			$tm->setService( $service );
			$tm->setLocation( $NTS_VIEW['CURRENT_REQUEST']['location'][$i] );
			$tm->setResource( $NTS_VIEW['CURRENT_REQUEST']['resource'][$i] );

			$testTimes = $tm->getSelectableTimes_Internal( 
				$time[$i],
				$time[$i],
				$thisSeats
				);

			$duration = 0;
			reset( $testTimes[ $time[$i] ] );
			foreach( $testTimes[ $time[$i] ] as $tt ){
				if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
					$duration = $tt[ $tm->SLT_INDX['duration'] ];
				}
			}
		else {
			$duration = $service->getProp('duration');
			}
		$object->setProp( 'duration', $duration );

		$object->setProp( 'lead_in', $service->getProp('lead_in') );
		$object->setProp( 'lead_out', $service->getProp('lead_out') );

/* appointment */
		$cm->runCommand( $object, 'init' );
		if( ! $cm->isOk() ){
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}

		$price = $service->getProp('price');
		if( $price )
			$totalPrice += $price;

		$allApps[] = $object;
		}

	reset( $allApps );
	foreach( $allApps as $object ){
		$cm->runCommand( $object, 'request' );
		$appId = $object->getId();
		}

/* create invoice as well if price given */
	if( $totalPrice > 0 ){
	/* generate new invoice */
		$invoice = new ntsObject( 'invoice' );
		$invoice->setProp( 'amount', $totalPrice );
		$cm->runCommand( $invoice, 'create' );

	/* update app object to store ref to invoice */
		$invoiceId = $invoice->getId();
		
		reset( $allApps );
		foreach( $allApps as $object ){
			$object->setProp( '_invoice', $invoiceId );
			$cm->runCommand( $object, 'update' );
			}
		}

/* reset appointment params */
	$saveOn = array(
		'cal'			=> 0,
		'service'		=> 0,
		'resource'		=> 0,
		'location'		=> 0,
		'time'			=> 0,
		'customer'		=> 0,
		'reschedule'	=> 0,
		);
	ntsView::setPersistentParams( $saveOn, $req, $PANEL_PREFIX );

	ntsView::addAnnounce( M('New appointment has been created and automatically approved.'), 'ok' );
	if( count($allApps) == 1 ){
		$forwardTo = ntsLink::makeLink( '-current-/../../edit', '', array('_id' => $appId) );
		}
	else {
		$forwardTo = ntsLink::makeLink( '-current-/../../manage' );
		}
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
/* form not valid, continue to create form */
	}
?>