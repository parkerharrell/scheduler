<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();
$className = $object->getClassName();

if( ! ( isset($objectInfo) && isset($metaInfo) ) ){
	list( $objectInfo, $metaInfo ) = $object->getByArray( true );
	}
$metaClass = $object->getMetaClass();

/* do queries */
/* main table */
$tblName = $om->getTableForClass( $className );
if( $objectInfo && (! ( isset($skipMainTable) && $skipMainTable )) ){
	$actionDescription = 'Store object data in database';

	/* check if show order needed */
	if( $om->isPropRegistered($className, 'show_order') ){
		$setShowOrder = $object->getProp( 'show_order' );
		if( ! $setShowOrder ){

			$sql =<<<EOT
			SELECT 
				MAX(show_order) AS max_show_order
			FROM
				{PRFX}$tblName
EOT;
			$result = $ntsdb->runQuery( $sql );
			$max = $result->fetch();

			$showOrder = $max['max_show_order'] + 1;
			$object->setProp( 'show_order', $showOrder );
			$objectInfo[ 'show_order' ] = $showOrder;
			}
		}

	/* already id? */
	$objectId = $object->getId();
	if( $objectId && (! isset($objectInfo['id'])) ){
		$objectInfo['id'] = $objectId;
		}

	$propsAndValues = $ntsdb->prepareInsertStatement( $objectInfo );

	$sql =<<<EOT
INSERT INTO {PRFX}$tblName 
$propsAndValues
EOT;

	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$actionResult = 1;
		$newId = $ntsdb->getInsertId();
		$object->setId( $newId, false );
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

/* meta properties */
if( $metaClass && $metaInfo ){
	$metas = $om->prepareMeta( $newId, $metaClass, $metaInfo );
	reset( $metas );
	foreach( $metas as $ma ){
		$propsAndValues = $ntsdb->prepareInsertStatement( $ma );
		$sql =<<<EOT
INSERT INTO {PRFX}objectmeta 
$propsAndValues
EOT;
		$result = $ntsdb->runQuery( $sql );
		}
	}
$object->resetUpdatedProps();
?>