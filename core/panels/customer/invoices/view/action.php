<?php
if( ! isset($returnBackAsRequest) )
	$returnBackAsRequest = 1;

$ntsdb =& dbWrapper::getInstance();

$display = $req->getParam( 'display' ); 
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

$NTS_VIEW['invoiceInfo'] = $invoiceInfo;
$invoiceId = $invoiceInfo['id'];

/* payments for this invoice */
$payments = array();
$sql =<<<EOT
SELECT 
	*
FROM 
	{PRFX}payments
WHERE
	invoice_id = $invoiceId
ORDER BY
	paid_at DESC
EOT;

$paidAmount = 0;
$result = $ntsdb->runQuery( $sql );
while( $p = $result->fetch() ){
	$payments[] = $p;
	$paidAmount += $p['amount_gross'];
	}

$NTS_VIEW['payments'] = $payments;
$NTS_VIEW['paidAmount'] = $paidAmount;

/* find dependants */
if( $display == 'ok' ){
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
				if( count($deps) == 1 ){
					$forwardTo = ntsLink::makeLink( 'customer/appointments/view', '', array('id' => $deps[0][1], 'request' => $returnBackAsRequest) );
					$_REQUEST['request'] = 1;
					$appId = $deps[0][1];
					$_REQUEST['id'] = $appId;
					$nextPanel = 'customer/appointments/view';

					$appointment = ntsObjectFactory::get( 'appointment' );
					$appointment->setId( $appId );
					$customerId = $appointment->getProp( 'customer_id' );
					if( ! isset($_SESSION['temp_customer_id']) )
						$_SESSION['temp_customer_id'] = $customerId;
					ntsView::setNextAction( $nextPanel );
					return;
					}
				else {
//					$forwardTo = ntsLink::makeLink( 'customer/appointments/browse' );
					$ids = array();
					foreach( $deps as $d )
						$ids[] = $d[1];
					$id2view = join( '-', $ids );
					$forwardTo = ntsLink::makeLink( 'customer/appointments/view', '', array('id' => $id2view, 'request' => $returnBackAsRequest) );
					}
				ntsView::redirect( $forwardTo );
				exit;
				break;
			default:
				break;
			}
		}
	}
?>