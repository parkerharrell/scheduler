<?php
$resId = $req->getParam( '_id' );
if( in_array($resId, $NTS_VIEW['viewedResourcesIds']) ){
	$title = M('Schedules');
	$sequence = 20;
	$directLink = ntsLink::makeLink('admin/schedules', '', array('_res_id' => $resId) );
	}
?>