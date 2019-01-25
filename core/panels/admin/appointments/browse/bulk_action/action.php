<?php
global $PANEL_PREFIX, $NTS_CURRENT_USER;
$PANEL_PREFIX = 'admin/appointments';

$forwardTo = $_SERVER['HTTP_REFERER'];
if( strpos($forwardTo, '?') === false ){
	$forwardTo = '?';
	}
else {
	preg_match( '/(\?.+)$/', $forwardTo, $ma );
	$forwardTo = $ma[1];
	}

$bulkAction = $req->getParam( 'bulk-action' );
$id = $req->getParam( 'id' );
if( ! $id ){
	/* redirect back to the referrer */
	ntsView::redirect( $forwardTo );
	exit;
	}

switch( $bulkAction ){
	case 'approve':
		$action = $bulkAction;
		$NTS_CURRENT_PANEL = $PANEL_PREFIX . '/edit/approve';

		$actionFile = dirname(__FILE__) . '/../../edit/approve/action.php';
		require( $actionFile );
		break;

	case 'reject':
//		$action = $bulkAction;
		$action = '';
		$NTS_CURRENT_PANEL = $PANEL_PREFIX . '/edit/reject';
		$actionFile = dirname(__FILE__) . '/../../edit/reject/action.php';
		require( $actionFile );
		break;

	case 'purge':
		$action = '';
		$NTS_CURRENT_PANEL = $PANEL_PREFIX . '/edit/purge';
		$actionFile = dirname(__FILE__) . '/../../edit/purge/action.php';
		require( $actionFile );
		break;

   ### Customized by RAH 5/17/11 - Added no show bulk action
   case 'noshow':
      $action = $bulkAction;
      $actionFile = dirname(__FILE__) . '/../../edit/noshow/action.php';
      require( $actionFile );
      break;

	default:
		/* redirect back to the referrer if no action */
		ntsView::redirect( $forwardTo );
		exit;
		break;
	}
?>