<?php
$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();
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

	$cm->runCommand( $object, 'approve' );

	if( $cm->isOk() ){
		$resultCount++;
		}
	else {
		$failedCount++;
		}
	}

if( $resultCount ){
	if( $resultCount > 1 )
		$msg = $resultCount . ' ' . M('Appointments') . ': ' . M('Approved');
	else
		$msg = M('Appointment') . ': ' . M('Approved');
	ntsView::addAnnounce( $msg, 'ok' );
	ntsView::reloadParent();
	}
if( $failedCount ){
	if( $failedCount > 1 )
		$msg = $failedCount . ' ' . M('Appointments') . ': ' . M('Already Approved');
	else
		$msg = M('Appointment') . ': ' . M('Already Approved');
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