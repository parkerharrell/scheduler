<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/resources/edit' );

$object = ntsObjectFactory::get( 'resource' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Bookable Resource') . ':</span> ' . ntsView::objectTitle($object);

$NTS_VIEW['fixResource'] = $id;

/* get all resources and fixed resource if any */
global $NTS_CURRENT_USER;
$resourceSchedules = $NTS_CURRENT_USER->getProp( '_resource_schedules' );
reset( $resourceSchedules );

$allResources = array();
$managedResources = array();

$NTS_VIEW['viewedResourcesIds'] = array();
$NTS_VIEW['managedResourcesIds'] = array();
foreach( $resourceSchedules  as $resId => $accLevel ){
	if( $accLevel != 'none' ){
		$res = ntsObjectFactory::get( 'resource' );
		$res->setId( $resId );
		if( $accLevel == 'edit' ){
			$managedResources[] = $res;
			$NTS_VIEW['managedResourcesIds'][] = $resId;
			}
		$allResources[] = $res;
		$NTS_VIEW['viewedResourcesIds'][] = $resId;
		}
	}
$NTS_VIEW['allResources'] = $allResources;
$NTS_VIEW['managedResources'] = $managedResources;
?>