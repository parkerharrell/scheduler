<?php
if( defined('NTS_REGISTER_REDIRECT') && NTS_REGISTER_REDIRECT ){
	ntsView::redirect( NTS_REGISTER_REDIRECT );
	exit;
	}

$conf =& ntsConf::getInstance();
switch( $action ){
	case 'register':
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
			$cm->runCommand( $object, 'create' );
			$NTS_VIEW['object'] = $object;

			if( $cm->isOk() ){
				$_SESSION['temp_customer_id'] = $object->getId();

			/* check if we need to require email validation */
				$userEmailConfirmation = $conf->get('userEmailConfirmation');
			/* or admin approval */
				$userAdminApproval = $conf->get('userAdminApproval');

				if( $userEmailConfirmation ){
					$cm->runCommand( $object, 'require_email_confirmation' );

					$display = 'emailConfirmation';
					$forwardTo = ntsLink::makeLink( '-current-', '', array('display' => $display) );
					ntsView::redirect( $forwardTo );
					exit;
					}
				elseif( $userAdminApproval ) {
					$cm->runCommand( $object, 'require_approval' );

					$display = 'waitingApproval';
					$forwardTo = ntsLink::makeLink( '-current-', '', array('display' => $display, ) );
					ntsView::redirect( $forwardTo );
					exit;
					}
				else {
				/* autoapprove */
					$cm->runCommand( $object, 'activate' );
					ntsView::addAnnounce( M('Congratulations, your account has been created and activated'), 'ok' );
				/* then login */
					$cm->runCommand( $object, 'login' );

				/* continue to login dispatcher */
					$forwardTo = ntsLink::makeLink( 'anon/login/dispatcher' );
					ntsView::redirect( $forwardTo );
					exit;
					}
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