<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Integers Only');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if ( preg_match("/[^0-9]/", $checkValue) )
			$validationFailed = true;
		break;
	}
?>