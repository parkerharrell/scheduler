<?php
$id = $this->getValue( 'id' );
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);
?>
<table>
<tr>
	<th><?php echo M('Title'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'title',
			'attr'		=> array(
				'size'	=> 42,
				),
			'default'	=> '',
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			array(
				'code'		=> 'checkUniqueProperty.php', 
				'error'		=> M('Already in use'),
				'params'	=> array(
					'prop'	=> 'title',
					'class'	=> 'resource',
					'skipMe'	=> 1
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th colspan="2"><?php echo M('Description'); ?></th>
</tr>
	<td colspan="2">
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 64,
				'rows'	=> 6,
				),
			'default'	=> '',
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<?php
// additional fields if any
$className = 'resource';
$om =& objectMapper::getInstance();
$fields = $om->getFields( $className, 'internal', true );
reset( $fields );
?>

<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $className, $f[0], false ); ?>
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

<?php
$pgm =& ntsPaymentGatewaysManager::getInstance();
$paymentGateways = $pgm->getActiveGateways();
$paypalEnabled = in_array('paypal', $paymentGateways) ? true : false;
?>
<?php if( $paypalEnabled ) : ?>
<tr>
	<th><?php echo M('Paypal Email'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> '_paypal',
			'attr'		=> array(
				'size'	=> 32,
				),
			'default'	=> '',
			'required'	=> 0,
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<i>Set this if you wish to provide a separate Paypal account for this resource. Otherwise the global account will be used.</i>
	</td>
</tr>
<?php endif; ?>

</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update', array('id' => $this->getValue('id')) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</DIV>