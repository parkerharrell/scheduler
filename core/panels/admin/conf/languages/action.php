<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();
$lm =& ntsLanguageManager::getInstance();

switch( $action ){
	case 'activate':
		$oldLanguage = $lm->getCurrentLanguage();

		$newLanguage = $req->getParam( 'language' );
		$setting = $lm->languageActivate( $newLanguage );

		$newValue = $conf->set( 'languages', $setting );

		$sql = $conf->getSaveSql( 'languages', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::setAnnounce( M('Language') . ': ' . M('Activate') . ': ' . M('OK'), 'ok' );
		/* continue to import zip codes */
			$forwardTo = ntsLink::makeLink( 'admin/conf/languages' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
			}
		break;

	case 'disable':
		$disableLanguage = $req->getParam( 'language' );
		$setting = $lm->languageDisable( $disableLanguage );

		$newValue = $conf->set( 'languages', $setting );
		$sql = $conf->getSaveSql( 'languages', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::setAnnounce( 'Language Disabled', 'ok' );
		/* continue to import zip codes */
			$forwardTo = ntsLink::makeLink( 'admin/conf/languages' );
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