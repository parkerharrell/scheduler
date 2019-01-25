<?php
$ntsdb =& dbWrapper::getInstance();

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
	{PRFX}invoices.refno = '$invoiceRefNo'
EOT;
$result = $ntsdb->runQuery( $sql );
$invoiceInfo = $result->fetch();

if( ! $invoiceInfo ){
	echo "invoice '$invoiceRefNo' not found!";
	exit;
	}
$invoiceId = $invoiceInfo['id'];

/* check if the invoice is already fully paid */
$paidAmount = 0;
$sql =<<<EOT
SELECT 
	amount_gross
FROM 
	{PRFX}payments
WHERE
	invoice_id = $invoiceId
EOT;
$result = $ntsdb->runQuery( $sql );
while( $p = $result->fetch() ){
	$paidAmount += $p['amount_gross'];
	}

if( $paidAmount >= $invoiceInfo['amount'] ){
	// redirect to 
	$paymentOkUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'ok') );
	ntsView::redirect( $paymentOkUrl );
	exit;
	}

/* payment manager */
$pgm =& ntsPaymentGatewaysManager::getInstance();
$allGateways = $pgm->getActiveGateways();

/* find dependants and item name */
$sql =<<<EOT
SELECT 
	obj_class, obj_id
FROM 
	{PRFX}objectmeta
WHERE
	meta_name = '_invoice' AND 
	meta_value = $invoiceId
EOT;
$deps = array();
$result = $ntsdb->runQuery( $sql );
while( $depInfo = $result->fetch() ){
	$deps[] = array( $depInfo['obj_class'], $depInfo['obj_id'] );
	}

if( $deps ){
	switch( $deps[0][0] ){
		case 'appointment':
			$appointment = ntsObjectFactory::get( 'appointment' );
			$appointment->setId( $deps[0][1] );

			$customer = new ntsUser();
			$customer->setId( $appointment->getProp('customer_id') );
			$invoiceInfo['customer'] = $customer;

			$service = new ntsObject( 'service' );
			$service->setId( $appointment->getProp('service_id') );

			if( count($deps) == 1 ){
				$t = new ntsTime( $appointment->getProp('starts_at'), $NTS_CURRENT_USER->getprop('_timezone') );
				$invoiceInfo['item_name'] = $service->getProp('title') . ' ' . $t->formatFull();
				}
			else {
				$itemName = M('Invoice') . ' ' . $invoiceInfo['refno'];
				$invoiceInfo['item_name'] = $itemName;
				}

		// if app is a part of pack, search for gateways for the pack otherwise get appointment's
			$packId = $appointment->getProp('_pack');
			if( $packId ){
				$pack = new ntsObject('pack');
				$pack->setId( $packId );
				$disabledGateways = $pack->getProp( '_disable_gateway' );
				}
			else {
				$disabledGateways = $service->getProp( '_disable_gateway' );
				}

			$paymentGateways = array();
			reset( $allGateways );
			foreach( $allGateways as $gw ){
				if( ! in_array($gw, $disabledGateways) )
					$paymentGateways[] = $gw;
				}
			
			break;
		default:
			$itemName = M('Invoice') . ' ' . $invoiceInfo['refno'];
			$invoiceInfo['item_name'] = $itemName;

			$paymentGateways = $allGateways;
			break;
		}
	}

$NTS_VIEW['paymentGateways'] = $paymentGateways;
$NTS_VIEW['invoiceInfo'] = $invoiceInfo;
?>