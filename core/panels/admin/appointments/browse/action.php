<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();

$viewMode = $req->getParam( 'viewMode' );
if( $viewMode == 'ajax' )
	$NTS_VIEW['skipMenu'] = true;

if( isset($NTS_VIEW['fix']) && ($NTS_VIEW['fix'] == 'resource') && (! $NTS_VIEW['fixId']) ){
	$NTS_VIEW['displayFile'] = NTS_APP_DIR . '/panels/admin/appointments/manage/index-no-resources.php';
	return;
	}

$t = new ntsTime();
$NTS_VIEW['t'] = $t;

/* fix can be customer, resource, location */
if( ! isset($NTS_VIEW['fix']) ){
	$NTS_VIEW['fix'] = '';
	$NTS_VIEW['fixId'] = 0;
	}
$showAllDisplays = array( 
	'print',
	'ical',
	'excel',
	'all'
	);

$display = $req->getParam( 'display' );
$perPage = $req->getParam( 'perpage' );
if( ! $perPage )
	$perPage = 10;
$NTS_VIEW['showPerPage'] = $perPage;
$NTS_VIEW['currentPage'] = 1;

if( in_array($display, $showAllDisplays) || ($perPage == 'all') ){
	$limit = '';
	}
else {
	$NTS_VIEW['currentPage'] = $req->getParam('p');
	if( ! $NTS_VIEW['currentPage'] )
		$NTS_VIEW['currentPage'] = 1;
	$limit = ( ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'] ) . ',' . $NTS_VIEW['showPerPage'];
	}

$where = array();
if( $NTS_VIEW['fix'] ){
	$fixId = $NTS_VIEW['fixId'];
	switch( $NTS_VIEW['fix'] ){
		case 'resource':
			if( is_array($fixId) )
				$where[] = 'resource_id IN (' . join(',', $fixId) . ')';
			else
				$where[] = "resource_id = $fixId";
			break;
		case 'customer':
			$where[] = "customer_id = $fixId";
			break;
		case 'location':
			$where[] = "location_id = $fixId";
			break;
		case 'service':
			$where[] = "service_id = $fixId";
			break;
		}
	}

if( $display == 'ical' ){
	$where[] = "cancelled <> 1";
	}

/* check what to show */
$NTS_VIEW['searchParams'] = array();
$NTS_VIEW['showFilter'] = false;

$from = $req->getParam('from');
$to = $req->getParam('to');
$show = $req->getParam( 'show' );
$createdFrom = $req->getParam('createdFrom');
$createdTo = $req->getParam('createdTo');

$orderBy = 'starts_at ASC';
/* search params */
$status = $req->getParam( 'status' );
$serviceId = $req->getParam( 'service' );
$locationId = $req->getParam( 'location' );
$resourceId = $req->getParam( 'resource' );
$customerId = $req->getParam( 'customer' );

$saveOn = array();

$TIMEFRAME_SET = false;

if( $status || $from || $to || $createdFrom || $createdTo || $serviceId || $locationId || $resourceId || $customerId ){
	/* from and to */
	$NTS_VIEW['showFilter'] = true;

	if( $from ){
		$NTS_VIEW['searchParams']['from'] = $from;
		$saveOn['from'] = $from;

		$fromTimestamp = $t->timestampFromDbDate( $from );
		$NTS_VIEW['fromTimestamp'] = $fromTimestamp;
		$where[] = "starts_at >= $fromTimestamp";
		$TIMEFRAME_SET = true;
		}

	if( $to ){
		list( $year, $month, $day ) = ntsTime::splitDate( $to );

		$t->setDateTime( $year, $month, $day + 1, 0, 0, 0 );
		$toTimestamp = $t->getTimestamp() - 1;

		$NTS_VIEW['searchParams']['to'] = $to;
		$saveOn['to'] = $to;
		$NTS_VIEW['toTimestamp'] = $toTimestamp;

		if( isset($NTS_VIEW['searchParams']['to']) )
			$where[] = "starts_at < $toTimestamp";
		$TIMEFRAME_SET = true;
		}

	if( $createdFrom ){
		$NTS_VIEW['searchParams']['createdFrom'] = $createdFrom;
		$where[] = "created_at >= $createdFrom";
		}
	if( $createdTo ){
		$NTS_VIEW['searchParams']['createdTo'] = $createdTo;
		$where[] = "created_at <= $createdTo";
		}

	/* status */
	if( $status ){
		$NTS_VIEW['searchParams']['status'] = $status;
		$saveOn['status'] = $status;
		switch( $status ){
			case 'approved':
				$where[] = "approved = 1";
				$where[] = "cancelled <> 1";
				break;
			case 'pending':
				$where[] = "approved <> 1";
				$where[] = "cancelled <> 1";
				break;
			case 'noshow':
				$where[] = "no_show = 1";
				$where[] = "cancelled <> 1";
				break;
			case 'cancelled':
				$where[] = "cancelled = 1";
				break;
			}
		}

	/* service */
	if( $serviceId ){
		$service = new ntsObject( 'service' );
		$service->setId( $serviceId );
		$NTS_VIEW['searchParams']['service'] = $service;
		$saveOn['service'] = $serviceId;
		$where[] = "service_id = $serviceId";
		}

	/* location */
	if( $locationId ){
		$location = new ntsObject( 'location' );
		$location->setId( $locationId );
		$NTS_VIEW['searchParams']['location'] = $location;
		$saveOn['location'] = $locationId;
		$where[] = "location_id = $locationId";
		}

	/* resource */
	if( $resourceId ){
		$resource = ntsObjectFactory::get( 'resource' );
		$resource->setId( $resourceId );
		$NTS_VIEW['searchParams']['resource'] = $resource;
		$saveOn['resource'] = $resourceId;
		$where[] = "resource_id = $resourceId";
		}

	/* customer */
	if( $customerId ){
		$customer = new ntsUser();
		$customer->setId( $customerId );
		$NTS_VIEW['searchParams']['customer'] = $customer;
		$saveOn['customer'] = $customerId;
		$where[] = "customer_id = $customerId";
		}
	}

if( ! $TIMEFRAME_SET ){
	$today = $t->formatDate_Db();
	$todayTimestamp = $t->timestampFromDbDate( $today );

	if( ! $show )
		$show = 'upcoming';
	$NTS_VIEW['searchParams']['show'] = $show;

	if( $show == 'upcoming' ){
		$where[] = "starts_at >= $todayTimestamp";
		}
	else {
		$where[] = "(starts_at + duration + lead_out) < $todayTimestamp";
		$orderBy = 'starts_at DESC';
		}
	}
$NTS_VIEW['show'] = $show;

$where[] = "is_ghost = 0";
$whereString = join( ' AND ', $where );

// save on
ntsView::setPersistentParams( $saveOn, $req, 'admin/appointments/browse' );

/* entries */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	$whereString
ORDER BY
	$orderBy
EOT;
if( $limit )
	$sql .=<<<EOT

LIMIT $limit
EOT;

$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result ){
	while( $e = $result->fetch() ){
		$a = ntsObjectFactory::get( 'appointment' );
		$a->setId( $e['id'] );
		$NTS_VIEW['entries'][] = $a;
		}
	}

