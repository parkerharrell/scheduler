<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = new ntsObject( 'pack' );
$object->setId( $id );

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'delete' );

if( $cm->isOk() ){
	ntsView::setAnnounce( M('Appointment Pack') . ': ' . M('Deleted'), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}

/* continue to service list */
$forwardTo = ntsLink::makeLink( '-current-/..' );
ntsView::redirect( $forwardTo );
exit;
?>