<?php
switch( $action ){
	case 'create':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $req->getParam('noEmail') ){
			$removeValidation[] = 'email';
			}

		if( $form->validate($req, $removeValidation) ){
			$formValues = $form->getValues();
			if( isset($formValues['noEmail']) && $formValues['noEmail'] )
				$formValues['email'] = '';

			$cm =& ntsCommandManager::getInstance();

		/* customer */
			$object = new ntsUser();
			unset( $formValues['password2'] );
			$object->setByArray( $formValues );
			$object->setProp('_role', array('admin') );
			
		/* default permissions as by creating user */
			global $NTS_CURRENT_USER;
			$object->setProp( '_disabled_panels', $NTS_CURRENT_USER->getProp('_disabled_panels') ); 

			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				$announce = M('Administrative User') . ': ' . M('Created');
				$panel = 'admin/staff';

				ntsView::addAnnounce( $announce, 'ok' );

				$id = $object->getId();
			/* continue to edit with anouncement */
				$forwardTo = ntsLink::makeLink( $panel, '', array('_id' => $id) );
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