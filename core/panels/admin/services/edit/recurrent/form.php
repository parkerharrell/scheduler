<table>
<tr>
<th>
<?php echo M('Recurring Options'); ?>
</th>
<td>
<?php
$recurOptions = array(
	array( 'd', M('Every Day') ),
	array( '2d', M('Every Other Day') ),
	array( 'w', M('Every Week') ),
	array( '2w', M('Every Fortnight') ),
	array( '3w', M('Every 3 Weeks') ),
	array( 'm', M('Every Month') ),
	array( '6w', M('Every 6 Weeks') ),
	);

echo $this->makeInput (
/* type */
	'checkboxSetGlue',
/* attributes */
	array(
		'id'		=> 'recur_options',
		'options'	=> $recurOptions,
		),
	/* validators */
	array(
		array(
			'code'		=> 'notEmpty.php', 
			'error'		=> M('Please choose at least one option'),
			),
		)
	);
?>
</td>
</tr>

<tr>
<th>
<?php echo M('Max Number Of Recurring Appointments'); ?>
</th>
<td>
<?php
$totalOptions = array();
for( $i = 1; $i <= 30; $i++ ){
	$totalOptions[] = array( $i, $i );
	}

echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'recur_total',
		'options'	=> $totalOptions,
		)
	);
?>
</td>
</tr>

<tr>
<td colspan="2">
<i><?php echo M('If set to 1, the recurring option will be disabled'); ?></i>
</td>
<td>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'update' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</td>
</table>
