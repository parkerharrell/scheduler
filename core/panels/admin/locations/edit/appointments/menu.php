<?php
$title = M('Appointments');
$sequence = 2;

$locId = $req->getParam( '_id' );
$directLink = ntsLink::makeLink('admin/appointments/browse', '', array('location' => $locId) );
?>