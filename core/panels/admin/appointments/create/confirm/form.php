<?php
global $NTS_VIEW;
$class = 'appointment';
$otherDetails = array(
	'service_id'	=> $this->getValue('service_id'),
	);
$om =& objectMapper::getInstance();
$fields = $om->getFields( $class, 'internal', true, $otherDetails  );
reset( $fields );
?>

<table>
<?php foreach( $fields as $f ) : ?>
<?php $c = $om->getControl( $class, $f[0], false ); ?>
<tr>
	<th><?php echo $c[0]; ?></th>
	<td>
	<?php
	echo $this->makeInput (
		$c[1],
		$c[2],
		$c[3]
		);
	?>
	</td>
</tr>
<?php endforeach; ?>

<tr>
	<td colspan="2">
<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
	<?php echo $this->makePostParams('-current-', 'reschedule' ); ?>
<?php else: ?>
	<?php echo $this->makePostParams('-current-', 'submit' ); ?>
<?php endif; ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Confirm Appointment'); ?>">
	</td>
</tr>
</table>