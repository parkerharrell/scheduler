<?php
$testMode = $paymentGatewaySettings['test'];

$paymentUrl = "https://merchant.ematters.com.au/cmaonline.nsf/Payment?OpenAgent";

$customerEmail = $invoiceInfo['customer']->getProp( 'email' );
$customerFullName = $invoiceInfo['customer']->getProp( 'first_name' ) . ' ' . $invoiceInfo['customer']->getProp( 'last_name' );

$invoiceId    = $invoiceInfo['id'];
$merchantId = $paymentGatewaySettings['merchant_id'];
?>
<FORM ACTION="<?php echo $paymentUrl; ?>" METHOD=POST>

<?php if( $paymentGatewaySettings['notice'] ) : ?>
	<?php echo $paymentGatewaySettings['notice']; ?>
<?php endif; ?>

<?php if( $paymentGatewaySettings['submerchant'] ) : ?>
	<input type="hidden" name="s" value="<?php echo $paymentGatewaySettings['submerchant']; ?>">
<?php endif; ?>

<?php if( $testMode ) : ?>
	<input type="hidden" name="m" value="t">
<?php endif; ?>


<input type="hidden" name="a" value="<?php echo $merchantId; ?>">
<input type="hidden" name="r" value="<?php echo $paymentNotifyUrl; ?>&">
<input type="hidden" name="p" value="<?php echo $paymentAmount; ?>">
<input type="hidden" name="e" value="<?php echo $customerEmail; ?>">
<input type="hidden" name="cn" value="<?php echo $customerFullName; ?>">
<?php if( 0 ) : ?>
	<input type="hidden" name="u" value="<?php echo $invoiceId; ?>">
<?php endif; ?>

<input type="submit" value="<?php echo $paymentGatewaySettings['label']; ?>">
</FORM>