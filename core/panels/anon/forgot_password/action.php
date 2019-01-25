<?php
switch( $action ){
	case 'reset':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

		/* check if we have a user with this email */
			$email = $formValues[ 'email' ];

			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();
			$info = $integrator->getUsers( array('email' => '="' . $email . '"') );

			if( $info ){
				reset( $info );
				foreach( $info as $i ){
					$object = new ntsUser();
					$object->setId( $i['id'] );

					$cm =& ntsCommandManager::getInstance();
					$cm->runCommand( $object, 'reset_password' );
					}
				if( $cm->isOk() ){
					ntsView::setAnnounce( M('Your new password has been sent to your email'), 'ok' );

				/* continue to login page */
					$forwardTo = ntsLink::makeLink( 'anon/login' );
					}
				else {
					$errorText = $cm->printActionErrors();
					ntsView::addAnnounce( $errorText, 'error' );

				/* continue to reset pass form */
					$forwardTo = ntsLink::makeLink( '-current-' );
					}
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				ntsView::setAnnounce( M('This email is not registered'), 'error' );
			/* continue to reset pass form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}

		/* continue to login page */
			$forwardTo = ntsLink::makeLink( 'anon/login' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
		/* form not valid, continue to create form */
			}

		break;

	default:
		break;
	}
?>