<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( '_id' );
$schedule = new ntsObject( 'schedule' );
$schedule->setId( $id );
$formParams = $schedule->getByArray();

switch( $action ){
	case 'update':
		$NTS_VIEW['id'] = $id;

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $formParams );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$cm =& ntsCommandManager::getInstance();

		/* schedule */
			$object = new ntsObject( 'schedule' );
			$object->setId( $id );
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'update' );

			$scheduleInfo = $object->getByArray();

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Schedule') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id) );
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
		break;

	default:
		break;
	}

if( ! isset($form) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$form =& $ff->makeForm( $formFile, $formParams );
	}
?>