<?php
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

// save time when run this backup
$now = time();
$newValue = $conf->set( 'backupLastRun', $now );
$sql = $conf->getSaveSql( 'backupLastRun', $newValue );
$result = $ntsdb->runQuery( $sql );

$out = '';
$tables = $ntsdb->getTablesInDatabase();
reset( $tables );
foreach( $tables as $t ){
	// skip users table, let the user integrator manage
	if( $t == 'users' )
		continue;
	$out .= $ntsdb->dumpTable( $t, true ) . "\n";
	}
// add users
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$out .= $integrator->dumpUsers() . "\n";

$t = new ntsTime;
$fileName = 'AppointmentsBackup-' . $t->formatDate_Db() . '-' . $t->formatTime_Db(). '.sql';
ntsLib::startPushDownloadContent( $fileName );
echo $out;
exit;
?>