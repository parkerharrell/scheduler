<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$allServiceIds = array();
$formParams['services'] = array();
$reqCount = count($NTS_CURRENT_REQUEST); 
for( $i = 0; $i < $reqCount; $i++ ){
	$thisServiceId = $NTS_CURRENT_REQUEST[$i]['service']->getId();
	if( ! in_array($thisServiceId, $allServiceIds) ){
		$formParams['services'][] = $NTS_CURRENT_REQUEST[$i]['service'];
		$allServiceIds[] = $NTS_CURRENT_REQUEST[$i]['service']->getId();
		}
	}

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

if( $form->validate($req) ){
	$formValues = $form->getValues();

/* appointment */
	list( $discountedPrices, $fullPrices, $totalDiscountedPrice, $totalFullPrice ) = ntsLib::getPackPrice( $NTS_CURRENT_REQUEST, $NTS_VIEW['PACK'] );

	reset( $NTS_CURRENT_REQUEST );
	$allApps = array();
	$alreadySumPrice = 0;

	for( $i = 0; $i < count($NTS_CURRENT_REQUEST); $i++ ){
		$tt = $NTS_CURRENT_REQUEST[ $i ]['time'];
		if( $NTS_CURRENT_REQUEST[ $i ]['ghost'] ){
			$object = $NTS_CURRENT_REQUEST[ $i ]['ghost'];
			}
		else {
			$object = ntsObjectFactory::get( 'appointment' );
			}
		$object->setByArray( $formValues );
		$object->setProp( 'starts_at',		$tt );

		if( NTS_CURRENT_USERID ){
			$object->setProp( 'customer_id', NTS_CURRENT_USERID );
			}
		elseif( isset($_SESSION['temp_customer_id']) ){
			$object->setProp( 'customer_id', $_SESSION['temp_customer_id'] );
			}
		elseif( $req->getParam('email') && $req->getParam('first_name') && $req->getParam('last_name') ){
			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();

			$myWhere = array();
			$myWhere['email'] = ' = "' . $req->getParam('email') . '"';
			$thisUsers = $integrator->getUsers( $myWhere );
			if( $thisUsers && count($thisUsers) > 0 ){
				$existingUserId = $thisUsers[0]['id'];
				$object->setProp( 'customer_id', $existingUserId );
				}
			else {
				$targetPanel = '-current-/register';
				// redirect to register
				$forwardTo = ntsLink::makeLink( $targetPanel );
				ntsView::redirect( $forwardTo );
				exit;
				}
			}
		else {
			// redirect to login & register
			$targetPanel = '-current-/../login';
			$forwardTo = ntsLink::makeLink( $targetPanel );
			ntsView::redirect( $forwardTo );
			exit;
			}

		$object->setProp( 'service_id',		$NTS_CURRENT_REQUEST[$i]['service']->getId() );
		$object->setProp( 'resource_id',	$NTS_CURRENT_REQUEST[$i]['resource']->getId() );
		$object->setProp( 'location_id',	$NTS_CURRENT_REQUEST[$i]['location']->getId() );
		$object->setProp( 'until_closed',	$NTS_CURRENT_REQUEST[$i]['service']->getProp('until_closed') );

	/* calc duration as it may be until close */
		if( $NTS_CURRENT_REQUEST[$i]['service']->getProp('until_closed') ){
			$duration = 0;
			$tm = new haTimeManager();
			$tm->setService( $NTS_CURRENT_REQUEST[$i]['service'] );
			$tm->setLocation( $NTS_CURRENT_REQUEST[$i]['location'] );
			$tm->setResource( $NTS_CURRENT_REQUEST[$i]['resource'] );

			$testTimes = $tm->getSelectableTimes( 
				$NTS_CURRENT_REQUEST[$i]['time'],
				$NTS_CURRENT_REQUEST[$i]['time'],
				$NTS_CURRENT_REQUEST[$i]['seats']
				);
			reset( $testTimes[ $NTS_CURRENT_REQUEST[$i]['time'] ] );
			foreach( $testTimes[ $NTS_CURRENT_REQUEST[$i]['time'] ] as $tt ){
				if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
					$duration = $tt[ $tm->SLT_INDX['duration'] ];
				}
			}
		else {
			$duration = $NTS_CURRENT_REQUEST[$i]['service']->getProp('duration');
			}
		$object->setProp( 'duration', 		$duration );

		$object->setProp( 'lead_in',		$NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_in') );
		$object->setProp( 'lead_out',		$NTS_CURRENT_REQUEST[$i]['service']->getProp('lead_out') );

	// set price
		$object->setProp( 'price', $discountedPrices[$i] );
		if( $NTS_CURRENT_REQUEST[ $i ]['ghost'] ){
			$cm->runCommand( $object, 'update' );
			}
		else {
			$cm->runCommand( $object, 'init' );
			$errorText = $cm->printActionErrors();

			if( ! $cm->isOk() ){
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			}

		$allApps[] = $object;
		}

	if( $cm->isOk() ){
		$id = $object->getId();

	/* reset appointment params */
		$saveOn = array(
			'service'	=> 0,
			'resource'	=> 0,
			'location'	=> 0,
			'time'		=> 0,
			);
		ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );

	/* check if payment required */
		if( $totalDiscountedPrice > 0 ){
		/* generate new invoice */
			$invoice = new ntsObject( 'invoice' );
			$invoice->setProp( 'amount', $totalDiscountedPrice );
			$cm->runCommand( $invoice, 'create' );

		/* update app object to store ref to invoice */
			$invoiceId = $invoice->getId();
			reset( $allApps );
			foreach( $allApps as $object ){
				$object->setProp( '_invoice', $invoiceId );
				if( $NTS_VIEW['PACK'] ){
					$object->setProp( '_pack', $NTS_VIEW['PACK']->getId() );
					}
				$cm->runCommand( $object, 'update' );
				}

		/* invoice created - redirect to payment screen */
			$refno = $invoice->getProp('refno');
			$forwardTo = ntsLink::makeLink( 'customer/invoices/pay', '', array('refno' => $refno) );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* service permissions will be checked in virtual _request command */
			reset( $allApps );
			foreach( $allApps as $object ){
				$cm->runCommand( $object, '_request' );
				}

			$firstAppId = $allApps[0]->getId();
			$customerId = $allApps[0]->getProp('customer_id');
			$serviceId = $allApps[0]->getProp('service_id'); 
			$service = ntsObjectFactory::get( 'service' );
			$service->setId( $serviceId );
			$returnUrl = $service->getProp( 'return_url' );
			if( $returnUrl ){
				$forwardTo = $returnUrl;
				}
			else {
				$forwardTo = ntsLink::makeLink( 'customer/appointments/view', '', array('id' => $firstAppId, 'request' => 1, 'customer_id' => $customerId) );
				}
			ntsView::redirect( $forwardTo );
			exit;
			}
		}
	else {
		$errorText = $cm->printActionErrors();
		ntsView::addAnnounce( $errorText, 'error' );
		}
	}
else {
/* form not valid, continue to create form */
	}

?>