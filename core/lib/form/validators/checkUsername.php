<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Unique Username');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		$uif =& ntsUserIntegratorFactory::getInstance();
		$integrator =& $uif->getIntegrator();

		$myWhere = array();
		$myWhere['username'] = " = \"$checkValue\"";

		if( isset($formValues['id']) && ($formValues['id'] > 0) ){
			$myId = $formValues['id'];
			$myWhere['id'] = " <> $myId";
			}

		$count = $integrator->countUsers( $myWhere );
		if( $count ){
			$validationFailed = 1;
			}
		break;
	}
?>