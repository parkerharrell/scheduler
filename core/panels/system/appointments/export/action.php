<?php
$_REQUEST[NTS_PARAM_ACTION] = 'export';
$userId = 0;
$authCode = $req->getParam( 'code' );

// find user by this code
$sql =<<<EOT
SELECT
	obj_id
FROM
	{PRFX}objectmeta
WHERE
	obj_class = "user" AND
	meta_name = "_auth_code" AND
	meta_value = "$authCode"
EOT;
$result = $ntsdb->runQuery( $sql );
if( $i = $result->fetch() ){
	$userId = $i['obj_id'];
	}

if( ! $userId ){
	echo "user not found";
	exit;
	}

$object = new ntsUser();
$object->setId( $userId );
if( $object->notFound() ){
	echo "user not found";
	exit;
	}

$where = array();

if( $object->hasRole('admin') ){
	$fixId = array();
	$resourceSchedules = $object->getProp( '_resource_apps' );
	reset( $resourceSchedules );
	foreach( $resourceSchedules  as $resId => $accLevel ){
		if( $accLevel != 'none' ){
			$fixId[] = $resId;
			}
		}
	$where[] = 'resource_id IN (' . join(',', $fixId) . ')';
	}
else {
	$where[] = "customer_id = $userId";
	}

$where[] = "is_ghost = 0";
$where[] = "cancelled <> 1";
$whereString = join( ' AND ', $where );

$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	$whereString
ORDER BY
	starts_at ASC
EOT;

include_once( NTS_APP_DIR . '/helpers/ical.php' );
$NTS_VIEW['ntsCal'] = new ntsIcal();

$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$NTS_VIEW['ntsCal']->addAppointment( $e['id'] );
		}
	}

if( ob_get_contents() )
	ob_end_clean();
?>