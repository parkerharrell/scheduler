<?php
switch( $validatorAction ){
	case 'display':
		$validatorSkip = true;
		break;
	default:
		$mainPasswordField = $validationParams[ 'mainPasswordField' ];
		$mainPasswordFieldValue = $formValues[ $mainPasswordField ];

		$checkValue = trim( $checkValue );

		$validationFailed = false;
		// NOT EQUAL TO THE ALLOW EMPTY VALUE
		if( $mainPasswordFieldValue != $checkValue ){
			$validationFailed = true;
			}
		}
?>