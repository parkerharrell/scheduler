<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();
$className = $object->getClassName();

if( ! ( isset($objectInfo) && isset($metaInfo) ) ){
	list( $objectInfo, $metaInfo ) = $object->getByArray( true, true );
	}

$metaClass = $object->getMetaClass();
$id = $object->getId();

/* do queries */
/* main table */
if( $objectInfo && (! ( isset($skipMainTable) && $skipMainTable )) ){
	$actionDescription = 'Store object data in database';
	$tblName = $om->getTableForClass( $className );

	$propsAndValues = $ntsdb->prepareUpdateStatement( $objectInfo );

	$whereString = "id = $id";

	$sql =<<<EOT
UPDATE {PRFX}$tblName 
SET $propsAndValues
WHERE $whereString
EOT;

	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

/* meta properties */
if( $metaClass && $metaInfo ){
	/* get current meta properties */
	$currentMetas = array();
	$sql =<<<EOT
SELECT 
	meta_name, meta_value, meta_data, id
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $id AND 
	obj_class = "$className"
EOT;

	$result = $ntsdb->runQuery( $sql );
	while( $u = $result->fetch() ){
		if( isset($metaInfo[$u['meta_name']]) )
			$currentMetas[] = $u;
		}

	/* get new meta properties */
	$newMetas = $om->prepareMeta( $id, $metaClass, $metaInfo, false );

	$toAdd = array();
	$toDelete = array();
	$toUpdate = array();

	/* skip ones that already exist */
	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] ) && 
				( $newMetas[$i]['meta_value']	== $currentMetas[$j]['meta_value'] ) && 
				( $newMetas[$i]['meta_data']	== $currentMetas[$j]['meta_data'] )
				){
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
//echo "<h1>SKIP $i to $j</h1>";
				break;
				}
			}
		}

	/* ok, which ones we can update */
	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] ) && 
				( $newMetas[$i]['meta_value']	== $currentMetas[$j]['meta_value'] )
				){
				$updateArray = array(
					'meta_data' => $newMetas[$i]['meta_data'],
					);
				$toUpdate[] = array( $currentMetas[$j]['id'], $updateArray );
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
				break;
				}
			}
		}

	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] )
				){
				$updateArray = array(
					'meta_value' => $newMetas[$i]['meta_value'],
					'meta_data' => $newMetas[$i]['meta_data'],
					);
				$toUpdate[] = array( $currentMetas[$j]['id'], $updateArray );
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
				break;
				}
			}
		}

	/* to update */
	reset( $toUpdate );
	foreach( $toUpdate as $ua ){
		$metaId = $ua[0];
		unset( $ua[1]['meta_name'] );
		$propsAndValues = $ntsdb->prepareUpdateStatement( $ua[1] );
		$sql =<<<EOT
UPDATE 
{PRFX}objectmeta 
SET
$propsAndValues
WHERE
id = $metaId
EOT;

		$result = $ntsdb->runQuery( $sql );
		}

	/* to add */
	reset( $newMetas );
	foreach( $newMetas as $ma ){
		$ma['obj_id'] = $id;
		$ma['obj_class'] = $metaClass;
		$propsAndValues = $ntsdb->prepareInsertStatement( $ma );
		$sql =<<<EOT
INSERT INTO
{PRFX}objectmeta 
$propsAndValues
EOT;

		$result = $ntsdb->runQuery( $sql );
		}

	/* to delete */
	reset( $currentMetas );
	foreach( $currentMetas as $ma ){
		$id2delete = $ma['id'];
		$sql =<<<EOT
DELETE FROM {PRFX}objectmeta WHERE id = $id2delete
EOT;

		$result = $ntsdb->runQuery( $sql );
		}
	}
?>