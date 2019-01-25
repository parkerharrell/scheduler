<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/services/cats/edit' );

$object = new ntsObject( 'service_cat' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Category') . ':</span> ' . ntsView::objectTitle($object);
?>