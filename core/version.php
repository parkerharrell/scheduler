<?php
define( 'NTS_APP_VERSION', '4.5.23' );
if( file_exists(dirname(__FILE__) . '/panels/admin/services/create') )
	define( 'NTS_APP_LITE', false );
else
	define( 'NTS_APP_LITE', true );

/* check which we are using */
if( ! file_exists(dirname(__FILE__) . '/panels/admin/services/create') ){
	define( 'NTS_APP_LEVEL', 'lite' );
	}
elseif( ! file_exists(dirname(__FILE__) . '/panels/admin/staff') ){
	define( 'NTS_APP_LEVEL', 'solo' );
	}
else {
	define( 'NTS_APP_LEVEL', 'pro' );
	}

define( 'NTS_APP_TITLE', 'Appointment Scheduler' . ' ' . ucfirst( NTS_APP_LEVEL ) );
define( 'NTS_APP_URL', '' );

define( 'NTS_DEFAULT_USER_ROLE', 'customer' );

global $NTS_SKIP_PANELS;
$NTS_SKIP_PANELS = array(
	'superadmin',
	);
?>
