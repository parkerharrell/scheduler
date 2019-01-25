<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$resourceId = $req->getParam( '_res_id' );
$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );
$NTS_VIEW['resource'] = $resource;

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$params = array( 
	'resource_id'	=> $resourceId,
	);
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $params );

if( $NTS_VIEW['form']->validate($req) ){
	$formValues = $NTS_VIEW['form']->getValues();

	$t = new ntsTime();
	$startsAt = $t->timestampFromDbDate( $formValues['starts_at_date'] ) + $formValues['starts_at_time'];
	$endsAt = $t->timestampFromDbDate( $formValues['ends_at_date'] ) + $formValues['ends_at_time'];

	if( $resourceId == 'all' ){
		$sql = "SELECT id FROM {PRFX}resources";
		$result = $ntsdb->runQuery( $sql );
		while( $r = $result->fetch() ){
			$resourcesIds[] = $r['id'];
			}
		}
	else {
		$resourcesIds = array( $resourceId );
		}

	$values = array(
		'starts_at'		=> $startsAt,
		'ends_at'		=> $endsAt,
		'location_id'	=> 0,
		'description'	=> $formValues['description']
		);

	reset( $resourcesIds );
	foreach( $resourcesIds as $pid ){
		$values[ 'resource_id' ] = $pid;

	/* create timeoffs */
		$object = new ntsObject('timeoff');
		$object->setByArray( $values );

		$cm =& ntsCommandManager::getInstance();
		$cm->runCommand( $object, 'create' );
		}

	if( $cm->isOk() ){
		ntsView::addAnnounce( M('Timeoff') . ': ' . M('Created'), 'ok' );

	/* continue to customer edit */
		$forwardTo = ntsLink::makeLink( '-current-/..' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	else {
		$actionError = true;
		$errorString = $cm->printActionErrors();
		}
	}
else {
/* form not valid, continue to edit form */
	}

if( ! isset($NTS_VIEW['form']) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$params = array( 
		'resource_id'	=> $resourceId,
		);
	$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $params );
	}
?>