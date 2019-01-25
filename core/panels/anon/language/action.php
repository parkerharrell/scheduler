<?php
$lng = $req->getParam( 'lang' );
$lm =& ntsLanguageManager::getInstance();
$lm->setCurrentLanguage( $lng );

/* redirect back to the referrer */
$forwardTo = $_SERVER['HTTP_REFERER'];
ntsView::redirect( $forwardTo );
exit;
?>