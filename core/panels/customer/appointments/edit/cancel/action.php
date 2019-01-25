<?php
global $NTS_VIEW;

$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$NTS_VIEW['id'] = $id;

switch( $action ){
	case 'cancel':
		if( ! is_array($id) ){
			$id = array( $id );
			}

		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/confirmForm';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$reason = $req->getParam( 'reason' );
			$commandParams = array(
				'reason' => $reason,
				);

			$resultCount = 0;
			$failedCount = 0;
			reset( $id );
			foreach( $id as $i ){
				$object = ntsObjectFactory::get( 'appointment' );
				$object->setId( $i );
				$cm->runCommand( $object, 'cancel', $commandParams );

				if( $cm->isOk() ){
					$resultCount++;
					}
				else {
					$failedCount++;
					}
				}

			if( $resultCount ){
				if( $resultCount > 1 )
					ntsView::addAnnounce( $resultCount . ' ' . M('Appointments') . ': ' . M('Cancelled'), 'ok' );
				else
					ntsView::addAnnounce( M('Appointment') . ': ' . M('Cancelled'), 'ok' );
				}

		/* continue to the list with anouncement */
			if( ! isset($forwardTo) ){
				$return = $req->getParam( 'return' );
				if( $return == 'all' )
					$forwardTo = ntsLink::makeLink( 'customer/appointments/browse' );
				else
					$forwardTo = ntsLink::makeLink( 'customer/appointments/browse' );
				}

			ntsView::redirect( $forwardTo );
			exit;
			}

		break;

	default:
		break;
	}

$ff =& ntsFormFactory::getInstance();
$return = $req->getParam( 'return' );
$formParams = array(
	'return'	=> $return,
	'id'		=> $id,
	);
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm', $formParams );
?>