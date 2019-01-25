<?php
global $NTS_PERSISTENT_PARAMS, $NTS_CURRENT_USER;
require_once( dirname(__FILE__) . '/../common/grab.php' );

$ff =& ntsFormFactory::getInstance();
$conf =& ntsConf::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );

$removeValidation = array();
if( NTS_ALLOW_NO_EMAIL && $req->getParam('noEmail') ){
	$removeValidation[] = 'email';
	}

if( $form->validate($req, $removeValidation) ){
	$registerNew = true;
	$formValues = $form->getValues();

	$cm =& ntsCommandManager::getInstance();

/* customer */
	$object = new ntsUser();
	unset( $formValues['password2'] );

	$conf =& ntsConf::getInstance();
	$allowDuplicateEmails = $conf->get( 'allowDuplicateEmails' );

/* if no reg enabled and this email exists, find it first */
	if( (! NTS_ENABLE_REGISTRATION) && $formValues['email'] && (! $allowDuplicateEmails) ){
		$uif =& ntsUserIntegratorFactory::getInstance();
		$integrator =& $uif->getIntegrator();

		$myWhere = array();
		$myWhere['email'] = ' = "' . $formValues['email'] . '"';

		$thisUsers = $integrator->getUsers( $myWhere );

		if( $thisUsers && count($thisUsers) > 0 ){
			$existingUserId = $thisUsers[0]['id'];
			$object->setId( $existingUserId );
			$registerNew = false;
			}
		}

	if( ! NTS_ENABLE_REGISTRATION ){
		if( $formValues['email'] )
			$formValues['username'] = $formValues['email'];
		}

	$object->setByArray( $formValues );
	$object->setProp( '_timezone', $NTS_CURRENT_USER->getTimezone() );

	if( $registerNew ){
		$cm->runCommand( $object, 'create' );

		if( $cm->isOk() ){
			if( NTS_ENABLE_REGISTRATION ){
			/* check if we need to require email validation */
				$userEmailConfirmation = $conf->get('userEmailConfirmation');
			/* or admin approval */
				$userAdminApproval = $conf->get('userAdminApproval');
				}
			else {
			/* registration not enabled - not email confirmation required */	
				$userEmailConfirmation = 0;
				$userAdminApproval = 1;
				}

			if( $userEmailConfirmation || $userAdminApproval ){
				if( $userEmailConfirmation ){
					$cm->runCommand( $object, 'require_email_confirmation' );
					}
				elseif( $userAdminApproval ) {
					$cm->runCommand( $object, 'require_approval' );
					}

				$_SESSION['temp_customer_id'] = $object->getId();
				$targetPanel = '-current-/../confirm';
				$addonParams = array(
					'email'			=> $object->getProp( 'email' ),
					'first_name'	=> $object->getProp( 'first_name' ),
					'last_name'		=> $object->getProp( 'last_name' ),
					);
				$forwardTo = ntsLink::makeLink( $targetPanel, '', $addonParams );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
			/* autoapprove */
				$cm->runCommand( $object, 'activate' );
//					ntsView::addAnnounce( M('Congratulations, your account has been created and activated'), 'ok' );

				if( defined('NTS_SKIP_COOKIE') && NTS_SKIP_COOKIE ){
					$targetPanel = '-current-/../confirm';
					$addonParams = array(
						'email'			=> $object->getProp( 'email' ),
						'first_name'	=> $object->getProp( 'first_name' ),
						'last_name'		=> $object->getProp( 'last_name' ),
						);
					$forwardTo = ntsLink::makeLink( $targetPanel, '', $addonParams );
					ntsView::redirect( $forwardTo );
					exit;
					}
				else {
				/* then login */
					$cm->runCommand( $object, 'login' );

					$targetPanel = 'customer/appointments/request/confirm';
					$_SESSION['return_after_login']['nts-panel'] = $targetPanel;
					$_SESSION['return_after_login']['params'] = $NTS_PERSISTENT_PARAMS['customer/appointments/request'];

				/* continue to login dispatcher */
					$forwardTo = ntsLink::makeLink( 'anon/login/dispatcher' );
					ntsView::redirect( $forwardTo );
					exit;
					}
				}
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
		}
	else {
		// update existing customer record
		$cm->runCommand( $object, 'update' );
		if( $cm->isOk() ){
			$_SESSION['temp_customer_id'] = $object->getId();
			$targetPanel = '-current-/../confirm';
			$addonParams = array(
				'email'			=> $object->getProp( 'email' ),
				'first_name'	=> $object->getProp( 'first_name' ),
				'last_name'		=> $object->getProp( 'last_name' ),
				);
			$forwardTo = ntsLink::makeLink( $targetPanel, '', $addonParams );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
		}
	}
else {
/* form not valid, continue to create form */
	}
?>