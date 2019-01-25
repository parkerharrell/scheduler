<?php
switch( $validatorAction ){
	case 'display':
		$validatorSkip = true;
		break;
	default:	
		if( isset($validationParams['compareWithField']) ){
			$compareWithField = $validationParams['compareWithField'];
			$compareWith = $formValues[ $compareWithField ];
			}
		else {
			$compareWith = $validationParams['compareWith'];
			}

		$checkValue = trim( $checkValue );

		$validationFailed = false;
		// LESS OR EQUAL TO THE COMPARE WITH FIELD
		if( $checkValue < $compareWith ){
			$validationFailed = true;
			}
		}
?>