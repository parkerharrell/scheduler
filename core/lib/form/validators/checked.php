<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Checkbox On');
		$validatorAppliedOn = array('checkbox');
		break;
	default:
		if( ! $checkValue ){
			$validationFailed = true;
			}
		break;
	}
?>