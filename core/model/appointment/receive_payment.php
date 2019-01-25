<?php
$amount = isset($params['amount']) ? $params['amount'] : 0;

/* auto approve appointment if paid for */
if( $amount > 0 ){
	$this->runCommand( $object, 'request' );
	}
/* no payment done - see settings for permissions */
else {
	$this->runCommand( $object, '_request' );
	}
?>