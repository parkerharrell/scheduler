<?php
if( isset($params['oldStartsAt']) && $params['oldStartsAt'] ){
	$object->setProp( 'need_reminder', 0 );
	}

$this->runCommand( $object, 'update' );
$actionResult = 1;
?>