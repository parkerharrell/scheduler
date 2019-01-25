<?php
global $NTS_VIEW;
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

if( count($NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) > 1 ){
	$NTS_VIEW['displayFile'] = dirname(__FILE__) . '/chooseResource.php';
	}
else {
	$params = array(
		'_res_id'		=> $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'][0],
		'_copy_from'		=> $id,
		);
	$forwardTo = ntsLink::makeLink( 'admin/schedules/create', '', $params );
	ntsView::redirect( $forwardTo );
	exit;
	}
?>