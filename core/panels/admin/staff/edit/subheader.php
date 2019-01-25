<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/staff/edit' );

$object = new ntsUser();
$object->setId( $id );

$title = '<span class="subtitle">' . M('Administrative User') . ':</span> ' . ntsView::objectTitle( $object );
?>