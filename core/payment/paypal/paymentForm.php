<?php
//$paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr"; // sandbox
$paypalUrl = "https://www.paypal.com/cgi-bin/webscr"; // live
?>
<FORM ACTION="<?php echo $paypalUrl; ?>" METHOD=POST target="_parent">
<INPUT TYPE="hidden" name="cmd" value="_xclick">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">

<input type="hidden" name="business" value="<?php echo $paymentGatewaySettings['email']; ?>">

<INPUT TYPE="hidden" NAME="currency_code" value="<?php echo strtoupper($paymentCurrency); ?>">
<input type="hidden" name="amount" value="<?php echo $paymentAmount; ?>">
<input type="hidden" name="item_name" value="<?php echo $paymentItemName; ?>">
<input type="hidden" name="item_number" value="<?php echo $paymentOrderRefNo; ?>">

<input type="hidden" name="return" value="<?php echo $paymentOkUrl; ?>">
<input type="hidden" name="cancel_return" value="<?php echo $paymentFailedUrl; ?>">
<input type="hidden" name="notify_url" value="<?php echo $paymentNotifyUrl; ?>">

<input type="submit" value="<?php echo ( isset($paymentGatewaySettings['label']) ) ? $paymentGatewaySettings['label'] : 'Pay online with Paypal'; ?>">

</FORM>