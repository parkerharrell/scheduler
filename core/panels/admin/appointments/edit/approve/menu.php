<?php
global $NTS_READ_ONLY;
$id = $req->getParam( '_id' );
if( ! is_array($id) ){
	if( ! $NTS_READ_ONLY ){
		$object = ntsObjectFactory::get( 'appointment' );
		$object->setId( $id );

		if( (! $object->getProp('cancelled')) && (! $object->getProp('approved')) ){
			$title = M('Approve');
			$sequence = 4;
			}
		}
	}
?>