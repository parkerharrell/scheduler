<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = new ntsObject('location');
$object->setId( $id );

switch( $action ){
	case 'delete':
		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'delete' );

		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Location') . ': ' . M('Deleted'), 'ok' );
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
		
	/* count how many appointments already exists for this location */
		$sql =<<<EOT
		SELECT
			COUNT(*) AS count
		FROM
			{PRFX}appointments
		WHERE
			{PRFX}appointments.location_id = $id
EOT;

		$result = $ntsdb->runQuery( $sql );
		$NTS_VIEW['appsCount'] = 0;
		if( $result && $e = $result->fetch() ){
			$NTS_VIEW['appsCount'] = $e['count'];
			}
		break;
	}
?>