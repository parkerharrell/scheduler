<?php
$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();
if( ! isset($id) ){
	$id = $req->getParam( '_id' );
	}
$NTS_VIEW['id'] = $id;

if( ! is_array($id) ){
	$id = array( $id );
	}

$resultCount = 0;
$failedCount = 0;
reset( $id );

foreach( $id as $i ){
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $i );

	$cm->runCommand( $object, 'delete' );

	if( $cm->isOk() ){
		$resultCount++;
		}
	else {
		$failedCount++;
		}
	}

if( $resultCount ){
	if( $resultCount > 1 )
		$msg = $resultCount . ' ' . M('Appointments') . ': ' . M('Purged');
	else
		$msg = M('Appointment') . ': ' . M('Purged');
	ntsView::addAnnounce( $msg, 'ok' );
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