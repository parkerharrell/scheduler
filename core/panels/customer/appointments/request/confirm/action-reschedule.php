<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();
$formParams = array(
	'service'	=> $NTS_CURRENT_REQUEST[0]['service'],
	);
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

if( $form->validate($req) ){
	$formValues = $form->getValues();

/* appointment */
	$object = $NTS_VIEW['RESCHEDULE'];
	$oldStartsAt = $object->getProp('starts_at');

	$object->setProp( 'resource_id',	$NTS_CURRENT_REQUEST[0]['resource']->getId() );
	$object->setProp( 'location_id',	$NTS_CURRENT_REQUEST[0]['location']->getId() );
	$object->setProp( 'starts_at',		$NTS_CURRENT_REQUEST[0]['time'] );

/* calc duration as it may be until close */
	if( $NTS_VIEW['RESCHEDULE']->getProp('until_closed') ){
		$duration = 0;
		$tm = new haTimeManager();
		$tm->setService( $NTS_CURRENT_REQUEST[$i]['service'] );
		$tm->setLocation( $NTS_CURRENT_REQUEST[$i]['location'] );
		$tm->setResource( $NTS_CURRENT_REQUEST[$i]['resource'] );

		$testTimes = $tm->getSelectableTimes( 
			$NTS_CURRENT_REQUEST[0]['time'],
			$NTS_CURRENT_REQUEST[0]['time'],
			$NTS_CURRENT_REQUEST[0]['seats']
			);
		reset( $testTimes[ $NTS_CURRENT_REQUEST[0]['time'] ] );
		foreach( $testTimes[ $NTS_CURRENT_REQUEST[0]['time'] ] as $tt ){
			if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
				$duration = $tt[ $tm->SLT_INDX['duration'] ];
			}
		}
	else {
		$duration = $NTS_VIEW['RESCHEDULE']->getProp('duration');
		}
	$object->setProp( 'duration', 	$duration );

	$cm->runCommand( $object, 'change', array('oldStartsAt' => $oldStartsAt) );

	if( $cm->isOk() ){
		$id = $object->getId();				

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
		ntsView::setPersistentParams( $saveOn, $req, 'customer/appointments/request' );

		$params = array();
		$params['id'] = $object->getId();

		ntsView::addAnnounce( M('Appointment') . ': ' . M('Change') . ': ' . M('OK'), 'ok' );

		$forwardTo = ntsLink::makeLink( 'customer/appointments/view', '', $params );
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