<?php
global $NTS_CURRENT_USER, $NTS_VIEW;
require( dirname(__FILE__) . '/../subheader.php' );

$ntsdb =& dbWrapper::getInstance();
$t = new ntsTime;
$req = new ntsRequest();
$id = $req->getParam( '_id' );

ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/schedules/edit' );

$schedule = new ntsObject( 'schedule' );
$schedule->setId( $id );

$resourceId = $schedule->getProp( 'resource_id' );
/* check if can view or manage this resource */
if( ! (in_array($resourceId, $NTS_VIEW['RESOURCE_SCHEDULE_VIEW']) || in_array($resourceId, $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'])) ){
	ntsView::setAnnounce( M('Access Denied'), 'error' );
	$forwardTo = ntsLink::makeLink( 'admin/schedules' );
	ntsView::redirect( $forwardTo );
	exit;
	}

global $NTS_READ_ONLY;
$NTS_READ_ONLY = false;
if( ! in_array($resourceId, $NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) ){
	$NTS_READ_ONLY = true;
	}

$t->setDateDb( $schedule->getProp('valid_from') );
$scheduleDate = $t->formatDate();

$t->setDateDb( $schedule->getProp('valid_to') );
$scheduleDate .= ' - ' . $t->formatDate();

$resourceId = $schedule->getProp('resource_id');
$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );

$title = '<span class="subtitle">' . M('Bookable Resource') . ':</span> ' . ntsView::objectTitle($resource);
$title .= '<br><span class="subtitle">' . M('Schedule') . ':</span> ' . ntsView::objectTitle($schedule) . ' [' . $scheduleDate . ']';
?>