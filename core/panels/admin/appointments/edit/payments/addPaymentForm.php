<?php
echo $this->makeInput (
/* type */
	'text',
/* attributes */
	array(
		'id'		=> 'amount',
		'attr'		=> array(
			'size'	=> 4,
			),
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
<?php echo $this->makePostParams('-current-', 'add' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Add'); ?>">
