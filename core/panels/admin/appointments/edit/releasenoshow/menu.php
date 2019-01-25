<?php
global $NTS_READ_ONLY;
$id = $req->getParam( '_id' );
if( ! is_array($id) ){
	if( ! $NTS_READ_ONLY ){
		$object = ntsObjectFactory::get( 'appointment' );
		$object->setId( $id );

		if( $object->getProp('no_show') ){
			$title = M('Release No Show');
			$sequence = 5;
			}
		}
	}
?>