<?php
$NTS_CURRENT_VERSION = $conf->get('currentVersion');
if( ! $NTS_CURRENT_VERSION ){
	$setupFile = NTS_BASE_DIR . '/setup/setup.php';
	if( ! file_exists($setupFile) )
		$setupFile = NTS_APP_DIR . '/setup/setup.php';
	require( $setupFile );
	exit;
	}

global $NTS_CURRENT_PANEL, $req, $NTS_REQUESTED_ACTION, $NTS_REQUESTED_PANEL, $NTS_CORE_DIRS;
$NTS_REQUESTED_PANEL = ( isset($_GET[NTS_PARAM_PANEL]) ) ? $_GET[NTS_PARAM_PANEL] : '';

/* IF PULL JAVASCRIPT OR CSS */
if( $NTS_REQUESTED_PANEL == 'system/pull' ){
	if( ob_get_length() ){
		ob_end_clean();
		}
	require( dirname(__FILE__) . '/pull.php' );
	exit;
	}

$thisPage = ntsLib::pureUrl( ntsLib::currentPageUrl() );
if( ! defined('NTS_ROOT_WEBPAGE') )
	define( 'NTS_ROOT_WEBPAGE',	$thisPage );

$thisWebDir = ntsLib::webDirName(NTS_ROOT_WEBPAGE);
define( 'NTS_ROOT_WEBDIR',	$thisWebDir );

if( ! defined('NTS_FRONTEND_WEBPAGE') ){
	define( 'NTS_FRONTEND_WEBPAGE',	NTS_ROOT_WEBPAGE );
	}

/* session start */
if( ! defined('NTS_SESSION_NAME') ){
	define( 'NTS_SESSION_NAME', 'ntssess_' . str_replace( ' ', '', strtolower(NTS_APP_TITLE)) );
	}

if( ! isset($_SESSION) ){
	session_name( NTS_SESSION_NAME );
	session_start();
	}

/* reminder code */
if( isset($_GET['nts-reminder']) ){
	require( dirname(__FILE__) . '/reminder.php' );
	exit;
	}

/* sos code */
if( isset($_GET['nts-send-sos']) ){
	require( dirname(__FILE__) . '/send-sos.php' );
	exit;
	}
if( isset($_GET['nts-sos']) ){
	$ntsSos = $_GET['nts-sos'];
	$sosSetting =  $conf->get( 'sosCode' );
	list( $sosCode, $sosCreated ) = explode( ':', $sosSetting );

	$now = time();
	if( $ntsSos == $sosCode  ){
		if( $now <= ($sosCreated + 24 * 60 * 60) ){
			ntsView::setAnnounce( 'SOS code ok', 'ok' );
			$_SESSION['nts_sos_user_id'] = -111;
			}
		else {
			ntsView::setAnnounce( 'SOS code expired', 'error' );
			if( isset($_SESSION['nts_sos_user_id']) )
				unset($_SESSION['nts_sos_user_id']);
			}
		}
	else {
		ntsView::setAnnounce( 'SOS code incorrect', 'error' );
		if( isset($_SESSION['nts_sos_user_id']) )
			unset($_SESSION['nts_sos_user_id']);
		}
	}

/* request */
$req = new ntsRequest;

/* sanitize */
$req->addSanitizer( 'service', '/^[\d-]*$/' );
$req->addSanitizer( 'resource', '/^[\d-]*$/' );
$req->addSanitizer( 'time', '/^[\d-]*$/' );
$req->addSanitizer( 'key', '/^[a-zA-Z\d_-]*$/' );

/* check if return url is given */
$returnAfterAction = $req->getParam( NTS_PARAM_RETURN );
if( $returnAfterAction ){
	if( isset($_SERVER['HTTP_REFERER']) )
		$_SESSION['return_after_action'] = $_SERVER['HTTP_REFERER'];
	}
