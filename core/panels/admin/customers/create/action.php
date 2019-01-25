<?php
$class = 'customer';
$NTS_VIEW['class'] = $class;

switch( $action ){
	case 'create':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$fparams = array(
			'class'	=> $class
			);
		$form =& $ff->makeForm( $formFile, $fparams );

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
			$object->setProp('_role', array($class) );

			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				if( isset($formValues['notify']) && $formValues['notify'] ){
					$cm->runCommand( $object, 'activate' );
					}

				$announce = M('Customer') . ': ' . M('Created');
				$panel = '-current-/../edit';
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