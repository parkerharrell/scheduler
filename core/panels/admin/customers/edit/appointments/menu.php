<?php
$title = M('Appointments');
$sequence = 20;

$clId = $req->getParam( '_id' );
$directLink = ntsLink::makeLink('admin/appointments/browse', '', array('customer' => $clId) );
?>