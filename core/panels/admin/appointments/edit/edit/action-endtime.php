<?php
require( dirname(__FILE__) . '/action.php' );
$appDetails = $object->getByArray();

$endTimeDetails = $appDetails;
$endTimeDetails['end_time'] = $appDetails['starts_at'] + $appDetails['duration'];

$formFile = dirname( __FILE__ ) . '/formEndTime';
$form =& $ff->makeForm( $formFile, $endTimeDetails );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$duration = $formValues['end_time'] - $appDetails['starts_at'];
	
	$object->setProp( 'duration', $duration );

	$cm->runCommand( $object, 'update' );
	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Appointment') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );
		ntsView::reloadParent();

	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-' );
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