<?php
$req = new ntsRequest();

$transactionId = $req->getParam( 'x_trans_id' );
$paymentAmountGross = $req->getParam( 'x_amount' );
$paymentAmountGross = sprintf( "%.2f", $paymentAmountGross );

/* authenticate */
$suppliedHash = $req->getParam( 'x_MD5_Hash' );
$suppliedHash = strtolower( $suppliedHash );

$myHash = md5( 
	$paymentGatewaySettings['md5hash'] . 
	$paymentGatewaySettings['login_id'] . 
	$transactionId . 
	$paymentAmountGross
	);

if( $myHash == $suppliedHash ){
	if( $req->getParam( 'x_response_code') == 1 ){
		$paymentOk = true;
		$paymentRef = $transactionId;
		$paymentAmountGross = $req->getParam( 'x_amount' );
		$paymentAmountNet = $paymentAmountGross;
		}
	else {
		$paymentOk = false;
		}
	}
else {
	$paymentOk = false;
	}
$paymentResponse = $req->getParam( 'x_response_reason_text' );
?>