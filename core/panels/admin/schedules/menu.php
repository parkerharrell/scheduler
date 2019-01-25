<?php
global $NTS_CURRENT_USER;
$resourceSchedules = $NTS_CURRENT_USER->getProp( '_resource_schedules' );

if( $resourceSchedules ){
	$title = M('Schedules');
	$sequence = 70;
	}
?>