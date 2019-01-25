<?php
global $NTS_CURRENT_USER, $NTS_VIEW;

$resourceSchedules = $NTS_CURRENT_USER->getProp( '_resource_schedules' );
$NTS_VIEW['RESOURCE_SCHEDULE_VIEW'] = array();
$NTS_VIEW['RESOURCE_SCHEDULE_EDIT'] = array();

reset( $resourceSchedules );
foreach( $resourceSchedules as $resId => $accLevel ){
	if( $accLevel == 'edit' ){
		$NTS_VIEW['RESOURCE_SCHEDULE_EDIT'][] = $resId;
		}
	else {
		$NTS_VIEW['RESOURCE_SCHEDULE_VIEW'][] = $resId;
		}
	}

$resourceId = $req->getParam( '_res_id' );
if( (! $resourceId) && (count($NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) + count($NTS_VIEW['RESOURCE_SCHEDULE_VIEW'])) == 1 ){
	$resourceId = ( count($NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) == 1 ) ? $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'][0] : $NTS_VIEW['RESOURCE_SCHEDULE_VIEW'][0];
	}

$NTS_VIEW['FIXED_RESOURCE'] = 0;
if( $resourceId ){
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $resourceId );
	$title = '<span class="subtitle">' . M('Bookable Resource') . ':</span> ' . ntsView::objectTitle($resource);
	$NTS_VIEW['FIXED_RESOURCE'] = $resourceId;
	ntsView::setPersistentParams( array('_res_id' => $resourceId), $req, 'admin/schedules' );
	}
?>