<?php
$resId = $req->getParam( '_id' );
if( in_array($resId, $NTS_VIEW['viewedResourcesIds']) ){
	$title = M('Timeoff');
	$sequence = 30;
	$directLink = ntsLink::makeLink('admin/schedules/timeoff', '', array('_res_id' => $resId) );
	}
?>