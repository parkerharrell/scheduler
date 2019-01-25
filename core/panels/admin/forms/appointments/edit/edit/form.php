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
					'class'	=> 'form',
					'skipMe'	=> 1
					),
				),
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