<?php
$ff =& ntsFormFactory::getInstance();

$serviceId = $req->getParam( '_id' );
$object = ntsObjectFactory::get( 'service' );
$object->setId( $serviceId );

$formInfo = $object->getByArray();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $formInfo );
$form->display();
?>