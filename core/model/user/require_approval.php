<?php
$app =& ntsApplication::getInstance();

/* add restriction */
$object->setProp( '_restriction', 'not_approved' );
$this->runCommand( $object, 'update' );
?>