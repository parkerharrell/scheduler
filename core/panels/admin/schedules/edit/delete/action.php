<?php
$forwardTo = $_SERVER['HTTP_REFERER'];
if( strpos($forwardTo, '?') === false ){
	$forwardTo = '?';
	}
else {
	preg_match( '/(\?.+)$/', $forwardTo, $ma );
	$forwardTo = $ma[1];
	}

$id = $req->getParam( '_id' );
$object = new ntsObject( 'schedule' );
$object->setId( $id );

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'delete' );

if( $cm->isOk() ){
	ntsView::setAnnounce( M('Schedule') . ': ' . M('Deleted'), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}

/* redirect back to the referrer */
ntsView::redirect( $forwardTo );
exit;
?>