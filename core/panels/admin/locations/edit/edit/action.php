<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$location = new ntsObject( 'location' );
$location->setId( $id );
$formParams = $location->getByArray();

switch( $action ){
	case 'update':
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $formParams );
		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$cm =& ntsCommandManager::getInstance();
		/* service */
			$object = new ntsObject( 'location' );
			$object->setId( $id );

			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Location') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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

		break;
	default:
		break;
	}

if( ! isset($form) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$form =& $ff->makeForm( $formFile, $formParams );
	}
?>