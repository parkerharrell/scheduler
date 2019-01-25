<?php
$ff =& ntsFormFactory::getInstance();

$serviceId = $req->getParam( '_id' );
$object = ntsObjectFactory::get( 'service' );
$object->setId( $serviceId );

$activeGateways = $object->getPaymentGateways();
$formInfo = array(
	'payment_gateways'	=> $activeGateways,
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
$form->display();
?>