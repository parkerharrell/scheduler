<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();

$pgm =& ntsPaymentGatewaysManager::getInstance();
$allGateways = $pgm->getActiveGateways();

$packId = $req->getParam( '_id' );
$object = new ntsObject( 'pack' );
$object->setId( $packId );

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$newGateways = $formValues['payment_gateways'];
	if( ! $newGateways )
		$newGateways = array();

	/* check if we need to delete any */
	$gw2delete = array();
	reset( $allGateways );
	foreach( $allGateways as $gw ){
		if( ! in_array($gw, $newGateways) )
			$gw2delete[] = $gw;
		}

	$object->setProp( '_disable_gateway', $gw2delete );
	$cm =& ntsCommandManager::getInstance();
	$cm->runCommand( $object, 'update' );

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Payment Options') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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