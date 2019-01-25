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
					'class'	=> 'pack',
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
				'cols'	=> 42,
				'rows'	=> 6,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th colspan="2"><?php echo M('Pricing'); ?></th>
</tr>
<tr>
	<td colspan="2">
	<?php
	echo $this->makeInput(
		'PackDiscount',
		array(
			'id'	=> 'discount',
			)
		);
	?>
</td>
</tr>
</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update', array('id' => $this->getValue('id')) ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</DIV>