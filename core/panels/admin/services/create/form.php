<table class="ntsContainerTable">
<tr>
	<td style="vertical-align: top; width: 50%;">

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
					'class'	=> 'service',
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th colspan="2"><?php echo M('Description'); ?></th>
</tr>
	<td colspan="2">
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 42,
				'rows'	=> 6,
				),
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Duration'); ?></th>
	<td>
	<div id="nts-duration" style="display: inline;">
	<?php
	echo $this->makeInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'		=> 'duration',
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterThan.php', 
				'error'		=> M('Required field'),
				'params'	=> array(
					'compareWith'	=> 0,
					)
				),
			)
		);
	?>
	</div>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'until_closed',
			'attr'	=> array(
				'onClick'	=> 'ntsElementToggle( \'nts-duration\', this.checked, \'inline\' ); this.form["duration_qty_hour.value"] = 1; this.form["duration_qty_min"].value = 0;',
				),
			)
		);
	?>
	<?php echo M('Until Closed'); ?>
	<SCRIPT LANGUAGE="JavaScript">
		if( document.forms["<?php echo $this->getName(); ?>"]["until_closed"].checked ){
			ntsElementHide( "nts-duration" );
			document.forms["<?php echo $this->getName(); ?>"]["duration_qty_hour"].value = 1;
			document.forms["<?php echo $this->getName(); ?>"]["duration_qty_min"].value = 0;
			}
		else
			ntsElementShow( "nts-duration", "inline" );
	</SCRIPT>
	</td>
</tr>

<tr>
	<th><?php echo M('Lead In'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'		=> 'lead_in',
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Lead Out'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/HourMinute',
	/* attributes */
		array(
			'id'		=> 'lead_out',
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Price'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'	=> 'price',
			'attr'	=> array(
				'size'	=> 4,
				)
			)
		);
	?>
	</td>
</tr>
</table>

	</td>

	<td style="vertical-align: top; width: 50%;">

<h3><?php echo M('Availability'); ?></h3>

<table>
<tr>
	<th><?php echo M('Min Advance Booking'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'min_from_now',
			'attr'		=> array(
				),
			'default'	=> 3 * 60 * 60, // 3 hours
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Max Advance Booking'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'max_from_now',
			'attr'		=> array(
				),
			'default'	=> 2 * 7 * 24 * 60 * 60, // 2 weeks
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterEqualThan.php', 
				'error'		=> M('This should not be smaller than the min advance booking'),
				'params'	=> array(
					'compareWithField'	=> 'min_from_now',
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Cancellation/Reschedule Deadline'); ?> *</th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'period/MinHourDayWeek',
	/* attributes */
		array(
			'id'		=> 'min_cancel',
			'attr'		=> array(
				),
			'default'	=> 1 * 24 * 60 * 60, // 1 day
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>
</table>

<p>
<table>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'pack_only',
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
	<th><?php echo M('Available In Packs Only'); ?></th>
</tr>
<tr>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'class_type',
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
	<th><?php echo M('Class/Lesson'); ?></th>
</tr>
<tr>
<td></td>
<td>
<i><?php echo M('If more than one seat available, then all appointments should start at the same time. Otherwise, any overlapping allowed.'); ?></i>
</td>
</tr>
</table>
	
	</td>
</tr>
</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'create' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
</DIV>