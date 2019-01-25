<?php
global $NTS_VIEW;
$ntsdb =& dbWrapper::getInstance();
$resourceId = $this->getValue( '_res_id' );
$t = new ntsTime;
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
				'title'	=> M('Something short and descriptive, like Summer Schedule, etc'),
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
					'class'	=> 'schedule',
					'prop'	=> 'title',
					'also'	=> array(
						'resource_id' => " = $resourceId"
						),
					),
				),
			)
		);
	?>
	</td>

	<th><?php echo M('Valid'); ?> *</th>
	<td>
	<?php
	$defaultFrom = $t->formatDate_Db();
	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'valid_from',
			'default'	=> $defaultFrom,
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
	- 
	<?php
	list( $year, $month, $day ) = ntsTime::splitDate( $defaultFrom );

	$t->setDateTime( $year + 1, $month, $day, 0, 0, 0 );
	$defaultTo = $t->formatDate_Db();

	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'valid_to',
			'default'	=> $defaultTo,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> 'Please enter the to date',
				),
			array(
				'code'		=> 'greaterEqualThan.php', 
				'error'		=> "This date can't be before the from date",
				'params'	=> array(
					'compareWithField' => 'valid_from',
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Capacity'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'capacity',
			'attr'		=> array(
				'size'	=> 3,
				),
			'default'	=> 1,
			'required'	=> 1,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required field'),
				),
			array(
				'code'		=> 'integer.php', 
				'error'		=> M('Numbers only'),
				),
			)
		);
	?> <?php echo M('Seats'); ?>
	</td>
</tr>
</table>

<table>
<tr>
<th style="width: 20em;"><?php echo M('Services'); ?></th>
<th style="width: 20em;"><?php echo M('Locations'); ?></th>
</tr>

<tr>
<td style="vertical-align: top;">
	<?php
	/* check how many services do we have */
	$sql =<<<EOT
	SELECT 
		id
	FROM 
		{PRFX}services
EOT;
	$result = $ntsdb->runQuery( $sql );
	$defaultServices = array();
	while( $e = $result->fetch() ){
		$defaultServices[] = $e['id'];
		}

	echo $this->makeInput (
	/* type */
		'services',
	/* attributes */
		array(
			'id'		=> '_service',
			'default'	=> $defaultServices,
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please choose at least one option'),
				)
			)
		);
	?>
</td>
<td style="vertical-align: top;">
	<?php
	echo $this->makeInput (
	/* type */
		'locations',
	/* attributes */
		array(
			'id'		=> '_location',
			'default'	=> NTS_SINGLE_LOCATION ? array( NTS_SINGLE_LOCATION ) : array(),
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please choose at least one option'),
				)
			)
		);
	?>
</td>
</tr>
</table>

<p>
<DIV CLASS="buttonBar">
<?php
echo $this->makePostParams(
	'-current-',
	'create',
	array(
		'_res_id' 	=> $resourceId,
		'_copy_from' 	=> $NTS_VIEW['copyFrom']
		)
	);
?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>