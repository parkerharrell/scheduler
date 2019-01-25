<?php
global $NTS_VIEW, $NTS_CURRENT_PANEL, $NTS_REQUESTED_PANEL, $NTS_CURRENT_USER, $req;
$viewMode = isset($_REQUEST['viewMode']) ? $_REQUEST['viewMode'] : '';
$display = isset($_REQUEST['display']) ? $_REQUEST['display'] : '';
$action = isset($_REQUEST[NTS_PARAM_ACTION]) ? $_REQUEST[NTS_PARAM_ACTION] : '';

switch( $action ){
	case 'export':
		require( dirname(__FILE__) . '/views/export.php' );
		break;
	default:
		switch( $display ){
			case 'print':
				require( dirname(__FILE__) . '/views/print.php' );
				break;
			default:
				require( dirname(__FILE__) . '/views/normal.php' );
				break;
			}
		break;
	}
?>
