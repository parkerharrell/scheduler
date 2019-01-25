<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( 'id' ); 

$object = new ntsObject( 'timeblock' );
$object->setId( $id );
$formParams = $object->getByArray();

if( $object->getProp('selectable_every') ){
	$formParams['selectable_style'] = 'every';
	}
else
	$formParams['selectable_style'] = 'fixed';

switch( $action ){
	case 'update':
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $formParams );

		$removeValidation = array();
		$selectableStyle = $req->getParam('selectable_style');
		if( $selectableStyle != 'fixed' ){
			$removeValidation[] = 'selectable_fixed';
			}
		if( $form->validate($req, $removeValidation) ){
			$formValues = $form->getValues();

			switch( $formValues['selectable_style'] ){
				case 'fixed':
					$formValues['selectable_every'] = 0;
					break;
				case 'every':
					$formValues['selectable_fixed'] = array();
					break;
				}
			unset( $formValues['selectable_style'] );

			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'update' );
			
			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Time Slot') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );
				$forwardTo = ntsLink::makeLink( '-current-/../..' );
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