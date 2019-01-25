<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );

if( $form->validate($req) ){
	$formValues = $form->getValues();
	$currentFlowSetting = $formValues['current-flow-setting'];
	$flow = explode( "|", $currentFlowSetting );
	$currentFlow = array();
	reset( $flow );
	foreach( $flow as $f ){
		$f = trim( $f );
		if( ! $f )
			continue;
		switch( $f ){
			case 'location':
			case 'resource':
				$mode = $formValues['assign-' . $f];
				break;
			default:
				$mode = 'manual';
				break;
			}
		$currentFlow[] = array( $f, $mode );
		}

	$serviceIndex = 0;
	$timeIndex = 0;
	$i = 0;
	reset( $currentFlow );
	foreach( $currentFlow as $f ){
		if( $f[0] == 'service' )
			$serviceIndex = $i;
		if( $f[0] == 'time' )
			$timeIndex = $i;
		$i++;
		}

	if( $timeIndex < $serviceIndex ){
		$errorText = M('Service selection should come before the date and time selection');
		ntsView::addAnnounce( $errorText, 'error' );
		}
	else {
		$newValue = $conf->set( 'appointmentFlow', $currentFlow );
		$sql = $conf->getSaveSql( 'appointmentFlow', $newValue );
		$result = $ntsdb->runQuery( $sql );

		if( $result ){
			ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

		/* continue to form */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
			}
		else {
			$errorText = 'Database error:<BR>' . $ntsdb->getError();
			ntsView::addAnnounce( $errorText, 'error' );
			}
		}
	}
?>