<?php
$return = $this->getValue('return');
$params['return'] = $return;

$id = $this->getValue('id');
$params['_id'] = $id;
?>
<?php echo M('Please provide the reject reason'); ?><br>
<?php
echo $this->makeInput (
/* type */
	'textarea',
/* attributes */
	array(
		'id'		=> 'reason',
		'attr'		=> array(
			'cols'	=> 48,
			'rows'	=> 4,
			),
		'default'	=> '',
		),
/* validators */
	array(
		)
	);
?>
<p>
<?php echo $this->makePostParams('-current-', 'reject', $params ); ?>
<input type="submit" VALUE="<?php echo M('Reject'); ?>">
