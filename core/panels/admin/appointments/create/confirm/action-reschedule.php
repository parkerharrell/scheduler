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

/* appointment */
	$object = $NTS_VIEW['RESCHEDULE']['obj'];
	$oldStartsAt = $object->getProp('starts_at');
	$object->setByArray( $formValues );

	$object->setProp( 'service_id',		$serviceId );
	$object->setProp( 'resource_id',	$resourceId[0] );
	$object->setProp( 'customer_id',	$customerId[0] );
	$object->setProp( 'location_id',	$locationId[0] );
	$object->setProp( 'starts_at',		$time[0] );

	$service = $NTS_VIEW['CURRENT_REQUEST']['service'];
	if( $service->getProp('until_closed') ){
		$tm = new haTimeManager();
		$tm->allowEarlierThanNow = true;
		$tm->setService( $service );
		$tm->setLocation( $NTS_VIEW['CURRENT_REQUEST']['location'][0] );
		$tm->setResource( $NTS_VIEW['CURRENT_REQUEST']['resource'][0] );

		$duration = 0;
		$testTimes = $tm->getSelectableTimes( 
			$time[0],
			$time[0],
			$NTS_VIEW['RESCHEDULE']['obj']->getProp('seats')
			);
		reset( $testTimes[ $time[0] ] );
		foreach( $testTimes[ $time[0] ] as $tt ){
			if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
				$duration = $tt[ $tm->SLT_INDX['duration'] ];
			}
		}
	else {
//		$duration = $object->getProp('duration');
		$duration = $service->getProp('duration');
		}
	$object->setProp( 'duration', 		$duration );

	$object->setProp( 'lead_in', $service->getProp('lead_in') );
	$object->setProp( 'lead_out', $service->getProp('lead_out') );

	$cm->runCommand( $object, 'change', array('oldStartsAt' => $oldStartsAt) );

	if( $cm->isOk() ){
		$id = $object->getId();				

		ntsView::addAnnounce( M('Appointment') . ': ' . M('Change') . ': ' . M('OK'), 'ok' );
		$forwardTo = ntsLink::makeLink( '-current-/../../edit', '', array('_id' => $id) );

		ntsView::redirect( $forwardTo );
		exit;
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