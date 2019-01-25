<?php
$plugin = 'sms';

$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();
$plm =& ntsPluginManager::getInstance();

$defaults = $plm->getPluginSettings( $plugin );

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $defaults );

$confPrefix = 'plugin-' . $plugin . '-';

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile );
if( $form->validate($req) ){
	$formValues = $form->getValues();

/* add theese settings to the database */
	$result = true;
	reset( $formValues );
	foreach( $formValues as $pName => $pValue ){
		$pName = $confPrefix . $pName;
		$newValue = $conf->set( $pName, $pValue );
		$sql = $conf->getSaveSql( $pName, $pValue );
		$result = $ntsdb->runQuery( $sql );
		}

	if( $result ){
		ntsView::setAnnounce( M('Settings')  . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

	/* continue to the list with anouncement */
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
else {
/* form not valid, continue to edit form */
	}
?>