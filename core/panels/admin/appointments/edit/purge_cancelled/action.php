<?php
$cm =& ntsCommandManager::getInstance();
$ntsdb =& dbWrapper::getInstance();

$id = array();
$sql =<<<EOT
SELECT
	{PRFX}appointments.id
FROM
	{PRFX}appointments
WHERE
	cancelled = 1
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$id[] = $e['id'];
		}
	}

$resultCount = 0;
$failedCount = 0;
reset( $id );
foreach( $id as $i ){
	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $i );

	$cm->runCommand( $object, 'delete' );

	if( $cm->isOk() ){
		$resultCount++;
		}
	else {
		$failedCount++;
		}
	}

if( $resultCount ){
	if( $resultCount > 1 )
		$msg = $resultCount . ' ' . M('Appointments') . ': ' . M('Purged');
	else
		$msg = M('Appointment') . ': ' . M('Purged');
	ntsView::addAnnounce( $msg, 'ok' );
	}

/* continue to the list with anouncement */
if( ! isset($forwardTo) ){
	if( isset($_SESSION['return_after_action']) && $_SESSION['return_after_action'] ){
		$forwardTo = $_SESSION['return_after_action'];
		unset( $_SESSION['return_after_action'] );
		}
	else {
		$forwardTo = ntsLink::makeLink( '-current-/../..' );
		}
	}

ntsView::redirect( $forwardTo );
exit;
?>