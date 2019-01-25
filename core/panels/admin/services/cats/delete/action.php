<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = new ntsObject( 'service_cat' );
$object->setId( $id );

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'delete' );

if( $cm->isOk() ){
	ntsView::setAnnounce( M('Category') . ': ' . M('Deleted'), 'ok' );
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