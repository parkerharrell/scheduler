<?php
$ntsdb =& dbWrapper::getInstance();

$fldName = $object->getProp( 'name' );
$formId = $object->getProp( 'form_id' );

/* ALTER TABLE IF THIS IS NOT A BUILT-IN FIELD */
$className = '';
$sql = "SELECT class FROM {PRFX}forms WHERE id = $formId";
$result = $ntsdb->runQuery( $sql );
$o = $result->fetch();
if( $o ){
	$className = $o['class'];
	}

if( $className ){
	if( $className != 'appoinment' ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$biltInFields = array_keys( $coreProps );

		$builtIn = in_array($fldName, $biltInFields) ? true : false;

		if( ! $builtIn ){
			$sql =<<<EOT
DELETE FROM 
		{PRFX}objectmeta
WHERE
		meta_name = "$fldName" AND obj_class = "$className"
EOT;
			$result = $ntsdb->runQuery( $sql );
			}
		}
	}
$actionResult = 1;
?>