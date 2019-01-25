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

		/* resource */
			$object = ntsObjectFactory::get( 'resource' );
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'create' );
			$newObjId = $object->getId();

		/* assign all rights for the creating user */
			global $NTS_CURRENT_USER;
			$resourceSchedules = $NTS_CURRENT_USER->getProp( '_resource_schedules' );
			$resourceApps = $NTS_CURRENT_USER->getProp( '_resource_apps' );
			$resourceSchedules[ $newObjId ] = 'edit';
			$resourceApps[ $newObjId ] = 'manage';
			$NTS_CURRENT_USER->setProp( '_resource_schedules', $resourceSchedules );
			$NTS_CURRENT_USER->setProp( '_resource_apps', $resourceApps );

			$cm->runCommand( $NTS_CURRENT_USER, 'update' );

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Bookable Resource') . ': ' . M('Created'), 'ok' );

			/* continue to the list with anouncement */
				global $NTS_CURRENT_USER;
				if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/resources/edit') )
					$forwardTo = ntsLink::makeLink( '-current-/../edit/staff', '', array('_id' => $newObjId) );
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