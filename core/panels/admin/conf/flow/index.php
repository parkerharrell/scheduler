<?php
$conf =& ntsConf::getInstance();
$confFlow = $conf->get('appointmentFlow');

$params = array();
reset( $confFlow );
foreach( $confFlow as $f ){
	$params[ 'assign-' . $f[0] ] = $f[1];
	}

$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form', $params );
$form->display();
?>