<?php
if( substr(str_replace('.', '', PHP_VERSION), 0, 2) < 52 ){
	echo "This software requires PHP version 5.2 at least, yours is " . PHP_VERSION;
	exit;
	}

ini_set( 'track_errors', 'On' );
define( 'NTS_APP_DIR', realpath(dirname(__FILE__) . '/../')  );
define( 'NTS_BASE_DIR', NTS_APP_DIR );
define( 'NTS_EXTENSIONS_DIR', realpath(dirname(__FILE__) . '/../../extensions')  );
include_once( NTS_APP_DIR . '/lib/ntsLib.php' );

global $NTS_EXECUTION_START;
$NTS_EXECUTION_START = ntsLib::utime();

/* database */
if( ! (defined('NTS_DB_HOST') && defined('NTS_DB_USER') && defined('NTS_DB_PASS') && defined('NTS_DB_NAME')) ){
	if( file_exists(NTS_APP_DIR . '/../db.php') )
		include_once( NTS_APP_DIR . '/../db.php' );
	else {
		echo "<p><b>db.php</b> file doesn't exist! Please rename the sample <b>db.rename_it.php</b> to <b>db.php</b>, then edit your MySQL database information there.";
		exit;
		}
	}

/* load base code files */
include_once( NTS_BASE_DIR . '/lib/ntsRequest.php' );
include_once( NTS_BASE_DIR . '/lib/ntsView.php' );
include_once( NTS_BASE_DIR . '/lib/ntsObject.php' );
include_once( NTS_BASE_DIR . '/lib/ntsMysqlWrapper.php' );
include_once( NTS_BASE_DIR . '/lib/ntsUser.php' );
include_once( NTS_BASE_DIR . '/lib/ntsCommandManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsLanguageManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsPaymentGatewaysManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsPluginManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsEmailTemplateManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsUserIntegratorFactory.php' );
include_once( NTS_BASE_DIR . '/lib/ntsApplication.php' );
include_once( NTS_BASE_DIR . '/lib/ntsAdminPermissionsManager.php' );
include_once( NTS_BASE_DIR . '/lib/form/ntsFormFactory.php' );
include_once( NTS_BASE_DIR . '/lib/form/ntsValidatorManager.php' );
include_once( NTS_BASE_DIR . '/lib/ntsConf.php' );
include_once( NTS_BASE_DIR . '/lib/datetime/ntsTime.php' );

include_once( NTS_APP_DIR . '/helpers/currency.php' );
include_once( NTS_APP_DIR . '/helpers/timeManager.php' );

$versionFile1 = NTS_APP_DIR . '/version.php';
$versionFile2 = NTS_BASE_DIR . '/version.php';
if( file_exists($versionFile1) )
	include_once($versionFile1);
else
	include_once($versionFile2);

$objectMapperFile1 = NTS_APP_DIR . '/model/objectMapper.php';
$objectMapperFile2 = NTS_BASE_DIR . '/model/objectMapper.php';
if( file_exists($objectMapperFile1) )
	include_once($objectMapperFile1);
else
	include_once($objectMapperFile2);

/* define param names */
define( 'NTS_PARAM_ACTION', 'nts-action' );
define( 'NTS_PARAM_PANEL', 'nts-panel' );
define( 'NTS_PARAM_RETURN', 'nts-return' );

$ntsdb =& dbWrapper::getInstance();

global $NTS_CURRENT_VERSION, $NTS_CURRENT_VERSION_NUMBER;

$conf =& ntsConf::getInstance();

/* some essential configs */
/* if registration enabled */
$enableRegistration = $conf->get('enableRegistration');
define( 'NTS_ENABLE_REGISTRATION', $enableRegistration );


/*
1, 'Allow To Set Own Timezone'
0, 'Only View The Timezone'
-1, 'Do Not Show The Timezone'
*/
$enableTimezones = $conf->get('enableTimezones');
define( 'NTS_ENABLE_TIMEZONES', $enableTimezones );

$allowNoEmail = $conf->get('allowNoEmail');
define( 'NTS_ALLOW_NO_EMAIL', $allowNoEmail );

