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
			'default'	=> 'Credit Card by eMatters Australia',
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
	<th>Merchant Id *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'merchant_id',
			'default'	=> 'TST0022',
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
	<th>SubMerchant (Optional)</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'submerchant',
			'default'	=> 'Appointments',
			'attr'		=> array(
				'size'	=> 32
				),
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th>Additional Notice (Optional)</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'notice',
			'attr'		=> array(
				'cols'	=> 42,
				'rows'	=> 6
				)
			)
		);
	?>
	</TD>
</TR>

</TABLE>
