<?php
$service = $this->getValue( 'service' );
?>
<tr>
<th>
<?php echo M('Recurring'); ?>
</th>

<td>
<?php
$allOptions = array(
	'd'		=> M('Every Day'),
	'2d'	=> M('Every Other Day'),
	'w'		=> M('Every Week'),
	'2w'	=> M('Every Fortnight'),
	'3w'	=> M('Every 3 Weeks'),
	'm'		=> M('Every Month'),
	'6w'	=> M('Every 6 Weeks'),
	);

$myOptions = $service->getProp( 'recur_options' );
$myOptions = explode( '-', $myOptions );

$recurOptions = array();
reset( $allOptions );
foreach( $allOptions as $k => $v ){
	if( in_array($k, $myOptions) )
		$recurOptions[] = array( $k, $v );
	}

echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'recur-every',
		'options'	=> $recurOptions,
		'attr'		=> array(
			),
		)
	);
?>
</td>
</tr>

<tr>
<th>
<?php echo M('Appointments In Total'); ?>
</th>

<td>
<?php
$maxTotalRecur = $service->getProp( 'recur_total' );
$recurTotalOptions = array();
for( $i = 2; $i <= $maxTotalRecur; $i++ ){
	$recurTotalOptions[] = array( $i, $i );
	}

echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'recur-total',
		'options'	=> $recurTotalOptions,
		'attr'		=> array(
			),
		)
	);
?>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>
<?php echo $this->makePostParams('-current-', 'select' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Continue With Recurring Appointments'); ?>">
</td>
</tr>
