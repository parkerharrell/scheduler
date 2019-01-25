<?php
require_once( dirname(__FILE__) . '/../common/grab.php' );

unset( $_SESSION['temp_customer_id'] );
$forwardTo = ntsLink::makeLink( '-current-/../dispatcher' );
ntsView::redirect( $forwardTo );
exit;
?>