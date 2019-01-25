<?php
$req = new ntsRequest();
$id = $req->getParam( '_id' );

global $PANEL_PREFIX;
if( $PANEL_PREFIX )
	$panelSave = $PANEL_PREFIX . '/edit';
else
	$panelSave = 'admin/appointments/edit';

ntsView::setPersistentParams( array('_id' => $id, 'return' => 'this'), $req, $panelSave );

if( (! is_array($id)) && $id ){
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $id );

	$resourceId = $object->getProp( 'resource_id' );
	/* check if can view or manage this resource */
	global $NTS_CURRENT_USER;
	$resourceApps = $NTS_CURRENT_USER->getProp( '_resource_apps' );
	if( (! isset($resourceApps[$resourceId]) ) || ( $resourceApps[$resourceId] == 'none' ) ){
		ntsView::setAnnounce( M('Access Denied'), 'error' );
		$forwardTo = ntsLink::makeLink( 'admin/appointments' );
		ntsView::redirect( $forwardTo );
		exit;
		}

	global $NTS_READ_ONLY;
	$NTS_READ_ONLY = false;
	if( ! (isset($resourceApps[$resourceId]) && ( ($resourceApps[$resourceId] == 'manage') || ($resourceApps[$resourceId] == 'edit') )) ){
		$NTS_READ_ONLY = true;
		}

	$t = new ntsTime( $object->getProp('starts_at') );

	$service = new ntsObject( 'service' );
	$service->setId( $object->getProp('service_id') );

	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $object->getProp('resource_id') );

	$location = new ntsObject( 'location' );
	$location->setId( $object->getProp('location_id') );

	$title = '';

	$title .= $t->formatWeekday() . ', ' . $t->formatDate() . ' ' . $t->formatTime( $object->getProp('duration') );
//	$title .= $t->formatTime() . ' ' . $service->getProp('title');

	$title .= '<br><span class="subtitle">' . M('Service') . ':</span> ' . $service->getProp('title');
	$title .= '<br><span class="subtitle">' . M('Bookable Resource') . ':</span> ' . $resource->getProp('title');

//	$subHeaderFile = dirname( __FILE__ ) . '/../manage/index.php';
	}
?>