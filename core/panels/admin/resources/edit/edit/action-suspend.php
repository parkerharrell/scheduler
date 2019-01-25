<?php
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( '_id' );
$object = ntsObjectFactory::get( 'resource' );
$object->setId( $id );

$cm->runCommand( $object, 'suspend' );
if( $cm->isOk() ){
	ntsView::setAnnounce( M('Bookable Resource') . ': ' . M('Suspend') . ': ' . M('OK'), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}
/* continue to the list with anouncement */
$forwardTo = ntsLink::makeLink( '-current-/../../browse' );
ntsView::redirect( $forwardTo );
exit;
?>