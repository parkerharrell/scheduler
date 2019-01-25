<?php
$resourceId = $this->getValue('resource_id');
$from = $this->getValue('from');
$defaults = array();
$defaults['starts_at_date'] = '';
$defaults['starts_at_time'] = 0;
if( $from ){
	$t = new ntsTime( $from );
	$defaults['starts_at_date'] = $t->formatDate_Db();
	$startDay = $t->getStartDay();
	$defaults['starts_at_time'] = $from - $startDay;
	}
?> 
<?php
echo $this->makeValidator (
	'checkIfEndAfterStart',
	array(
		'directCode'	=> dirname(__FILE__) . '/validate/checkIfEndAfterStart.php',
		'error'			=> 'The end time should be after the start',
		)
	);
?>
<?php
echo $this->makeValidator (
	'checkIfTimeoffOverlaps',
	array(
		'directCode'	=> dirname(__FILE__) . '/validate/checkIfTimeoffOverlaps.php',
		'error'		=> 'There already exists a timeoff that overlaps with this one',
		'params'	=> array(
			'resource_id'	=> $resourceId,
			),
		)
	);
?>
<table>
<tr>
	<th>&nbsp;</th>
	<th><?php echo M('Date'); ?></th>
	<th><?php echo M('Time'); ?></th>
</tr>

<tr>
	<th><?php echo M('From'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'starts_at_date',
			'default'	=> $defaults['starts_at_date'],
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the from date',
				),
			)
		);
	?>
	</td>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'	=> 'starts_at_time',
			'default'	=> $defaults['starts_at_time'],
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('To'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'ends_at_date',
			'default'	=> $defaults['starts_at_date'],
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the to date',
				),
			)
		);
	?>
	</td>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'	=> 'ends_at_time',
			'default'	=> $defaults['starts_at_time'],
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th colspan="3"><?php echo M('Description'); ?></th>
</tr>
<tr>
	<td colspan="3">
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 32,
				'rows'	=> 4,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

</table>

<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create', array('resource_id' => $resourceId)); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>