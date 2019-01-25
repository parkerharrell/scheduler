<?php
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
	case 'delete':
		$NTS_CURRENT_PANEL = 'admin/customers/edit/delete';

		$actionFile = dirname(__FILE__) . '/../edit/delete/action.php';
		require( $actionFile );
		break;

	case 'activate':
	case 'suspend':
		$action = $bulkAction;
		$NTS_CURRENT_PANEL = 'admin/customers/edit/edit';

		$actionFile = dirname(__FILE__) . '/../edit/edit/action.php';
		require( $actionFile );
		break;

	default:
		/* redirect back to the referrer if no action */
		ntsView::redirect( $forwardTo );
		exit;
		break;
	}
?>