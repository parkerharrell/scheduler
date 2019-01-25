<?php
$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

$id = $req->getParam( '_id' );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$cm =& ntsCommandManager::getInstance();
/* service */
	$object = new ntsObject( 'service' );
	$object->setId( $id );

	$object->setByArray( $formValues );
	$cm->runCommand( $object, 'update' );

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Service') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-', '', array('_id' => $id) );
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