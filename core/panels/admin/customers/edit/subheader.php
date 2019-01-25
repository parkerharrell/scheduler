<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );

if( ! is_array($id) ){
	ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/customers/edit' );

	$object = new ntsUser();
	$object->setId( $id );

	$title = '<span class="subtitle">' . M('Customer') . ':</span> ' . ntsView::objectTitle( $object );
	}
?>