<?php
$now = time();
$object->setProp( 'created_at', $now );
$object->setProp( 'ghost_last_access', $now );
$object->setProp( 'approved', 0 );
$object->setProp( 'no_show', 0 );
$object->setProp( 'is_ghost', 1 );

/* reminder */
$object->setProp( 'need_reminder', 1 );

/* auth code */
include_once( NTS_BASE_DIR . '/lib/crypt/ntsRandomGenerator.php' );
$gen = new ntsRandomGenerator;
$authCode = $gen->generate(8);
$object->setProp( 'auth_code', $authCode );

$this->runCommand( $object, 'create' );
$actionResult = 1;
?>
