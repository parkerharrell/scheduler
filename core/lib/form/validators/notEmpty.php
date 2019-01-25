<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Required field');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if( is_array($checkValue) ){
			if( ! $checkValue )
				$validationFailed = true;
			}
		else {
			$checkValue = trim( $checkValue );
			if( ! strlen($checkValue) ){
				$validationFailed = true;
				}
			}
		break;
	}
?>