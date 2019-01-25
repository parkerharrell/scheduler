<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

switch( $action ){
	case 'activate':
		$newTheme = $req->getParam( 'theme' );

		$newValue = $conf->set( 'theme', $newTheme );
		$sql = $conf->getSaveSql( 'theme', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::setAnnounce( M('Theme') . ': ' . M('Activate') . ': ' . M('OK'), 'ok' );
		/* continue */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
			}
		break;

	default:
		break;
	}
?>