<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = new ntsObject( 'form' );
$object->setId( $id );
$formParams = $object->getByArray();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$cm =& ntsCommandManager::getInstance();
/* service */

	$object->setByArray( $formValues );
	$cm->runCommand( $object, 'update' );

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Form') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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