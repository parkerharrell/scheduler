<?php
$object->unsetGhost();
$object->setProp( 'approved', 1 );
$this->runCommand( $object, 'update' );
$actionResult = 1;
?>