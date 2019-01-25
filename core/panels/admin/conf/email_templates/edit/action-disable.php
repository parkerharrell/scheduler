<?php
$conf =& ntsConf::getInstance();

$key = $req->getParam( 'key' );
$currentlyDisabled = $conf->get( 'disabledNotifications' );
$currentlyDisabled[] = $key;
$conf->save( 'disabledNotifications', $currentlyDisabled );

ntsView::setAnnounce( M('Notification') . ': ' . M('Disable') . ': ' . M('OK'), 'ok' );

/* continue  */
$forwardTo = ntsLink::makeLink( '-current-/..' );
ntsView::redirect( $forwardTo );
exit;
?>