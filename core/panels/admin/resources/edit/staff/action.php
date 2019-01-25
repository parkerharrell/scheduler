<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$object = ntsObjectFactory::get( 'resource' );
$object->setId( $id );
$objectInfo = $object->getByArray();

list( $appsAdmins, $scheduleAdmins ) = $object->getAdmins();

$currentAdminsIds = array();
$objectInfo['staff'] = array();
reset( $appsAdmins );
foreach( $appsAdmins as $admId => $perm ){
	if( ! isset($objectInfo['staff'][ $admId ]) )
		$objectInfo['staff'][ $admId ] = array();
	$objectInfo['staff'][ $admId ][ 'appointments' ] = $perm;
	$currentAdminsIds[] = $admId;
	}
reset( $scheduleAdmins );
foreach( $scheduleAdmins as $admId => $perm ){
	if( ! isset($objectInfo['staff'][ $admId ]) )
		$objectInfo['staff'][ $admId ] = array();
	$objectInfo['staff'][ $admId ][ 'schedules' ] = $perm;
	$currentAdminsIds[] = $admId;
	}
$currentAdminsIds = array_unique( $currentAdminsIds );

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $objectInfo );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate($req) ){
			$formValues = $NTS_VIEW['form']->getValues();
			$newAdminsIds = array_keys( $formValues['staff'] );
			$allAdminsIds = array_merge( $currentAdminsIds, $newAdminsIds );
			$allAdminsIds = array_unique( $allAdminsIds );

			reset( $allAdminsIds );
			foreach( $allAdminsIds as $admId ){
				$admin = new ntsUser;
				$admin->setId( $admId );
				$resApps = $admin->getProp( '_resource_apps' );
				if( isset($formValues['staff'][$admId]['appointments']) )
					$resApps[ $id ] = $formValues['staff'][$admId]['appointments'];
				else
					$resApps[ $id ] = array();
				$admin->setProp( '_resource_apps', $resApps );
				
				$resSched = $admin->getProp( '_resource_schedules' );
				if( isset($formValues['staff'][$admId]['schedules']) )
					$resSched[ $id ] = $formValues['staff'][$admId]['schedules'];
				else 
					$resSched[ $id ] = array();
				$admin->setProp( '_resource_schedules', $resSched );

				$cm->runCommand( $admin, 'update' );
				}

			if( $cm->isOk() ){
				ntsView::setAnnounce( M('Bookable Resource') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id ) );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to edit form */
			}

		break;
	default:
		break;
	}
?>