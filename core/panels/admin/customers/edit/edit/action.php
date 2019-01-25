<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

if( ! isset($id) ){
	$id = $req->getParam( '_id' );
	}
$NTS_VIEW['id'] = $id;

switch( $action ){
	case 'suspend':
		if( ! is_array($id) )
			$id = array( $id );

		$resultCount = 0;
		$failedCount = 0;
		reset( $id );
		foreach( $id as $i ){
			$object = new ntsUser();
			$object->setId( $i );
			$cm->runCommand( $object, 'suspend' );

			if( $cm->isOk() ){
				$resultCount++;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				$failedCount++;
				$actionOk = false;
				}
			}

		if( $resultCount ){
			$msg = $resultCount . ' ' . M('Users') . ': ' . M('Suspended');
			ntsView::addAnnounce( $msg, 'ok' );
			}

	/* continue to the list with anouncement */
		if( ! isset($forwardTo) ){
			$return = $req->getParam( 'return' );
			if( $return == 'edit' && (count($id) == 1) )
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id[0]) );
			else
				$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
			}

		ntsView::redirect( $forwardTo );
		exit;
		break;

	case 'activate':
		if( ! is_array($id) )
			$id = array( $id );

		$resultCount = 0;
		reset( $id );

		foreach( $id as $i ){
			$object = new ntsUser();
			$object->setId( $i );
			$cm->runCommand( $object, 'activate' );
			
			if( $cm->isOk() ){
				$resultCount++;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				break;
				}
			}

		if( $resultCount ){
			$msg = $resultCount . ' ' . M('Users') . ': ' . M('Activate') . ': ' . M('OK');
			ntsView::addAnnounce( $msg, 'ok' );
			}

	/* continue to the list with anouncement */
		if( ! isset($forwardTo) ){
			$return = $req->getParam( 'return' );
			if( $return == 'edit' && (count($id) == 1) )
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id[0]) );
			else
				$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
			}

		ntsView::redirect( $forwardTo );
		exit;
		break;

	case 'update':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';

		$object = new ntsUser();
		$object->setId( $id );
		$customerInfo = $object->getByArray();
		$customerInfo['object'] = $object;

		$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $customerInfo );

		$removeValidation = array();
		if( NTS_ALLOW_NO_EMAIL && $req->getParam('noEmail') ){
			$removeValidation[] = 'email';
			}

		if( $NTS_VIEW['form']->validate($req, $removeValidation) ){
			$formValues = $NTS_VIEW['form']->getValues();
			if( isset($formValues['noEmail']) && $formValues['noEmail'] )
				$formValues['email'] = '';

		/* update customer */
			$object = new ntsUser();
			$object->setId( $id );
			$object->setByArray( $formValues );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::setAnnounce( M('User') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id ) );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to edit form */
			}
		break;
	}

/* user info */
$object = new ntsUser();
$object->setId( $id );
$customerInfo = $object->getByArray();
$customerInfo['object'] = $object;

/* emails */
$toEmail = $customerInfo['email'];
$toEmail = str_replace( '@', '\@', $toEmail );

$sql =<<<EOT
SELECT
	COUNT(*) AS count 
FROM
	{PRFX}emaillog
WHERE
	to_email = '$toEmail'
EOT;
$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['emailLogs'] = $result->fetch();
	
if( ! isset($NTS_VIEW['form']) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $customerInfo );
	}
?>