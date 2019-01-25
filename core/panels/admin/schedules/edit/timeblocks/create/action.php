<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$appliedOn = $req->getParam( 'applied_on' );
if( ! $appliedOn )
	$appliedOn = 0;
$scheduleId = $req->getParam( '_id' );
$formParams = array(
	'applied_on'	=> $appliedOn,
	'schedule_id'	=> $scheduleId,
	);

switch( $action ){
	case 'create':
		$conf =& ntsConf::getInstance();

		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile, $formParams );

		$removeValidation = array();
		$selectableStyle = $req->getParam('selectable_style');
		if( $selectableStyle != 'fixed' ){
			$removeValidation[] = 'selectable_fixed';
			}
		if( $form->validate($req, $removeValidation) ){
			$formValues = $form->getValues();
			
			$formValues['schedule_id'] = $scheduleId;
			$formValues['applied_on'] = $appliedOn;

			switch( $formValues['selectable_style'] ){
				case 'fixed':
					$formValues['selectable_every'] = 0;
					break;
				case 'every':
					$formValues['selectable_fixed'] = array();
					break;
				}
			unset( $formValues['selectable_style'] );

			$object = new ntsObject('timeblock');
			$object->setByArray( $formValues );
			$cm->runCommand( $object, 'create' );

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Time Slot') . ': ' . M('Created'), 'ok' );
				$forwardTo = ntsLink::makeLink( '-current-/../..', '', array('_id' => $scheduleId) );
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