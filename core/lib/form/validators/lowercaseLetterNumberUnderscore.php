<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Lowercase English Letters, Numbers, Underscores Only');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if ( preg_match("/[^0-9a-z_]/", $checkValue) )
			$validationFailed = true;
		break;
	}
?>