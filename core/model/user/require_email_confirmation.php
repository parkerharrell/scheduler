<?php
include_once( NTS_BASE_DIR . '/lib/crypt/ntsRandomGenerator.php' );
$gen = new ntsRandomGenerator;
$confirmKey = $gen->generate(8);

$app =& ntsApplication::getInstance();
$conf =& ntsConf::getInstance();

/* add restriction */
$restriction = $object->getProp( '_restriction' );
if( in_array('email_not_confirmed', $restriction) ){
	$actionResult = 1;
	}
else {
	$object->setProp( '_restriction', 'email_not_confirmed' );
	$object->setProp( '_confirmKey', $confirmKey );

	$this->runCommand( $object, 'update' );
	}
?>