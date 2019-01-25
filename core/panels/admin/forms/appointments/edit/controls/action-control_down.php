<?php
$cm =& ntsCommandManager::getInstance();

$controlId = $req->getParam('control');
$object = new ntsObject( 'form_control' );
$object->setId( $controlId );

$cm->runCommand( $object, 'move_down' );
if( $cm->isOk() ){
	ntsView::setAnnounce( M('Moved Up'), 'ok' );
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