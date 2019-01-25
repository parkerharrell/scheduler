<?php
$req = new ntsRequest();
$id = $req->getParam( '_id' );
ntsView::setPersistentParams( array('_id' => $id), $req, 'admin/forms/appointments/edit' );

$object = new ntsObject( 'form' );
$object->setId( $id );

$title = '<span class="subtitle">' . M('Appointment Form') . ':</span> ' . $object->getProp( 'title' );
?>