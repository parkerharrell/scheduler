<?php
$conf =& ntsConf::getInstance();

$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$service = ntsObjectFactory::get( 'service' );
$service->setId( $id );
$serviceInfo = $service->getByArray();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $serviceInfo );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate($req) ){
			$formValues = $NTS_VIEW['form']->getValues();

		/* service */
			$service->setByArray( $formValues );

			$cm =& ntsCommandManager::getInstance();
			$cm->runCommand( $service, 'update' );
			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Service') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('_id' => $id) );
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
	}
?>