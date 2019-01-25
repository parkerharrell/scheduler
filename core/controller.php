<?php
include_once( dirname(__FILE__) . '/model/init.php' );
require( dirname(__FILE__) . '/panels/init.php' );

// intercept ajax or inline views
$viewMode = isset($_REQUEST['viewMode']) ? $_REQUEST['viewMode'] : '';
global $NTS_CURRENT_PANEL;
if( (! $viewMode) && (strpos($NTS_CURRENT_PANEL, 'ajax') !== false) ){
	$viewMode = 'ajax';
	}
if( $viewMode == 'ajax' ){
	require( dirname(__FILE__) . '/views/ajax.php' );
	exit;
	}
if( $viewMode == 'inline' ){
	require( dirname(__FILE__) . '/views/inline.php' );
	exit;
	}
?>