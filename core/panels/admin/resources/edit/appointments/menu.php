<?php
$title = M('Appointments');
$sequence = 40;

$resId = $req->getParam( '_id' );
$directLink = ntsLink::makeLink('admin/appointments/browse', '', array('resource' => $resId) );
?>