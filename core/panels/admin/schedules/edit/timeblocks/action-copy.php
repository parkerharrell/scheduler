<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$scheduleId = $req->getParam( '_id' );
$dayIndex = $req->getParam( 'day_from' );

$ff =& ntsFormFactory::getInstance();
$duplicateFormFile = dirname( __FILE__ ) . '/duplicateForm';

$params = array(
	'day_from'	=> $dayIndex,
	);
$form =& $ff->makeForm( $duplicateFormFile, $params );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$dayTo = $formValues['day_to'];

	/* this day timeblocks */
	$sql =<<<EOT
	SELECT
		id
	FROM 
		{PRFX}timeblocks
	WHERE
		schedule_id = $scheduleId AND
		applied_on = $dayIndex
EOT;
	$timeblocksInfo = array(); 
	$result = $ntsdb->runQuery( $sql );
	while( $s = $result->fetch() ){
		$t = new ntsObject( 'timeblock' );
		$t->setId( $s['id'] );
		$timeblocksInfo[] = $t->getByArray();
		}

	/* delete other day timeblocks */
	$appliedOnWhere = ( $dayTo > -1 ) ? "applied_on = $dayTo" : "applied_on <> $dayIndex";
	$sql =<<<EOT
	SELECT
		id
	FROM 
		{PRFX}timeblocks
	WHERE
		schedule_id = $scheduleId AND
		$appliedOnWhere
EOT;
	$result = $ntsdb->runQuery( $sql );
	while( $s = $result->fetch() ){
		$t = new ntsObject( 'timeblock' );
		$t->setId( $s['id'] );
		$cm->runCommand( $t, 'delete' );
		}

	for( $day = 0; $day <= 6; $day++ ){
		if( $day == $dayIndex )
			continue;
		if( ($dayTo > -1) && ($dayTo != $day) )
			continue;
		reset( $timeblocksInfo );
		foreach( $timeblocksInfo as $tbi ){
			unset( $tbi['id'] );
			$tbi['applied_on'] = $day;
			$object = new ntsObject( 'timeblock' );
			$object->setByArray( $tbi );
			$cm->runCommand( $object, 'create' );
			}
		}
	ntsView::setAnnounce( M('Time Slots') . ': ' . M('Duplicate') . ': ' . M('OK'), 'ok' );

	/* continue to timeblocks list */
	$forwardTo = ntsLink::makeLink( '-current-/..', '', array('id' => $scheduleId) );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	}
?>