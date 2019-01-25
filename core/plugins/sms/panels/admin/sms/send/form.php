<TABLE>
<tr>
	<th>Mobile Phone *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'to',
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
	<th>Message *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'message',
			'default'	=> '',
			'attr'		=> array(
				'cols'	=> 32,
				'rows'	=> 4,
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
<td>
&nbsp;
</td>
<td>
<?php echo $this->makePostParams('-current-', 'send' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Send'); ?>">
</td>
</tr>
</TABLE>
