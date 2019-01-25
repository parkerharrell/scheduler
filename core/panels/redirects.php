<?php
global $NTS_PANEL_REDIRECTS;
/* array( to, noForward ) */
$NTS_PANEL_REDIRECTS = array(
	'admin'	=> array( 'admin/appointments', false ),
	'customer'	=> array( 'customer/appointments/request/dispatcher', true ),
	);
?>