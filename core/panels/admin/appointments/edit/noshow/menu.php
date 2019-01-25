<?php
global $NTS_READ_ONLY;
$id = $req->getParam( '_id' );
if( ! is_array($id) ){
	if( ! $NTS_READ_ONLY ){
		$object = ntsObjectFactory::get( 'appointment' );
		$object->setId( $id );

		if( (! $object->getProp('cancelled')) && (! $object->getProp('no_show')) ){
			$title = M('No Show');
			$sequence = 5;
			$ajax = true;
			}
		}
	}
?>