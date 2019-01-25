<?php
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Not The First Option In Select List');
		$validatorAppliedOn = array('select');
		break;
	default:
		$checkValue = trim( $checkValue );
		$validationFailed = false;
		// ALLOW EMPTY VALUE
		if( ! $checkValue ){
			}
		else {
			if( isset($controlConf['options'][0]) ){
				$firstOption = $controlConf['options'][0][0];
				if( $firstOption == $checkValue ){
					$validationFailed = true;
					}
				}
			}
		break;
	}
?>