<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( 'id' );

/* field itself */
$sql = "SELECT * FROM {PRFX}form_controls WHERE id = $id";
$result = $ntsdb->runQuery( $sql );
$o = $result->fetch();
$NTS_VIEW['o'] = $o;

switch( $action ){
	case 'delete':
		$object = new ntsObject( 'form_control' );
		$object->setByArray( $o );
		$object->setId( $id );

		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'delete' );

		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Form Field') . ': ' . M('Deleted'), 'ok' );

		/* continue to zip list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-/../..' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
		break;

	default:
		break;
	}
?>