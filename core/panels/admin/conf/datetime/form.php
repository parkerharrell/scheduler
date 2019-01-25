<?php
/* tags */
$tm =& ntsEmailTemplateManager::getInstance();
$tags = $tm->getTags( 'common-header-footer' );
?>
<TABLE>
<tr>
	<th><?php echo M('Time Unit, min'); ?></th>
	<td>
	<?php
	$timeunitOptions = array(
		array( 3, 3 ),
		array( 5, 5 ),
		array( 10, 10 ),
		array( 15, 15 ),
		array( 30, 30 ),
		array( 60, 60 ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'timeUnit',
			'options'	=> $timeunitOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<th><?php echo M('Company Timezone'); ?></th>
	<td>
	<?php
	$timezoneOptions = ntsTime::getTimezones();

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'companyTimezone',
			'options'	=> $timezoneOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<?php
	$t = new ntsTime;
	$timeString = $t->formatDate() . ' ' . $t->formatTime();
	?>
	<i><?php echo M('If set correctly, your company time is now <b>{TIME_STRING}</b>. If it is wrong, please set your company correct time zone.', array('TIME_STRING' => $timeString)); ?></i>
	</td>
</tr>

<tr>
	<th><?php echo M('Date Format'); ?></th>
	<TD>
	<?php
	$dateFormats = array( 'd/m/Y', 'd-m-Y', 'n/j/Y', 'Y/m/d', 'd.m.Y', 'j M Y' );
	$dateFormatsOptions = array();
	$t = new ntsTime;
	reset( $dateFormats );
	foreach( $dateFormats as $f ){
		$t->dateFormat = $f;
		$dateFormatsOptions[] = array( $f, $t->formatDate() );
		}

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'dateFormat',
			'options'	=> $dateFormatsOptions,
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th><?php echo M('Time Format'); ?></th>
	<TD>
	<?php
	$timeFormats = array( 'H:i', 'g:i A');
	$timeFormatsOptions = array();
	reset( $timeFormats );
	foreach( $timeFormats as $f ){
		$timeFormatsOptions[] = array( $f, date($f) );
		}

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'timeFormat',
			'options'	=> $timeFormatsOptions,
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th><?php echo M('Week Starts On'); ?></th>
	<TD>
	<?php
	$weekStartsOnOptions = array(
		array( 1, M('Monday') ),
		array( 2, M('Tuesday') ),
		array( 3, M('Wednesday') ),
		array( 4, M('Thursday') ),
		array( 5, M('Friday') ),
		array( 6, M('Saturday') ),
		array( 7, M('Sunday') ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'weekStartsOn',
			'options'	=> $weekStartsOnOptions,
			)
		);
	?>
	</TD>
</TR>

<tr>
	<th><?php echo M('Months To Show In Calendar'); ?></th>
	<td>
	<?php
	$monthsToShowOptions = array(
		array( 1, 1 ),
		array( 2, 2 ),
		array( 3, 3 ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'monthsToShow',
			'options'	=> $monthsToShowOptions,
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Days To Show In Calendar For Internal User'); ?></th>
	<td>
	<?php
	$daysToShowOptions = array(
		array( 1, 1 ),
		array( 2, 2 ),
		array( 3, 3 ),
		array( 5, 5 ),
		array( 7, 7 ),
		array( 10, 10 ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'daysToShowAdmin',
			'options'	=> $daysToShowOptions,
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Days To Show In Calendar For External User'); ?></th>
	<td>
	<?php
	$daysToShowOptions = array(
		array( 1, 1 ),
		array( 2, 2 ),
		array( 3, 3 ),
		array( 5, 5 ),
		array( 7, 7 ),
		array( 10, 10 ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'daysToShowCustomer',
			'options'	=> $daysToShowOptions,
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Max Time Measure In Duration Display'); ?></th>
	<td>
	<?php
	$limitOptions = array(
		array( 'minute', M('Minute') ),
		array( 'hour', M('Hour') ),
		array( 'day', M('Day') ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'limitTimeMeasure',
			'options'	=> $limitOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<i><?php echo M('If set to Minute for example, it will show 90 Minutes rather than 1 Hour 30 Minutes.'); ?></i>
	</td>
</tr>

</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>