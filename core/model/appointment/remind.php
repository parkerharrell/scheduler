<?php
$object->setProp( 'need_reminder', 0 );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>