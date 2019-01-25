<?php
global $NTS_READ_ONLY;
if( ! $NTS_READ_ONLY ){
	$title = M('Payments');
	$sequence = 2;
	$ajax = true;
	}
?>