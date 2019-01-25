<TABLE>
<tr>
	<th><?php echo M('Web Page Title'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'	=> 'htmlTitle',
			'attr'	=> array(
				'size'	=> 48
				),
			)
		);
	?>
	</td>
</tr>
<tr>
	<th><?php echo M('Keep Cancelled/Rejected Appointments In Database'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'keepCancelledApps',
			)
		);
	?>
	</td>
</tr>
<?php
$ntsdb =& dbWrapper::getInstance();
$cancelledCount = 0;
$sql =<<<EOT
SELECT
	COUNT({PRFX}appointments.id) AS count 
FROM
	{PRFX}appointments
WHERE
	cancelled = 1
EOT;

$result = $ntsdb->runQuery( $sql );
if( $result ){
	if( $e = $result->fetch() ){
		$cancelledCount = $e['count'];
		}
	}
?>
<?php if( $cancelledCount > 0 ) : ?>
<tr>
<td colspan="2">
<?php echo M('Cancelled'); ?>: <?php echo $cancelledCount; ?> <a href="<?php echo ntsLink::makeLink('admin/appointments/edit/purge_cancelled', '', array(), true); ?>"><?php echo M('Purge'); ?>?</a>
</td>
</tr>
<?php endif; ?>
<tr>
	<th><?php echo M('Show Session Duration For Clients'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'showSessionDuration',
			)
		);
	?>
	</td>
</tr>
<tr>
	<th><?php echo M('Require Clients Give Appointment Cancellation Reason'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'requireCancelReason',
			)
		);
	?>
	</td>
</tr>
<tr>
	<th><?php echo M('Selection Display Style'); ?></th>
	<td>
	<?php
	$selectOptions = array(
		array( 'list', M('List') ),
		array( 'dropdown', M('Drop Down') ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'selectStyle',
			'options'	=> $selectOptions
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Max Number Of Appointments In Pack'); ?></th>
	<td>
	<?php
	$packOptions = array();
	for( $i = 5; $i <= 15; $i+=2 ){
		$packOptions[] = array( $i, $i );
		}

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'maxAppsInPack',
			'options'	=> $packOptions,
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('List Appointment Packs Above Regular Services'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'showPacksAbove',
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('CSV Field Delimiter'); ?></th>
	<td>
	<?php
	$csvOptions = array(
		array( ',', ',' ),
		array( ';', ';' ),
		);

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'csvDelimiter',
			'options'	=> $csvOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<i><?php echo M('This may depend on your Excel regional settings'); ?></i>
	</td>
</tr>

<tr>
	<th><?php echo M('Attach Ical File To Notification Email'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'		=> 'attachIcal',
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('URL To Return After Appointment Request'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'	=> 'returnAfterRequest',
			'attr'	=> array(
				'size'	=> 48
				),
			)
		);
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<i><?php echo M('Leave this empty to return to hitAppoint start page'); ?></i>
	</td>
</tr>

</table>

<p>
<DIV CLASS="buttonBar">
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</DIV>