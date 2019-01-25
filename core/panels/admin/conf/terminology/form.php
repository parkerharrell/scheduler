<?php
$defaults = $this->getDefaults();
reset( $defaults );
$count = 1;
?>
<TABLE>
<?php foreach( $defaults as $dv ) : ?>
<tr>
	<th><?php echo M($dv, array(), true); ?> *</th>
	<TD>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'term-' . $count,
			'attr'		=> array(
				'size'	=> 42,
				),
			'required'	=> 1,
			'default'	=> $defaults[ $count ],
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
<?php $count++; ?>
<?php endforeach; ?>
</TABLE>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>