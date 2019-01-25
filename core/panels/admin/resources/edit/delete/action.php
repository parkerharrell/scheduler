<?php
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );

$object = ntsObjectFactory::get( 'resource' );
$object->setId( $id );

switch( $action ){
	case 'delete':
		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'delete' );

		if( $cm->isOk() ){
			ntsView::setAnnounce( M('Bookable Resource') . ': ' . M('Deleted'), 'ok' );
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
		
	/* count how many appointments already exists for this resource */
		$sql =<<<EOT
		SELECT
			COUNT(*) AS count
		FROM
			{PRFX}appointments
		WHERE
			{PRFX}appointments.resource_id = $id
EOT;

		$result = $ntsdb->runQuery( $sql );
		$NTS_VIEW['appsCount'] = 0;
		if( $result && $e = $result->fetch() ){
			$NTS_VIEW['appsCount'] = $e['count'];
			}
		break;
	}
?>