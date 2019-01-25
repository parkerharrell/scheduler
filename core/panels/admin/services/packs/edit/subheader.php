<?php
$ntsdb =& dbWrapper::getInstance();
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/services/packs/edit' );

$object = new ntsObject( 'pack' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Appointment Pack') . ':</span> ' . $object->getProp( 'title' );
?>