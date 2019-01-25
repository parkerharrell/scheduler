<?php
include_once( dirname(__FILE__) . '/includes.php' );

$req = new ntsRequest();
$strCrypt = $req->getParam( 'crypt' );

if( strlen($strCrypt) == 0 ){
	$paymentOk = false;
	}
else {
	$strEncryptionPassword = $paymentGatewaySettings['encryption_password'];
	$strDecoded = simpleXor(Base64Decode($strCrypt),$strEncryptionPassword);
	$values = getToken( $strDecoded );
	if( $values['Status'] == 'OK' ){
		$paymentOk = true;
		$paymentAmountGross = $values[ 'Amount' ];
		$paymentAmountNet = $paymentAmountGross;
		}

// PAYMENT
	$paymentResponse = '';
	reset( $values );
	foreach( $values as $key => $value ){
		$paymentResponse .= "$key: $value\n";
		}
	$paymentRef = $values[ 'VPSTxId' ];
	}
//$paymentRef = $transactionId;
//$paymentResponse = $req->getParam( 'x_response_reason_text' );
?>