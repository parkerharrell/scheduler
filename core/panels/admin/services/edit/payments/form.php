<?php
$pgm =& ntsPaymentGatewaysManager::getInstance();
$paymentGateways = $pgm->getActiveGateways();

$pgOptions = array();
foreach( $paymentGateways as $pg ){
	$pgOptions[] = array( $pg, $pg );
	}
?>
<ul style="list-style-type: none; padding: 0 1em;">
<?php
echo $this->makeInput (
/* type */
	'checkboxSet',
/* attributes */
	array(
		'id'		=> 'payment_gateways',
		'options'	=> $pgOptions,
		'attr'		=> array(
			'separator_before'	=> '<li style="margin: 1em 0; padding: 0;">',
			'separator_after'	=> '',
			),
		),
/* validators */
	array(
		array(
			'code'		=> 'notEmpty.php', 
			'error'		=> M('Please choose at least one option'),
			),
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'update' ); ?>
<li>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</ul>
