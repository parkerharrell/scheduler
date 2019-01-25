<?php
$paymentOkUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'ok') );
$paymentFailedUrl = ntsLink::makeLink( 'customer/invoices/view', '', array('refno' => $invoiceRefNo, 'display' => 'fail') );
?>
<?php if( $paymentOk ) : ?>
	<META http-equiv="refresh" content="0;URL=<?php echo $paymentOkUrl; ?>">
<?php else : ?>
	<META http-equiv="refresh" content="0;URL=<?php echo $paymentFailedUrl; ?>">
<?php endif; ?>