<?php
include_once( dirname(__FILE__) . '/ntsPaypal.php' );

/* should return $paymentOk, $paymentAmountGross, $paymentAmountNet $paymentRef, $paymentResponse vars */
$paymentOk = true;
$paymentAmountGross = 0;
$paymentAmountNet = 0;

$paymentRef = '';
$paymentResponse = '';

$p = new ntsPaypal();
if( $p->validateIpn() ){
	$paymentOk = true;

	$paymentAmountGross = $p->ipn_data['mc_gross'];
	$fee = $p->ipn_data['mc_fee'];
	$paymentAmountNet = $paymentAmountGross - $fee;
	}
else {
	$paymentOk = 0;
	$paymentAmountNet = 0;
	}

if( isset($p->ipn_data['txn_id']) )
	$paymentRef = $p->ipn_data['txn_id'];

reset( $p->ipn_data );
foreach( $p->ipn_data as $key => $value ){
	$paymentResponse .= "$key: $value\n";
	}
?>