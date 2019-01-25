<?php
$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

require( dirname(__FILE__) . '/../../common/grab.php' );

$where = array();
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$NTS_VIEW['showPerPage'] = 20;
$NTS_VIEW['currentPage'] = $req->getParam('p');
if( ! $NTS_VIEW['currentPage'] )
	$NTS_VIEW['currentPage'] = 1;
$limit = ( ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'] ) . ',' . $NTS_VIEW['showPerPage'];

$allGetParams = $req->getGetParams();
$NTS_VIEW['searchParams'] = array();
$userSearchStatus = '';

$fields = $om->getFields( 'customer', 'internal', true );
reset( $fields );
foreach( $fields as $f ){
	if( isset($allGetParams[$f[0]]) )
		$NTS_VIEW['searchParams'][$f[0]] = $allGetParams[$f[0]];
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
$where['_role'] = "=\"customer\"";

if( ! NTS_EMAIL_AS_USERNAME ){
	$order = array(
		array( 'username', 'ASC' ),
		);
	}
else {
	$order = array(
		array( 'email', 'ASC' ),
		);
	}
	
/* load users */
$NTS_VIEW['entries'] = $integrator->getUsers(
	$where,
	$order,
	$limit,
	$userSearchStatus
	);

$NTS_VIEW['totalCount'] = $integrator->countUsers(
	$where,
	$userSearchStatus
	);
	
/* pager info */
$NTS_VIEW['showFrom'] = 1 + ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'];
$NTS_VIEW['showTo'] = $NTS_VIEW['showFrom'] + $NTS_VIEW['showPerPage'] - 1;
if( $NTS_VIEW['showTo'] > $NTS_VIEW['totalCount'] )
	$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	
?>