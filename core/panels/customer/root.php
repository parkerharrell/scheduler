<?php
/* this file is here to indicate that the menu hierarchy starts here */
$conf =& ntsConf::getInstance();
$userLoginRequired = $conf->get('userLoginRequired');

$firstTimeSplash = $conf->get('firstTimeSplash');
if( $firstTimeSplash && (! isset($_COOKIE['ntsFirstTimeSplash'])) && ($NTS_CURRENT_PANEL != 'customer/splash') ){
	$forwardTo = ntsLink::makeLink( 'customer/splash' );
	ntsView::redirect( $forwardTo );
	exit;
	}

/* also check permissions and set default panel */
if( (! NTS_CURRENT_USERID) && $userLoginRequired && ($NTS_CURRENT_PANEL != 'customer/splash') ){
	if( $NTS_CURRENT_PANEL != 'customer' ){
		$requestParams = $req->getGetParams();
		$returnPage = array(
			NTS_PARAM_PANEL		=> $NTS_CURRENT_PANEL,
			NTS_PARAM_ACTION	=> $requestParams,
			'params'	=> $requestParams,
			);
		$_SESSION['return_after_login'] = $returnPage;
		}

	/* redirect to login page */
	$NTS_CURRENT_PANEL = 'anon/login';
	}
if( ! $NTS_CURRENT_PANEL )
	$NTS_CURRENT_PANEL = 'customer';
?>