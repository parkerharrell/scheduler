<?php
$ff =& ntsFormFactory::getInstance();

$serviceId = $req->getParam( '_id' );
$object = new ntsObject( 'service' );
$object->setId( $serviceId );

$formInfo = array(
	'cats'	=> $object->getProp('_service_cat'),
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
$form->display();
?>