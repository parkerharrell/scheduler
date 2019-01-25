<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$object = new ntsUser();
$object->setId( $id );
$objectInfo = $object->getByArray();

/* prepare 'resources' input */
$objectInfo['resources'] = array();
reset( $objectInfo['_resource_apps'] );
foreach( $objectInfo['_resource_apps'] as $resId => $perm ){
	if( ! isset($objectInfo['resources'][ $resId ]) )
		$objectInfo['resources'][ $resId ] = array();
	$objectInfo['resources'][ $resId ][ 'appointments' ] = $perm;
	}
reset( $objectInfo['_resource_schedules'] );
foreach( $objectInfo['_resource_schedules'] as $resId => $perm ){
	if( ! isset($objectInfo['resources'][ $resId ]) )
		$objectInfo['resources'][ $resId ] = array();
	$objectInfo['resources'][ $resId ][ 'schedules' ] = $perm;
	}

unset($objectInfo['_resource_apps']);
unset($objectInfo['_resource_schedules']);

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $objectInfo );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate($req) ){
			$formValues = $NTS_VIEW['form']->getValues();

			$resourceSchedules = array();
			$resourceApps = array();
			reset( $formValues['resources'] );
			foreach( $formValues['resources'] as $resId => $resPerms ){
				$resourceSchedules[ $resId ] = $resPerms['schedules'];
				$resourceApps[ $resId ] = $resPerms['appointments'];
				}

			$formValues['_resource_schedules'] = $resourceSchedules;
			$formValues['_resource_apps'] = $resourceApps;
			unset( $formValues['resources'] );

		/* update user */
			$object = new ntsUser();
			$object->setId( $id );

			$object->setByArray( $formValues );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::setAnnounce( M('User') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

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