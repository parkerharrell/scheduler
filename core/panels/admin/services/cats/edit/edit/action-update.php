<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( 'id' );

$NTS_VIEW['id'] = $id;
$catInfo = array(
	'id'	=> $id,
	);
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $catInfo );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$cm =& ntsCommandManager::getInstance();
/* service */
	$object = new ntsObject( 'service_cat' );
	$object->setId( $id );

	$object->setByArray( $formValues );
	$cm->runCommand( $object, 'update' );

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Category') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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