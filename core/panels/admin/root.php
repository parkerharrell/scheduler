<?php
ntsView::setTitle( M('Admin Area') );

/* this file is here to indicate that the menu hierarchy starts here */
/* check if a force url is required */
if( defined('NTS_FORCE_ADMIN_URL') ){
	$currentUrl = ntsLib::currentPageUrl();
	if( substr($currentUrl, 0, strlen(NTS_FORCE_ADMIN_URL)) != NTS_FORCE_ADMIN_URL ){
		// force redirect
		$paramsPart = ntsLib::urlParamsPart( $currentUrl );
		$forwardTo = NTS_FORCE_ADMIN_URL . $paramsPart;
		ntsView::redirect( $forwardTo );
		exit;
		}
	}

/* check permissions if admin */
if( ! ($NTS_CURRENT_USER->hasRole('admin')) ){
	$requestParams = $req->getGetParams();
	$returnPage = array(
		NTS_PARAM_PANEL		=> $NTS_CURRENT_PANEL,
		NTS_PARAM_ACTION	=> $requestParams,
		'params'	=> $requestParams,
		);
	$_SESSION['return_after_login'] = $returnPage;

	/* redirect to login page */
	$forwardTo = ntsLink::makeLink( 'anon/login', '', array('user' => 'admin') );
	ntsView::redirect( $forwardTo );
	exit;
	}

if( ! $NTS_CURRENT_PANEL )
	$NTS_CURRENT_PANEL = 'admin';

/* check if should run backup */
$conf =& ntsConf::getInstance();
$remindOfBackup = $conf->get('remindOfBackup');
$backupLastRun = $conf->get('backupLastRun'); 
$now = time();

if( $remindOfBackup ){
	if( (! $backupLastRun) || ( ($now - $backupLastRun) > $remindOfBackup ) ){
		if( $NTS_CURRENT_PANEL != 'admin/conf/backup' ){
			$announceText = M("It seems that you have not made a backup for some time, it's highly recommended to do it now");
			$announceText .= '<br><a href="' . ntsLink::makeLink('admin/conf/backup') . '">' . M('Download Backup') . '</a>';
			ntsView::setAdminAnnounce( $announceText, 'alert' );
			}
		}
	}
?>