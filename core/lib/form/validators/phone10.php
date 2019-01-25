<?php
$numberOfDigits = 10;
switch( $validatorAction ){
	case 'display':
		$validatorTitle = M('Valid Phone Number (10 digits)');
		$validatorAppliedOn = array('text', 'textarea');
		break;
	default:
		$countMatches = 0;
		if( preg_match_all("/\d/", $checkValue, $matches) ){
			$countMatches = count($matches[0]);
			}
		if ( $countMatches != $numberOfDigits )
			$validationFailed = true;
		break;
	}
?>