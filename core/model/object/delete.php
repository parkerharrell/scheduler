<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

$className = $object->getClassName();
$metaClass = $object->getMetaClass();
$id = $object->getId();

/* MAIN TABLE */
if( (! isset($skipMainTable)) || (! $skipMainTable) ){
	$actionDescription = 'Delete object data from the database';

	$tblName = $om->getTableForClass( $object->getClassName() );
	$whereString = "id = $id";

	$sql = "DELETE FROM {PRFX}$tblName WHERE $whereString";
	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}
	
/* delete meta */
if( $metaClass ){
	$sql = "DELETE FROM {PRFX}objectmeta WHERE obj_id = $id AND obj_class = \"$metaClass\"";

	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

/* delete meta as child */
$childMetaClass = '_' . strtolower($className);
$sql = "DELETE FROM {PRFX}objectmeta WHERE meta_name = \"$childMetaClass\" AND meta_value = \"$id\"";

$result = $ntsdb->runQuery( $sql );
if( $result ){
	$actionResult = 1;
	}
else {
	$actionResult = 0;
	$actionError = $ntsdb->getError();
	}

$childMetaClass = '_' . strtolower($className) . '_srlzd';
$sql = "DELETE FROM {PRFX}objectmeta WHERE meta_name = \"$childMetaClass\" AND meta_value = \"$id\"";

$result = $ntsdb->runQuery( $sql );
if( $result ){
	$actionResult = 1;
	}
else {
	$actionResult = 0;
	$actionError = $ntsdb->getError();
	}

?>