<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('One Entry Only');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		break;
	}
?>