/* total count */
$NTS_VIEW['totalCount'] = 0;
$sql =<<<EOT
SELECT
	COUNT({PRFX}appointments.id) AS count 
FROM
	{PRFX}appointments
WHERE
	$whereString
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	if( $e = $result->fetch() ){
		$NTS_VIEW['totalCount'] = $e['count'];
		}
	}

/* pager info */
if( in_array($display, $showAllDisplays) || ($perPage == 'all') ){
	$NTS_VIEW['showFrom'] = 1;
	$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	}
else {
	$NTS_VIEW['showFrom'] = 1 + ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'];
	$NTS_VIEW['showTo'] = $NTS_VIEW['showFrom'] + $NTS_VIEW['showPerPage'] - 1;
	if( $NTS_VIEW['showTo'] > $NTS_VIEW['totalCount'] )
		$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	}
$NTS_VIEW['action'] = $action;

switch( $action ){
	case 'export':
		$t = new ntsTime;
		switch( $display ){
			case 'ical':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.ics';
				ntsLib::startPushDownloadContent( $fileName, 'text/calendar' );
				require( dirname(__FILE__) . '/ical.php' );
				exit;
				break;
			case 'excel':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.csv';
				ntsLib::startPushDownloadContent( $fileName );
				require( dirname(__FILE__) . '/excel.php' );
				exit;
				break;
				
			}
		break;
	default:
		break;
	}
?>