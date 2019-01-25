<?php
$title = M('Search');
$sequence = 4;

global $NTS_VIEW;
if( isset($NTS_VIEW['fix']) && ($NTS_VIEW['fix'] == 'resource') ){
	$params = array(
		'resource'	=> $NTS_VIEW['fixId'],
		);
	}
?>