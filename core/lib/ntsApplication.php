<?php
class ntsApplication {
	function deleteMeta( $object, $metaName, $metaValue = '' ){
		$ntsdb =& dbWrapper::getInstance();
		$id = $object->getId();
		$metaClass = $object->getMetaClass();
		if( $metaClass ){
			if( $metaValue )
				$sql = "DELETE FROM {PRFX}objectmeta WHERE obj_id = $id AND obj_class = \"$metaClass\" AND meta_name = \"$metaName\" AND meta_value = \"$metaValue\"";
			else
				$sql = "DELETE FROM {PRFX}objectmeta WHERE obj_id = $id AND obj_class = \"$metaClass\" AND meta_name = \"$metaName\"";
			$result = $ntsdb->runQuery( $sql );
			}
		return $result;
		}

	// Singleton stuff
	function &getInstance(){
		return ntsLib::singletonFunction( 'ntsApplication' );
		}
	}
?>