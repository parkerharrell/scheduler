<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Unique Email');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		if( ! (defined('NTS_ALLOW_DUPLICATE_EMAILS') && NTS_ALLOW_DUPLICATE_EMAILS) ){
			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();

			$myWhere = array();
			$myWhere['email'] = " = \"$checkValue\"";

			if( isset($formValues['id']) && ($formValues['id'] > 0) ){
				$myId = $formValues['id'];
				$myWhere['id'] = " <> $myId";
				}

			$count = $integrator->countUsers( $myWhere );
			if( $count ){
				$validationFailed = 1;
				}
			}
		break;
	}
?>