/* where which panel is currently requested */
$NTS_REQUESTED_PANEL = $req->getParam( NTS_PARAM_PANEL );

/* language manager */
$lm =& ntsLanguageManager::getInstance(); 
$languageConf = $lm->getLanguageConf( $lm->getCurrentLanguage() );
if( isset($languageConf['charset']) ){
	if( ! headers_sent() )
		header( 'Content-Type: text/html; charset=' . $languageConf['charset'] );
	}

$timeUnit = $conf->get( 'timeUnit' );
define( 'NTS_TIME_UNIT', $timeUnit );

/* now check current user id and type */
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

if( ! defined('NTS_CURRENT_USERID') ){
	if( isset($_SESSION['nts_sos_user_id']) ){
		ini_set( 'display_errors', 'On' );
		error_reporting( E_ALL );
		$currentUserId = $_SESSION['nts_sos_user_id'];
		}
	else {
		$currentUserId = $integrator->currentUserId();
		}
	define( 'NTS_CURRENT_USERID', $currentUserId );
	}

global $NTS_CURRENT_USER;
if( ! ( isset($NTS_CURRENT_USER) && $NTS_CURRENT_USER ) ){
	$NTS_CURRENT_USER = new ntsUser();
	$NTS_CURRENT_USER->setId( NTS_CURRENT_USERID );
	}

/* default panel */
if( ! $NTS_REQUESTED_PANEL ){
	if( $NTS_CURRENT_USER->hasRole('admin') ){
		$NTS_REQUESTED_PANEL = 'admin';
		}
	else{
		$NTS_REQUESTED_PANEL = 'customer';
		}
	}

/* view mode */
$viewMode = $req->getParam( 'viewMode' );
if( $viewMode )
	ntsView::setPersistentParams( array('viewMode' => $viewMode), $req );

global $NTS_REQUESTED_ACTION;
$NTS_REQUESTED_ACTION = $req->getParam( NTS_PARAM_ACTION );
$action = $NTS_REQUESTED_ACTION;

/* check current version */
require( dirname(__FILE__) . '/version-check.php' );

/* HANDLE ACTION */
$saveRequestedPanel = $NTS_REQUESTED_PANEL;
while( $NTS_REQUESTED_PANEL ){
	$NTS_CURRENT_PANEL = $NTS_REQUESTED_PANEL;
	$NTS_REQUESTED_PANEL = '';
	require( dirname(__FILE__) . '/action.php' );
	}
$NTS_REQUESTED_PANEL = $saveRequestedPanel;

/* display files */
$displayFiles = array();
$display = $req->getParam( 'display' );

if( $display ){
	reset( $realPanelDirs );
	foreach( $realPanelDirs as $realPanelDir )
		$displayFiles[] = $realPanelDir . '/' . $display . '.php';
	}
reset( $realPanelDirs );
foreach( $realPanelDirs as $realPanelDir ){
	if( $NTS_REQUESTED_ACTION )
		$displayFiles[] = $realPanelDir . '/index-' . $NTS_REQUESTED_ACTION . '.php';
	$displayFiles[] = $realPanelDir . '/index.php'; 
	}

if( ! isset($NTS_VIEW['displayFile']) ){
	reset( $displayFiles );
	foreach( $displayFiles as $displayFile ){
		if( file_exists($displayFile) ){
			$NTS_VIEW['displayFile'] = $displayFile;
			break;
			}
		}
	}

/* IF PULL ICAL */
if( $NTS_REQUESTED_PANEL == 'system/appointments/export' ){
	if( ob_get_length() ){
		ob_end_clean();
		}
	require( dirname(__FILE__) . '/../views/export.php' );
	exit;
	}

/* if no display file exists then it is an error, redirect to home page */
if( ! file_exists($NTS_VIEW['displayFile']) ){
	/* continue to home page */
	$forwardTo = NTS_ROOT_WEBPAGE;
	ntsView::redirect( $forwardTo );
	exit;
	}
?>