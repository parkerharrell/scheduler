<?php
global $req, $NTS_CURRENT_USER, $NTS_VIEW;

$ntsdb =& dbWrapper::getInstance();

/* schedule permissions */
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

/* get all resources and fixed resource if any */
global $NTS_CURRENT_USER;
$resourceSchedules = $NTS_CURRENT_USER->getProp( '_resource_apps' );
reset( $resourceSchedules );

$allResources = array();
$allResourcesIds = array();
$managedResources = array();
$NTS_VIEW['fixId'] = array();

foreach( $resourceSchedules  as $resId => $accLevel ){
	if( $accLevel != 'none' ){
		$res = ntsObjectFactory::get( 'resource' );
		$res->setId( $resId );
		if( in_array($accLevel, array('manage', 'edit')) ){
			$managedResources[] = $res;
			}
		$allResources[] = $res;
		$NTS_VIEW['fixId'][] = $resId;
		}
	}

// sort resources by order view
usort( $allResources, create_function('$a, $b', 'return ($a->getProp("show_order") - $b->getProp("show_order"));') );
usort( $managedResources, create_function('$a, $b', 'return ($a->getProp("show_order") - $b->getProp("show_order"));') );
	
$NTS_VIEW['allResources'] = $allResources;
$NTS_VIEW['managedResources'] = $managedResources;
$NTS_VIEW['fix'] = 'resource';

/* taking all resources, check schedules to see which locations are available */
$NTS_VIEW['allLocations'] = array();

$startWhere = ( $NTS_VIEW['fixId'] ) ? '{PRFX}schedules.resource_id IN (' . join(',', $NTS_VIEW['fixId']) . ')' : '0';

$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}schedules
WHERE
	$startWhere
EOT;

$result = $ntsdb->runQuery( $sql );
$locationIds = array();
while( $e = $result->fetch() ){
	$scheduleId = $e['id'];
	$schedule = ntsObjectFactory::get( 'schedule' );
	$schedule->setId( $scheduleId );
	$locations = $schedule->getProp( '_location' );
	$locationIds = array_merge( $locationIds, $locations );
	}
$locationIds = array_unique( $locationIds );
reset( $locationIds );
foreach( $locationIds as $locId ){
	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $locId );
	$NTS_VIEW['allLocations'][] = $location;
	}

if( ! $NTS_VIEW['allLocations'] ){
	$NTS_VIEW['allLocations'] = ntsObjectFactory::getAll( 'location' );
	}
// sort locations order view
usort( $NTS_VIEW['allLocations'], create_function('$a, $b', 'return ($a->getProp("show_order") - $b->getProp("show_order"));') );

/* if explicit resource or just one then fix it */
$resourceId = $req->getParam('resource');
$saveOn[ 'resource' ] = $resourceId;
if( $resourceId ){
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $resourceId );
	$NTS_VIEW['resources'] = array( $resource );
	}
else{
	$NTS_VIEW['resources'] = $NTS_VIEW['allResources'];
	}
	
/* save on */
ntsView::setPersistentParams( $saveOn, $req, 'admin/appointments/manage' );
?>