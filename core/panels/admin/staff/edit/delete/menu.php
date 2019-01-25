<?php
$req = new ntsRequest();
$id = $req->getParam( '_id' );
if( $id != NTS_CURRENT_USERID ){
	$title = M('Delete');
	$sequence = 50;
	}
?>