<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$toId = $req->getParam( 'id' );
$object = new ntsObject('timeoff');
$object->setId( $toId );
$cm->runCommand( $object, 'delete' );

if( $cm->isOk() ){
	ntsView::addAnnounce( M('Timeoff') . ': ' . M('Deleted'), 'ok' );

/* continue to list */
	$forwardTo = ntsLink::makeLink( '-current-' );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	$actionError = true;
	$errorString = $cm->printActionErrors();
	}
?>