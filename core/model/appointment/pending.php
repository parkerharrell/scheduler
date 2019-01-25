<?php
$alreadyApproved = $object->getProp( 'approved' );
if( ! $alreadyApproved ){
	$actionResult = 0;
	$actionError = M('Appointment') . ': ' . M('Already Pending');
	$actionStop = true;
	return;
	}
else {
	$object->setProp( 'approved', 0 );
	$this->runCommand( $object, 'update' );
	$actionResult = 1;
	}
?>