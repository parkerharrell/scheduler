<?php
$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

$t = new ntsTime;

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$showAllDisplays = array( 
	'print',
	'excel'
	);

$NTS_VIEW['sortByOptions'] = array();
if( ! NTS_EMAIL_AS_USERNAME )
	$NTS_VIEW['sortByOptions'][] = array( 'username', M('Username') );
$NTS_VIEW['sortByOptions'][] = array( 'email', M('Email') );
$NTS_VIEW['sortByOptions'][] = array( 'last_name', M('Last Name') );
$NTS_VIEW['sortByOptions'][] = array( 'first_name', M('First Name') );

$sortBy = $req->getParam( 'sort' );
if( ! $sortBy ){
	$sortBy = $NTS_VIEW['sortByOptions'][0][0];
	}
$NTS_VIEW['sortBy'] = $sortBy;

$display = $req->getParam( 'display' );
if( in_array($display, $showAllDisplays) ){
	$limit = '';
	}
else {
	$NTS_VIEW['showPerPage'] = 20;
	$NTS_VIEW['currentPage'] = $req->getParam('p');
	if( ! $NTS_VIEW['currentPage'] )
		$NTS_VIEW['currentPage'] = 1;
	$limit = ( ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'] ) . ',' . $NTS_VIEW['showPerPage'];
	}

$userSearchStatus = '';

$where = array();
$where['_role'] = '="customer"';

switch( $action ){
	case 'search':
	case 'export':
		$allGetParams = $req->getGetParams();
		$NTS_VIEW['searchParams'] = array();

		$fields = $om->getFields( 'customer', 'internal', true );
		reset( $fields );
		foreach( $fields as $f ){
			if( isset($allGetParams[$f[0]]) )
				$NTS_VIEW['searchParams'][$f[0]] = $allGetParams[$f[0]];
			}

		if( isset($allGetParams['nts_user_status']) ){
			$NTS_VIEW['searchParams']['nts_user_status'] = $allGetParams['nts_user_status'];
			$userSearchStatus = $allGetParams['nts_user_status'];
			}

		reset( $NTS_VIEW['searchParams'] );
	/* ok now search in local table if any custom fields provided */
		foreach( $NTS_VIEW['searchParams'] as $k => $v ){
			$v = strtolower( $v );
			if( $v == '-any-' ){
				unset($NTS_VIEW['searchParams'][$k]);
				continue;
				}
			if( $k == 'nts_user_status' ){
				continue;
				}

			if( strpos($v, '*') === false ){
				$where[$k] = "=\"$v\"";
				}
			else {
				$v = str_replace( '*', '%', $v );
				$where[$k] = " LIKE \"$v\"";
				}
			}
		break;
	default:
		break;
	}

$order = array(
	array( $NTS_VIEW['sortBy'], 'ASC' )
	);
/* load users */

$NTS_VIEW['entries'] = $integrator->getUsers(
	$where,
	$order,
	$limit,
	$userSearchStatus
	);

//_print_r( $NTS_VIEW['entries'] );
/* total count */
$NTS_VIEW['totalCount'] = $integrator->countUsers(
	$where,
	$userSearchStatus
	);
	
/* pager info */
if( in_array($display, $showAllDisplays) ){
	$NTS_VIEW['showFrom'] = 1;
	$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	}
else {
	$NTS_VIEW['showFrom'] = 1 + ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'];
	$NTS_VIEW['showTo'] = $NTS_VIEW['showFrom'] + $NTS_VIEW['showPerPage'] - 1;
	if( $NTS_VIEW['showTo'] > $NTS_VIEW['totalCount'] )
		$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	}

switch( $action ){
	case 'export':
		$fileName = 'appointment-users-' . $t->formatDate_Db() . '.csv';
		ntsLib::startPushDownloadContent( $fileName );
		require( dirname(__FILE__) . '/excel.php' );
		exit;
		break;
	default:
		break;
	}

$NTS_VIEW['action'] = $action;
?>