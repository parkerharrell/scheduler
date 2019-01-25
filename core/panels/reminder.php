<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();
$conf =& ntsConf::getInstance();
$remindBefore = $conf->get( 'remindBefore' );

$now = time();

/* find apps that should be reminded at this run */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	is_ghost <> 1 AND
	cancelled <> 1 AND
	need_reminder <> 0 AND 
	no_show <> 0 AND 
	(starts_at - $remindBefore) <= $now AND
	starts_at > $now
ORDER BY
	starts_at
EOT;

$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$a = ntsObjectFactory::get( 'appointment' );
		$a->setId( $e['id'] );
		$cm->runCommand( $a, 'remind' );
		}
	}
?>