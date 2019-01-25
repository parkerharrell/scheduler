<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( 'timeblock_id' );

$object = new ntsObject( 'timeblock' );
$object->setId( $id );
$scheduleId = $object->getProp( 'schedule_id' );

$cm =& ntsCommandManager::getInstance();
$cm->runCommand( $object, 'delete' );

if( $cm->isOk() ){
	ntsView::setAnnounce( M('Time Slot') . ': ' . M('Deleted'), 'ok' );
	}
else {
	$errorText = $cm->printActionErrors();
	ntsView::addAnnounce( $errorText, 'error' );
	}

/* continue to timeblocks list */
$forwardTo = ntsLink::makeLink( '-current-/..', '', array('id' => $scheduleId) );
ntsView::redirect( $forwardTo );
exit;
?>