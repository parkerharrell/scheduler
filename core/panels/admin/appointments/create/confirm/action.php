<?php
$ntsdb =& dbWrapper::getInstance();
require( dirname(__FILE__) . '/../common/grab.php' );

if( ! $NTS_VIEW['CURRENT_REQUEST']['customer'] ){
	// forward to customer selection
	$forwardTo = ntsLink::makeLink( '-current-/../select_customer' );
	ntsView::redirect( $forwardTo );
	exit;
	}
?>