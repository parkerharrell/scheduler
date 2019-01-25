<?php
global $NTS_VIEW;
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
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

$formParams = array();

/* copy from */	
$copyFromId = $req->getParam( '_copy_from' );
$NTS_VIEW['copyFrom'] = 0;
if( $copyFromId ){
	$copyFrom = ntsObjectFactory::get( 'schedule' ); 
	$copyFrom->setId( $copyFromId );
	$formParams = $copyFrom->getByArray();
	unset( $formParams['_res_id'] );
	unset( $formParams['id'] );
	$NTS_VIEW['copyFrom'] = $copyFromId;
	}
$formParams['_res_id'] = $resourceId;

$resource = ntsObjectFactory::get( 'resource' );
$resource->setId( $resourceId );
$NTS_VIEW['resourceInfo'] = $resource->getByArray();

switch( $action ){
	case 'create':
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $formParams );

		if( $form->validate($req) ){
			$formValues = $form->getValues();
			$formValues = array_merge( $formParams, $formValues );
			$formValues['resource_id'] = $resourceId;

			$object = new ntsObject('schedule');
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				$id = $object->getId();
			
				if( $copyFromId ){
				/* now copy timeblocks */
					$sql =<<<EOT
					SELECT
						id
					FROM
						{PRFX}timeblocks
					WHERE
						schedule_id = $copyFromId
EOT;
					$tbIds = array();
					$result = $ntsdb->runQuery( $sql );
					while( $e = $result->fetch() )
						$tbIds[] = $e['id'];

					reset( $tbIds );
					foreach( $tbIds as $tbId ){
						$tb = new ntsObject( 'timeblock' );
						$tb->setId( $tbId );

						$newTb = new ntsObject( 'timeblock' );
						$tbArray = $tb->getByArray();
						unset($tbArray['id']);
						$tbArray['schedule_id'] = $id;
						$newTb->setByArray( $tbArray );

						$cm->runCommand( $newTb, 'create' );
						}
					}

				ntsView::addAnnounce( M('Schedule') . ': ' . M('Created'), 'ok' );
				$forwardTo = ntsLink::makeLink( '-current-/../edit', '', array('_id' => $id) );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}

		break;
	default:
		break;
	}
	
if( ! isset($form) ){
	$formFile = dirname( __FILE__ ) . '/form';
	$form =& $ff->makeForm( $formFile, $formParams );
	}
?>