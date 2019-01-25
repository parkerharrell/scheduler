<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Valid URL Syntax');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if ( ! preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i", $checkValue) )
			$validationFailed = true;
		break;
	}
?>