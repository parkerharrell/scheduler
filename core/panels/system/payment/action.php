<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$gateway = $req->getParam( 'gateway' );
if( ! $gateway ){
	echo "gateway required!";
	exit;
	}

/* payment manager */
$pgm =& ntsPaymentGatewaysManager::getInstance();
$paymentGateways = $pgm->getActiveGateways();

if(! in_array($gateway, $paymentGateways)){
	echo "gateway '$gateway' not found!";
	exit;
	}
$paymentGatewaySettings = $pgm->getGatewaySettings( $gateway );

$invoiceRefNo = '';

/* include gateway file to get invoice no */
$gatewayFile = $pgm->getGatewayFolder( $gateway ) . '/receivePayment_before.php';
if( file_exists( $gatewayFile ) )
	require( $gatewayFile );
else
	$invoiceRefNo = $req->getParam( 'refno' );

if( ! $invoiceRefNo ){
	echo "invoiceRefNo required!";
	exit;
	}

/* invoice info */
$sql =<<<EOT
SELECT 
	*
FROM 
	{PRFX}invoices
WHERE
	refno = '$invoiceRefNo'
EOT;
$result = $ntsdb->runQuery( $sql );
$invoiceInfo = $result->fetch();
$invoiceId = $invoiceInfo['id'];

if( ! $invoiceInfo ){
	echo "invoice '$invoiceRefNo' not found!";
	exit;
	}

$paymentOk = true;
$paymentAmountGross = 100;
$paymentAmountNet = 95;
$paymentCurrency = $conf->get( 'currency' );
$paymentRef = 'abc';
$paymentResponse = 'resp from payment gateway';

/* process payment */
$gatewayFile = $pgm->getGatewayFolder( $gateway ) . '/receivePayment.php';
require( $gatewayFile );

$cm =& ntsCommandManager::getInstance();

/* if payment is ok */
if( $paymentOk ){
/* add payment if amount is > 0 */
	if( $paymentAmountGross > 0 ){
		$payment = new ntsObject( 'payment' );
		$payment->setProp( 'invoice_id',	$invoiceId );
		$payment->setProp( 'amount_gross',	$paymentAmountGross );
		$payment->setProp( 'amount_net',	$paymentAmountNet );
		$payment->setProp( 'currency',		$paymentCurrency );

		$payment->setProp( 'pgateway',			$gateway );
		$payment->setProp( 'pgateway_ref',		$paymentRef );
		$payment->setProp( 'pgateway_response',	$paymentResponse );

		$cm->runCommand( $payment, 'create' );
		}

/* find dependants */
	$sql =<<<EOT
SELECT 
	obj_class, obj_id
FROM 
	{PRFX}objectmeta
WHERE
	meta_name = '_invoice' AND 
	meta_value = $invoiceId
EOT;
	$result = $ntsdb->runQuery( $sql );
	while( $depInfo = $result->fetch() ){
		$dep = ntsObjectFactory::get( $depInfo['obj_class'] );
		$dep->setId( $depInfo['obj_id'] );
		if( ! $dep->notFound() ){
			$cm->runCommand( $dep, 'receive_payment', array('amount' => $paymentAmountGross) );
			}
		}
	}
else {
	$payment = new ntsObject( 'payment' );
	$payment->setProp( 'invoice_id',	$invoiceId );
	$payment->setProp( 'amount_gross',	0 );
	$payment->setProp( 'amount_net',	0 );
	$payment->setProp( 'currency',		$paymentCurrency );

	$payment->setProp( 'pgateway',			$gateway );
	$payment->setProp( 'pgateway_ref',		$paymentRef );
	$payment->setProp( 'pgateway_response',	$paymentResponse );

	$cm->runCommand( $payment, 'create' );
	}

/* after payment */
$gatewayFile = $pgm->getGatewayFolder( $gateway ) . '/receivePayment_after.php';
if( file_exists( $gatewayFile ) )
	require( $gatewayFile );

exit;
?>