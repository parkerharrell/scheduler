<?php
$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

require( dirname(__FILE__) . '/../common/grab.php' );

/* if customer already set then forward to next */
$customerId = $req->getParam( 'customer' );
if( $customerId ){
	$saveOn = array(
		'customer'	=> $customerId,
		);
	ntsView::setPersistentParams( $saveOn, $req, $PANEL_PREFIX );

	/* forward to dispatcher to see what's next? */
	$forwardTo = ntsLink::makeLink( '-current-/../dispatcher', '', array('from' => 'customer') );
	ntsView::redirect( $forwardTo );
	exit;
	}

switch( $action ){
	case 'select':
		require( dirname(__FILE__) . '/../common/grab.php' );

		$customerId = $req->getParam( 'id' );
		$saveOn = array(
			'customer'	=> $customerId,
			);
		ntsView::setPersistentParams( $saveOn, $req, $PANEL_PREFIX );

	/* forward to dispatcher to see what's next? */
		$forwardTo = ntsLink::makeLink( '-current-/../confirm' );

		ntsView::redirect( $forwardTo );
		exit;
		break;

	case 'create':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();
		$allowDuplicateEmails = $conf->get( 'allowDuplicateEmails' );

		$formFile = dirname( __FILE__ ) . '/createForm';
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

			if( ! NTS_ENABLE_REGISTRATION )
				$formValues['username'] = $formValues['email'];

			$object->setByArray( $formValues );

			if( $registerNew ){
				$object->setProp( '_role', array('customer') );
				$cm->runCommand( $object, 'create' );
				if( isset($formValues['notify']) && $formValues['notify'] ){
					$cm->runCommand( $object, 'activate' );
					}

				if( $cm->isOk() ){
					$announce = M('Customer') . ': ' . M('Created');

					ntsView::addAnnounce( $announce, 'ok' );

					$id = $object->getId();
				/* continue to edit with anouncement */
					$forwardTo = ntsLink::makeLink( '-current-', 'select', array('id' => $id) );
					ntsView::redirect( $forwardTo );
					exit;
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
					$id = $object->getId();
				/* continue to edit with anouncement */
					$forwardTo = ntsLink::makeLink( '-current-', 'select', array('id' => $id) );
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
		break;

	case 'start':
		$ff =& ntsFormFactory::getInstance();
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();
			reset( $formValues );
			$searchParams = array();
			foreach( $formValues as $key => $value ){
				$value = trim( $value );
				if( $value ){
					$searchParams[ $key ] = $value;
					}
				}

		/* continue to search results */
			$forwardTo = ntsLink::makeLink( '-current-/results', '', $searchParams );
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

/* reset customer */
$req->resetParam( 'customer' );
require( dirname(__FILE__) . '/../common/grab.php' );

/* if reschedule forward to next */
if( $NTS_VIEW['RESCHEDULE'] ){
	$forwardTo = ntsLink::makeLink( '-current-/../dispatcher', '', array('from' => 'review') );
	ntsView::redirect( $forwardTo );
	exit;
	}
?>