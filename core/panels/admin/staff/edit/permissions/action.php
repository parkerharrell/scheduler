<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$object = new ntsUser();
$object->setId( $id );
$objectInfo = $object->getByArray();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $objectInfo );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate($req) ){
			$formValues = $NTS_VIEW['form']->getValues();

		/* update user */
			$object = new ntsUser();
			$object->setId( $id );

			$object->setByArray( $formValues );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() ){
				ntsView::setAnnounce( M('User') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-', '', array('id' => $id ) );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to edit form */
			}

		break;
	default:
		break;
	}
?>