define( 'NTS_TIME_FORMAT',		$conf->get('timeFormat') );
define( 'NTS_DATE_FORMAT', 		$conf->get('dateFormat') );
define( 'NTS_COMPANY_TIMEZONE', $conf->get('companyTimezone') );
date_default_timezone_set( NTS_COMPANY_TIMEZONE );

/* if email as username */
if( defined('NTS_NEED_MEGA') && NTS_NEED_MEGA )
	$emailAsUsername = true;
else
	$emailAsUsername = defined('NTS_REMOTE_INTEGRATION') ? 0 : $conf->get('emailAsUsername');
define( 'NTS_EMAIL_AS_USERNAME', $emailAsUsername );

/* if duplicate emails allowed */
$allowDuplicateEmails = defined('NTS_REMOTE_INTEGRATION') ? 0 : $conf->get('allowDuplicateEmails');
define( 'NTS_ALLOW_DUPLICATE_EMAILS', $allowDuplicateEmails );

$NTS_CURRENT_VERSION = $conf->get('currentVersion');
$NTS_CURRENT_VERSION_NUMBER = 0;

if( $NTS_CURRENT_VERSION ){
	list( $v1, $v2, $v3 ) = explode( '.', $NTS_CURRENT_VERSION );
	$NTS_CURRENT_VERSION_NUMBER = $v1 . $v2 . sprintf('%02d', $v3 );
	}

if( $NTS_CURRENT_VERSION_NUMBER >= 4500 ){
	/* check how many locations do we have */
	$sql =<<<EOT
	SELECT 
		id
	FROM 
		{PRFX}locations
EOT;
	$result = $ntsdb->runQuery( $sql );
	$locations = array();
	while( $e = $result->fetch() ){
		$locations[] = $e['id'];
		}

	$locationsCount = count( $locations );
	if( $locationsCount == 1 ){
		define( 'NTS_SINGLE_LOCATION', $locations[0] );
		}
	else {
		define( 'NTS_SINGLE_LOCATION', 0 );
		}

	/* check how many resources do we have */
	$sql =<<<EOT
	SELECT 
		id
	FROM 
		{PRFX}resources
EOT;
	$result = $ntsdb->runQuery( $sql );
	$resources = array();
	while( $e = $result->fetch() ){
		$resources[] = $e['id'];
		}

	$resourcesCount = count( $resources );
	if( $resourcesCount == 1 ){
		define( 'NTS_SINGLE_RESOURCE', $resources[0] );
		}
	else {
		define( 'NTS_SINGLE_RESOURCE', 0 );
		}
	}

/* run mods init scripts */
$plm =& ntsPluginManager::getInstance();
$activePlugins = $plm->getActivePlugins();
reset( $activePlugins );
foreach( $activePlugins as $plg ){
	$plgInitFile = $plm->getPluginFolder( $plg ) . '/init.php';
	if( file_exists($plgInitFile) )
		require( $plgInitFile );
	}

/* init folders */
global $NTS_CORE_DIRS;
$NTS_CORE_DIRS = array();
/* plugins */
reset( $activePlugins );
foreach( $activePlugins as $plg ){
	$NTS_CORE_DIRS[] = $plm->getPluginFolder( $plg );
	}
/* normal */
$NTS_CORE_DIRS[] = NTS_APP_DIR;
/* base dir */
if( NTS_BASE_DIR != NTS_APP_DIR )
	$NTS_CORE_DIRS[] = NTS_BASE_DIR;

/* delete old "ghost" appointments */
if( $NTS_CURRENT_VERSION_NUMBER >= 4300 ){
	$now = time();
	$howOld = $now - 1 * 60 * 60; // 1 hour

	$sql =<<<EOT
	SELECT 
		id
	FROM 
		{PRFX}appointments
	WHERE
		is_ghost = 1 AND
		ghost_last_access < $howOld
EOT;
	$result = $ntsdb->runQuery( $sql );
	$appIds = array();
	while( $e = $result->fetch() )
		$appIds[] = $e['id'];

	$cm =& ntsCommandManager::getInstance();
	reset( $appIds );
	foreach( $appIds as $aid ){
		$appointment = ntsObjectFactory::get( 'appointment' );
		$appointment->setId( $aid, false );
		$cm->runCommand( $appointment, 'delete' );
		}
	}
?>