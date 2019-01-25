<TABLE>
<tr>
	<th><?php echo M('Label'); ?> *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'label',
			'default'	=> 'Credit Card by SagePay UK',
			'attr'		=> array(
				'size'	=> 32,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th>Test Mode</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'test',
			)
		);
	?>
	</TD>
</TR>

<tr>
<td colspan="2" style="padding-top: 1em;">
<i style="text-decoration: underline;">
Get these values in your SagePay control panel
</i>
</td>
</tr>

<tr>
	<th>Vendor Name *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'vendor_name',
			'default'	=> 'My Company',
			'attr'		=> array(
				'size'	=> 32,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th>Encryption Password *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'encryption_password',
			'default'	=> '12345678',
			'attr'		=> array(
				'size'	=> 32,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			)
		);
	?>
	</TD>
</TR>

</TABLE>
