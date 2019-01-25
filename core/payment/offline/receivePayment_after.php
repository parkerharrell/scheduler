<?php
$paymentOkUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'ok') );
$paymentFailedUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'fail') );

if( $paymentOk )
	ntsView::redirect( $paymentOkUrl );
else
	ntsView::redirect( $paymentFailedUrl );
?>