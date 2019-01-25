<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = ntsObjectFactory::get('service');
$object->setId( $id );

$NTS_VIEW['object'] = $object;

switch( $action ){
	case 'update':
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$autoConfirmed = array();
			$notAllowed = array();

			$permissions = array();
			reset( $formValues );
			foreach( $formValues as $ctlName => $ctlValue ){
				$permissions[] = $ctlName . ':' . $ctlValue;
				}

			$cm =& ntsCommandManager::getInstance();
			$object->setProp( '_permissions', $permissions );
			$cm->runCommand( $object, 'update' );

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Permissions') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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
		$permissions = $object->getPermissions();

		$formFile = dirname( __FILE__ ) . '/form';
		if( ! isset($form) ){
			$form =& $ff->makeForm( $formFile, $permissions );
			}
		break;
	}
?>