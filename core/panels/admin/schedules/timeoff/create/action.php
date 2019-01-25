<?php
global $NTS_VIEW;
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$resourceId = $req->getParam( '_res_id' );
if( ! $resourceId ){
	if( count($NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) > 1 ){
		$NTS_VIEW['displayFile'] = dirname(__FILE__) . '/chooseResource.php';
		}
	else {
		$params = array(
			'_res_id'	=> $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'][0],
			);
		$forwardTo = ntsLink::makeLink( '-current-', '', $params );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}

$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );
$NTS_VIEW['resource'] = $resource;

$from = $req->getParam( 'from' );

if( ! isset($NTS_VIEW['form']) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$params = array( 
		'resource_id'	=> $resourceId,
		'from'			=> $from,
		);
	$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $params );
	}
?>