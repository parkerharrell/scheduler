<?php
$conf =& ntsConf::getInstance();

$key = $req->getParam( 'key' );
$currentlyDisabled = $conf->get( 'disabledNotifications' );
$newDisabled = array();
reset( $currentlyDisabled );
foreach( $currentlyDisabled as $d ){
	if( $d == $key )
		continue;
	$newDisabled[] = $d;
	}

$conf->save( 'disabledNotifications', $newDisabled );

ntsView::setAnnounce( M('Notification') . ': ' . M('Activate') . ': ' . M('OK'), 'ok' );

/* continue  */
$forwardTo = ntsLink::makeLink( '-current-/..' );
ntsView::redirect( $forwardTo );
exit;
?>