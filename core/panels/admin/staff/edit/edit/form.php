<?php
/* check if paypal email field required */
$pgm =& ntsPaymentGatewaysManager::getInstance();
$paymentGateways = $pgm->getActiveGateways();
$paypalEnabled = in_array('paypal', $paymentGateways) ? true : false;

$id = $this->getValue('id');
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);
?>
<h3><?php echo M('Contact Information'); ?></h3>

<?php
$class = 'provider';

$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', true );
reset( $fields );
?>
<table>
<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $class, $f[0], false ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	echo $this->makeInput (
		$c[1],
		$c[2],
		$c[3]
		);
	?>
	</td>
</tr>
<?php endforeach; ?>

<tr>
	<th><?php echo M('Timezone'); ?></th>
	<td>
	<?php
	$timezoneOptions = ntsTime::getTimezones();
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> '_timezone',
			'options'	=> $timezoneOptions,
			'default'	=> NTS_COMPANY_TIMEZONE,
			)
		);
	?>
	</td>
</tr>

</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</DIV>