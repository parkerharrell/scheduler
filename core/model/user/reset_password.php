<?php
/* new password */
include_once( NTS_BASE_DIR . '/lib/crypt/ntsRandomGenerator.php' );
$gen = new ntsRandomGenerator;
$newPassword = $gen->generate( 8 );

$object->setProp( 'new_password', $newPassword );

$this->silent = true;
$this->runCommand( $object, 'update' );
$this->silent = false;

$actionResult = 1;
?>