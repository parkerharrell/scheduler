<?php
$title = M('Create Appointment');
$sequence = 30;

$clId = $req->getParam( '_id' );
$directLink = ntsLink::makeLink( 'admin/appointments/manage', '', array('customer' => $clId, 'viewPeriod' => 'week') );
?>