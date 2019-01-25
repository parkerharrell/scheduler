<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = new ntsUser();
$object->setId( $id );

switch( $action ){
	case 'delete':
		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'delete' );

		if( $cm->isOk() ){
			ntsView::setAnnounce( M('User') . ': ' . M('Deleted'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}

		/* continue to service list */
		$forwardTo = ntsLink::makeLink( '-current-/../..' );
		ntsView::redirect( $forwardTo );
		exit;
		break;

	default:
		$NTS_VIEW['object'] = $object;
		break;
	}
?>