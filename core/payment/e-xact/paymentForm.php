<?php
$testMode = $paymentGatewaySettings['test'];

if( ! class_exists('AuthorizeNetSim') ){
	require_once( dirname(__FILE__) . '/code/AuthorizeNetSim.php' );
	}

if( $testMode ){
	$paymentUrl = "https://rpm-demo.e-xact.com/pay"; // test
	}
else {
	$paymentUrl = "https://checkout.e-xact.com/pay"; // live
	}

$authorizeNetSim = new AuthorizeNetSim;

$loginId = $paymentGatewaySettings['login_id'];
$tranKey = $paymentGatewaySettings['transaction_key'];

$customerEmail = $invoiceInfo['customer']->getProp( 'email' );

srand(time());
$sequence = rand(1, 1000);

$paymentCurrency = strtoupper( $paymentCurrency );
?>

<FORM ACTION="<?php echo $paymentUrl; ?>" METHOD=POST>

<?php $authorizeNetSim->InsertFP($loginId, $tranKey, $paymentAmount, $sequence, $paymentCurrency); ?>

<INPUT type="hidden" name="x_show_form" value="PAYMENT_FORM">
<INPUT type="hidden" name="x_relay_response" value="TRUE">
<INPUT type="hidden" name="x_login" value="<?php echo $loginId; ?>">
<INPUT type="hidden" name="x_currency_code" value="<?php echo $paymentCurrency; ?>">
<?php if( $testMode ) : ?>
	<INPUT type="hidden" name="x_test_request" value="TRUE">
<?php endif; ?>
<INPUT type="hidden" name="x_description" value="<?php echo $paymentItemName; ?>">
<INPUT type="hidden" name="x_invoice_num" value="<?php echo $paymentOrderRefNo; ?>">
<INPUT type="hidden" name="x_amount" value="<?php echo $paymentAmount; ?>">
<INPUT type="hidden" name="x_relay_url22" value="<?php echo $paymentNotifyUrl; ?>">

<INPUT type="hidden" name="x_email" value="<?php echo $customerEmail; ?>">
<INPUT type="hidden" name="x_first_name" value="<?php echo $invoiceInfo['customer']->getProp( 'first_name' ); ?>">
<INPUT type="hidden" name="x_last_name" value="<?php echo $invoiceInfo['customer']->getProp( 'last_name' ); ?>">
<INPUT type="hidden" name="x_cust_id" value="<?php echo $invoiceInfo['customer']->getId(); ?>">

<input type="hidden" name="x_receipt_link_URL" value="<?php echo $paymentOkUrl; ?>">


<input type="submit" value="<?php echo $paymentGatewaySettings['label']; ?>">
</FORM>

<?php
/*
A:  You can use any of the following test credit card numbers.  The expiration date must be set to the present date or later:
370000000000002 American Express Test Card
6011000000000012 Discover Test Card
5424000000000015 MasterCard Test Card
4007000000027 Visa Test Card
4012888818888 second Visa Test Card
3088000000000017 JCB 
38000000000006 Diners Club/ Carte Blanche
*/
?>