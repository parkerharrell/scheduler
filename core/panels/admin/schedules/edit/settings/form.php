<?php
$id = $this->getValue( 'id' );
$resourceId = $this->getValue( 'resource_id' );

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
				'title'	=> M('Something short and descriptive, like Summer Schedule, etc'),
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
	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'valid_from',
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
	echo $this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'valid_to',
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

<table class="ntsContainerTable" style="width: auto;">
<tr>
<th><h3><?php echo M('Services'); ?></h3></th>
<th><h3><?php echo M('Locations'); ?></h3></th>
</tr>

<tr>
<td style="vertical-align: top;">
	<?php
	echo $this->makeInput (
	/* type */
		'services',
	/* attributes */
		array(
			'id'		=> '_service',
			'default'	=> array(),
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
			'default'	=> NTS_SINGLE_LOCATION ? array( NTS_SINGLE_LOCATION => array('seats' => 1) ) : array(),
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

<?php if( ! $this->readonly ) : ?>
	<p>
	<DIV CLASS="buttonBar">
	<?php echo $this->makePostParams('-current-', 'update' ); ?>
	<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
	</DIV>
<?php endif; ?>
