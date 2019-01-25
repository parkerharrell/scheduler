<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/locations/edit' );

$object = new ntsObject( 'location' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Location') . ':</span> ' . ntsView::objectTitle($object);
?>