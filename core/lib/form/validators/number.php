<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Numbers only');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if ( preg_match("/[^0-9.]/", $checkValue) )
			$validationFailed = true;
		break;
	}
?>