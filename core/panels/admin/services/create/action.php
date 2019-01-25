<?php
switch( $action ){
	case 'create':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$cm =& ntsCommandManager::getInstance();

		/* service */
			$object = ntsObjectFactory::get( 'service' );
			$object->setByArray( $formValues );

			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				$id = $object->getId();

				ntsView::addAnnounce( M('Service') . ': ' . M('Created'), 'ok' );

			/* continue to the list with anouncement */
				global $NTS_CURRENT_USER;
				if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/services/edit') )
					$forwardTo = ntsLink::makeLink( '-current-/../edit', '', array('_id' => $id) );
				else
					$forwardTo = ntsLink::makeLink( '-current-/..' );
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
?>