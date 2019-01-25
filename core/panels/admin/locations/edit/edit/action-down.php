<?php
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( '_id' );
$object = ntsObjectFactory::get( 'location' );
$object->setId( $id );

$cm->runCommand( $object, 'move_down' );
if( $cm->isOk() ){
	ntsView::setAnnounce( M('Moved Down'), 'ok' );
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