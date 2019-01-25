<?php
$conf =& ntsConf::getInstance();
$maxAppsInPack = $conf->get('maxAppsInPack');

$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$objId = $req->getParam( '_id' );

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	for( $i = 1; $i <= $maxAppsInPack; $i++ ){
		if( isset($formValues['services-' . $i]) ){
			$values[] = join( '-', $formValues['services-' . $i] );
			}
		else
			$values[] = '';
		}
	$newValue = join( '|', $values );

	$object = new ntsObject( 'pack' );
	$object->setId( $objId );
	$object->setProp( 'services', $newValue );
	$cm->runCommand( $object, 'update' );
	
	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Appointment Pack') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-', '', array('_id' => $objId) );
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