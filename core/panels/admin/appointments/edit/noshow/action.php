<?php
global $NTS_VIEW;
/* redefine display file */
$NTS_VIEW['displayFile'] = dirname(__FILE__) . '/index.php';

$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();
if( ! isset($id) ){
	$id = $req->getParam( '_id' );
	}
$NTS_VIEW['id'] = $id;

switch( $action ){
	case 'noshow':
		if( ! is_array($id) ){
			$id = array( $id );
			}

		$resultCount = 0;
		$failedCount = 0;
		reset( $id );

		foreach( $id as $i ){
			$object = ntsObjectFactory::get( 'appointment' );
			$object->setId( $i );

			$cm->runCommand( $object, 'noshow' );

			if( $cm->isOk() ){
				$resultCount++;
				}
			else {
				$failedCount++;
				}
			}

		if( $resultCount ){
			if( $resultCount > 1 )
				$msg = $resultCount . ' ' . M('Appointments') . ': ' . M('Marked As No Show');
			else
				$msg = M('Appointment') . ': ' . M('Marked As No Show');
			ntsView::addAnnounce( $msg, 'ok' );
			ntsView::reloadParent();
			}

	/* continue to the list with anouncement */
		if( ! isset($forwardTo) ){
			if( isset($_SESSION['return_after_action']) && $_SESSION['return_after_action'] ){
				$forwardTo = $_SESSION['return_after_action'];
				unset( $_SESSION['return_after_action'] );
				}
			else {
				$forwardTo = ntsLink::makeLink( '-current-/../..' );
				}
			}

		ntsView::redirect( $forwardTo );
		exit;
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