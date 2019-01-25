<DIV ID="requireOptions-Textarea">
<TABLE>
<TR>
	<TH><?php echo M('Textarea Columns'); ?> *</TH>
	<TD>
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-cols',
			'attr'		=> array(
				'size'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the columns',
				),
			array(
				'code'		=> 'number.php', 
				'error'		=> 'Only numbers are allowed for this field',
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<TH><?php echo M('Textarea Rows'); ?> *</TH>
	<TD>
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-rows',
			'attr'		=> array(
				'size'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the rows',
				),
			array(
				'code'		=> 'number.php', 
				'error'		=> 'Only numbers are allowed for this field',
				),
			)
		);
	?>
	</TD>
</TR>

<TR>
	<TH><?php echo M('Default Value'); ?></TH>
	<TD>
	<?php
	echo $this->makeInput(
		'textarea',
		array(
			'id'		=> 'default_value-textarea',
			'attr'		=> array(
				'cols'	=> 32,
				'rows'	=> 3,
				),
			)
		);
	?>
	</TD>
</TR>

</TABLE>
</DIV>

<DIV ID="requireOptions-Text">
<TABLE>
<TR>
	<TH><?php echo M('Text Field Size'); ?> *</TH>
	<TD>
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'attr-size',
			'attr'		=> array(
				'size'	=> 4,
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
				'code'		=> 'number.php', 
				'error'		=> M('Numbers only'),
				),
			)
		);
	?>
	</TD>
</TR>
<TR>
	<TH><?php echo M('Default Value'); ?></TH>
	<TD>
	<?php
	echo $this->makeInput(
		'text',
		array(
			'id'		=> 'default_value-text',
			'attr'		=> array(
				'size'	=> 32,
				),
			)
		);
	?>
	</TD>
</TR>
</TABLE>
</DIV>

<DIV ID="requireOptions-Select">
<TABLE>
<TR>
	<TH><?php echo M('Select Options'); ?> *</TH>
</tr>
<tr>
	<TD>
	<?php
	echo $this->makeInput(
		'textareaArray',
		array(
			'id'		=> 'attr-options',
			'help'		=> M('Each option on new line') . '. ' . M('Add a star sign (*) before the default value') . '.',
			'attr'		=> array(
				'cols'	=> 24,
				'rows'	=> 4,
				),
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the field options',
				),
			)
		);
	?>
	</TD>
</TR>
</TABLE>
</DIV>

<DIV ID="requireOptions-Checkbox">
<TABLE>
<TR>
	<TH><?php echo M('Default Value'); ?></TH>
	<TD>
	<?php
	echo $this->makeInput(
		'checkbox',
		array(
			'id'		=> 'default_value-checkbox',
			)
		);
	?>
	</TD>
</TR>
</TABLE>
</DIV>
