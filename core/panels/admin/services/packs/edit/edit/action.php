<?php
$ff =& ntsFormFactory::getInstance();
$ntsdb =& dbWrapper::getInstance();
$id = $req->getParam( '_id' );
$NTS_VIEW['id'] = $id;

$object = new ntsObject( 'pack' );
$object->setId( $id );
$objInfo = $object->getByArray();

$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $objInfo );
?>