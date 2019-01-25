<?php
$cm =& ntsCommandManager::getInstance();

$objId = $req->getParam('cat');
$object = new ntsObject( 'service_cat' );
$object->setId( $objId );

$cm->runCommand( $object, 'move_down' );
if( $cm->isOk() ){
	ntsView::setAnnounce( M('Moved Down'), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}
/* continue to the list with anouncement */
$forwardTo = ntsLink::makeLink( '-current-' );
ntsView::redirect( $forwardTo );
exit;
?>