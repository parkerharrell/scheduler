<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$id = $req->getParam( '_id' );

$payments = array();
$paidAmount = 0;
$invoice = null;

$app = ntsObjectFactory::get( 'appointment' );
$app->setId( $id );
$invoiceId = $app->getProp('_invoice');

switch( $action ){
	case 'add':	
		$formFile = dirname( __FILE__ ) . '/addPaymentForm';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate($req) ){
			$formValues = $form->getValues();

			$payment = new ntsObject( 'payment' );
			$payment->setProp( 'invoice_id',	$invoiceId );
			$payment->setProp( 'amount_gross',	$formValues['amount'] );
			$payment->setProp( 'amount_net',	$formValues['amount'] );

			$payment->setProp( 'pgateway',			'offline' );
			$payment->setProp( 'pgateway_ref',		'' );
			$payment->setProp( 'pgateway_response',	'' );
			$cm->runCommand( $payment, 'create' );

			if( $cm->isOk() ){
				ntsView::addAnnounce( M('Payment') . ': ' . M('Add') . ': ' . M('OK'), 'ok' );

			/* continue to the list with anouncement */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	default:
		break;
	}

if( $invoiceId ){
	$invoice = new ntsObject( 'invoice' );
	$invoice->setId( $invoiceId );

	/* payments for this invoice */
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

	$result = $ntsdb->runQuery( $sql );
	while( $p = $result->fetch() ){
		$payments[] = $p;
		$paidAmount += $p['amount_gross'];
		}
	}

$NTS_VIEW['object'] = $app;
$NTS_VIEW['invoice'] = $invoice;
$NTS_VIEW['payments'] = $payments;
$NTS_VIEW['paidAmount'] = $paidAmount;	
?>