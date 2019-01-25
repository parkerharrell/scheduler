<?php
$id = $req->getParam( '_id' );

$object = new ntsObject( 'form' );
$object->setId( $id );
$formParams = $object->getByArray();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname(__FILE__) . '/form';
$form =& $ff->makeForm( $formFile, $formParams );
?>
<?php $form->display(); ?>