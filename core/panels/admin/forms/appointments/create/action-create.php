<?php
$ff =& ntsFormFactory::getInstance();
$conf =& ntsConf::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();

	$cm =& ntsCommandManager::getInstance();

/* location */
	$object = new ntsObject( 'form' );
	$object->setByArray( $formValues );
	$object->setProp( 'class', 'appointment' );
	$cm->runCommand( $object, 'create' );

	if( $cm->isOk() ){
		$id = $object->getId();
		ntsView::addAnnounce( M('Form') . ': ' . M('Created'), 'ok' );

	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-/../edit', '', array('_id' => $id) );
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