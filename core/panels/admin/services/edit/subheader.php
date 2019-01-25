<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/services/edit' );

$object = new ntsObject( 'service' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Service') . ':</span> ' . ntsView::objectTitle($object);
?>