<?php
$conf =& ntsConf::getInstance();
if( $conf->get('keepCancelledApps') ){
	$object->setProp( 'cancelled', 1 );
	$this->runCommand( $object, 'update' );
	}
else {
	$this->runCommand( $object, 'delete' );
	}
$actionResult = 1;
?>