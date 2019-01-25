<?php
$conf =& ntsConf::getInstance();
$pgm =& ntsPaymentGatewaysManager::getInstance();
$ntsdb =& dbWrapper::getInstance();

$invoiceInfo = $NTS_VIEW['invoiceInfo'];
$paymentGateways = $NTS_VIEW['paymentGateways'];

/* prepare some common data for payment forms */
$paymentCurrency = $conf->get( 'currency' );

$paymentAmount = $invoiceInfo['amount'];
$paymentItemName = $invoiceInfo['item_name'];
$invoiceRefNo = $invoiceInfo['refno'];
$paymentOrderRefNo = $invoiceRefNo;

reset( $paymentGateways );
?>
<H2><?php echo M('Payment Required'); ?></H2>

<p>
<h3><?php echo $paymentItemName; ?></h3>
<p>
<?php echo M('Total Amount'); ?>: <b><?php echo ntsCurrency::formatPrice($paymentAmount); ?></b>

<p>
<table>
<tr>
<?php foreach( $paymentGateways as $gateway ) : ?>
<?php
	$gatewayFolder = $pgm->getGatewayFolder( $gateway );
	$gatewayFile = $gatewayFolder . '/paymentForm.php';

	$paymentGatewaySettings = $pgm->getGatewaySettings( $gateway );

	if( $gateway == 'paypal' ){
		/* hack for hitAppoint - find provider Paypal*/
		$invoiceId = $invoiceInfo['id'];
		$sql =<<<EOT
		SELECT 
			obj_id
		FROM 
			{PRFX}objectmeta
		WHERE
			meta_name = '_invoice' AND 
			meta_value = $invoiceId AND
			obj_class = 'appointment'
EOT;

		$result = $ntsdb->runQuery( $sql );
		if( $appInfo = $result->fetch() ){
			$appointment = ntsObjectFactory::get( 'appointment' );
			$appointment->setId( $appInfo['obj_id'] );

			$resource = ntsObjectFactory::get( 'resource' );
			$resource->setId( $appointment->getProp( 'resource_id' ) );
			$myPaypal = $resource->getProp( '_paypal' );
			if( $myPaypal )
				$paymentGatewaySettings['email'] = $myPaypal;
			}
		}

	/* some links */
	$paymentNotifyUrl = ntsLink::makeLink( 'system/payment', '', array('gateway' => $gateway) ) . '&refno=' . $invoiceRefNo;
	$paymentOkUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'ok') );
	$paymentFailedUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'fail') );
?>
<td style="padding: 0 0.5em;">
<?php	require( $gatewayFile ); ?>
</td>
<?php endforeach; ?>
</tr>
</table>