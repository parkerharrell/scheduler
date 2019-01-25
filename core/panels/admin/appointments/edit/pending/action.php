<?php
$cm =& ntsCommandManager::getInstance();
$db =& dbWrapper::getInstance();
if( ! isset($id) ){
	$id = $req->getParam( '_id' );
	}
if( ! is_array($id) ){
	$id = array( $id );
	}
$NTS_VIEW['id'] = $id;

$resultCount = 0;
$failedCount = 0;
reset( $id );
foreach( $id as $i ){
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $i );

	$cm->runCommand( $object, 'pending' );

	if( $cm->isOk() ){
		$resultCount++;
		}
	else {
		$failedCount++;
		}
	}

if( $resultCount ){
	if( $resultCount > 1 )
		$msg = $resultCount . ' ' . M('Appointments') . ': ' . M('Pending');
	else
		$msg = M('Appointment') . ': ' . M('Pending');
	ntsView::addAnnounce( $msg, 'ok' );
	ntsView::reloadParent();
	}
if( $failedCount ){
	if( $failedCount > 1 )
		$msg = $failedCount . ' ' . M('Appointments') . ': ' . M('Already Pending');
	else
		$msg = M('Appointment') . ': ' . M('Already Pending');
	ntsView::addAnnounce( $msg, 'error' );
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
?>