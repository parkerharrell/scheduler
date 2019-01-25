<?php
require_once( dirname(__FILE__) . '/includes.php' );

$testMode = $paymentGatewaySettings['test'];

if( $testMode )
	$paymentUrl = "https://test.sagepay.com/simulator/vspformgateway.asp";
else
	$paymentUrl = "https://live.sagepay.com/gateway/service/vspform-register.vsp";

$customerEmail = $invoiceInfo['customer']->getProp( 'email' );
$customerFullName = $invoiceInfo['customer']->getProp( 'first_name' ) . ' ' . $invoiceInfo['customer']->getProp( 'last_name' );

/*
PAYMENT FORM VARIABLES
*/
$strTransactionType = "PAYMENT";
$strVSPVendorName = $paymentGatewaySettings['vendor_name'];
$strEncryptionPassword = $paymentGatewaySettings['encryption_password'];

/** Okay, build the crypt field for VSP Form using the information in our session **
*** First we need to generate a unique VendorTxCode for this transaction **
*** We're using VendorName, time stamp and a random element.  You can use different methods if you wish **
*** but the VendorTxCode MUST be unique for each transaction you send to VSP Server **/

$intRandNum = rand(0,32000) * rand(0,32000);
$strVendorTxCode = $strVSPVendorName . $intRandNum;

$sngTotal = $paymentAmount;
$strCurrency = $paymentCurrency;

// Now to build the VSP Form crypt field.  For more details see the VSP Form Protocol 2.23 
$strPost = "VendorTxCode=" . $strVendorTxCode; /** As generated above **/
$strPost = $strPost . "&Amount=" . number_format($sngTotal,2); // Formatted to 2 decimal places with leading digit
$strPost=$strPost . "&Currency=" . $strCurrency;
// Up to 100 chars of free format description
$strPost=$strPost . "&Description=" . $paymentItemName;
$strPost=$strPost . "&CustomerName=" . $customerFullName;
$strPost=$strPost . "&CustomerEMail=" . $customerEmail;

$strBasket = '';

$strPost=$strPost . "&Basket=" . $strBasket; // As created above 

$strPost=$strPost . "&SuccessURL=" . $paymentNotifyUrl;
$strPost=$strPost . "&FailureURL=" . $paymentFailedUrl;
?>
<?php
// Billing Details:
$strBillingFirstnames = $invoiceInfo['customer']->getProp( 'first_name' );
$strBillingSurname = $invoiceInfo['customer']->getProp( 'last_name' );

$strPost=$strPost . "&BillingFirstnames=" . $strBillingFirstnames;
$strPost=$strPost . "&BillingSurname=" . $strBillingSurname;
$strPost=$strPost . "&BillingAddress1=" . 'NA';

$strPost=$strPost . "&BillingCity=" . 'NA';
$strPost=$strPost . "&BillingPostCode=" . 'NA';
$strPost=$strPost . "&BillingCountry=" . 'NA';

$strPost=$strPost . "&DeliverySurname=" . $strBillingSurname;
$strPost=$strPost . "&DeliveryFirstnames=" . $strBillingFirstnames;
$strPost=$strPost . "&DeliveryAddress1=" . 'NA';
$strPost=$strPost . "&DeliveryCity=" . 'NA';
$strPost=$strPost . "&DeliveryPostCode=" . 'NA';
$strPost=$strPost . "&DeliveryCountry=" . 'NA';

// Encrypt the plaintext string for inclusion in the hidden field
$strCrypt = base64Encode(SimpleXor($strPost,$strEncryptionPassword));	
?>
<FORM ACTION="<?php echo $paymentUrl; ?>" METHOD=POST>

<input type="hidden" name="VPSProtocol" value="2.23">
<input type="hidden" name="TxType" value="<?php echo $strTransactionType ?>">
<input type="hidden" name="Vendor" value="<?php echo $strVSPVendorName ?>">
<input type="hidden" name="Crypt" value="<?php echo $strCrypt ?>">

<input type="submit" value="<?php echo $paymentGatewaySettings['label']; ?>">
</FORM>
