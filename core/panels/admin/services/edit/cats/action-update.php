<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$serviceId = $req->getParam( '_id' );
$object = new ntsObject( 'service' );
$object->setId( $serviceId );

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$newCats = $formValues['cats'];
	if( ! $newCats )
		$newCats = array();
	
	$cm =& ntsCommandManager::getInstance();

/* session */
	$object->setProp( '_service_cat', $newCats );
	$cm->runCommand( $object, 'update' );

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Categories') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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