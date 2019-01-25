<?php
$ff =& ntsFormFactory::getInstance();

$pgm =& ntsPaymentGatewaysManager::getInstance();
$allGateways = $pgm->getActiveGateways();

$packId = $req->getParam( '_id' );
$object = new ntsObject( 'pack' );
$object->setId( $packId );
$disabledGateways = $object->getProp( '_disable_gateway' );

$activeGateways = array();
reset( $allGateways );
foreach( $allGateways as $gw ){
	if( ! in_array($gw, $disabledGateways) )
		$activeGateways[] = $gw;
	}

$formInfo = array(
	'payment_gateways'	=> $activeGateways,
	);

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
$form->display();
?>