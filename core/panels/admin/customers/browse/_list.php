<table class="nts-listing" id="nts-customer-listing">
<tr class="listing-header">
<?php if( $displayActions ) : ?>
	<th style="text-align: center;">
	<input type="checkbox" id="checker" name="checker" onClick="ntsMarkAllRows('nts-customer-listing', this.checked);">
	</th>
<?php endif; ?>
<?php foreach( $fields as $f ) : ?>
	<th><?php echo $f[1]; ?></th>
<?php endforeach; ?>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<?php
$obj = new ntsUser();
$obj->setByArray( $e );
$restrictions = $obj->getProp('_restriction');

reset( $fields );
?>

<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
<?php if( $displayActions ) : ?>
	<td>
	<input type="checkbox" id="id[]" name="id[]" VALUE="<?php echo $obj->getId(); ?>">
	</td>
<?php endif; ?>
	<?php
	$editPanel = 'admin/customers/edit';
	?>

<?php foreach( $fields as $f ) : ?>
	<td>
<?php
switch( $f[0] ){
	case 'username' :
?>

<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<b><?php echo $obj->getProp('username'); ?></b>
	<br><?php echo $obj->getProp('email'); ?>
<?php else: ?>
	<b><?php echo $obj->getProp('email'); ?></b>
<?php endif; ?>
<?php break; ?>

<?php case 'email' : ?>
	<b><?php echo $obj->getProp('email'); ?></b>
<?php break; ?>

<?php case 'full_name' : ?>
	<?php echo $obj->getProp('first_name'); ?> <?php echo $obj->getProp('last_name'); ?>
<?php break; ?>

<?php case 'nts_user_status' : ?>
	<?php
		$statusOk = true;
		if( $restrictions ){
			$statusOk = false;
			if( in_array('email_not_confirmed', $restrictions) )
				$status = M('Email Not Confirmed');
			elseif( in_array('not_approved', $restrictions) )
				$status = M('Not Approved');
			elseif( in_array('suspended', $restrictions) )
				$status = M('Suspended');
			else
				$status = M('N/A');
			}
		else {
			$status = M('Active');
			}
	?>
	<?php if( $statusOk ) : ?>
		<span class="ok">
	<?php else: ?>
		<span class="alert">
	<?php endif; ?>
	<?php echo $status; ?></span>
<?php break; ?>

<?php 	default: ?>
<?php
		switch( $f[2] ){
			case 'checkbox' :
				echo $obj->getProp($f[0]) ? M('Yes') : M('No');
				break;
			default:
				echo $obj->getProp($f[0]);
				break;
			}
?>
<?php 	break; ?>

<?php
	}
?>
	</td>
<?php endforeach; ?>
</tr>

<?php if( $displayActions ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td class="nts-row-actions">&nbsp;</td>
	<td colspan="<?php echo count($fields); ?>" class="nts-row-actions">
<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit',
				'params'	=> array('_id' => $obj->getId()),
				'title'		=> M('Edit'),
				)
			);
?>

<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> 'admin/appointments/browse',
				'params'	=> array('customer' => $obj->getId()),
				'title'		=> M('Appointments'),
				)
			);
?>

<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> 'admin/appointments/manage',
				'params'	=> array(
					'customer'		=> $obj->getId(),
					'viewPeriod'	=> 'week',
					),
				'title'		=> M('Create Appointment'),
				'attr'		=> array(
					'class'	=> 'ok',
					),
				)
			);
?>
	
<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit/delete',
				'params'	=> array('_id' => $obj->getId()),
				'title'		=> M('Delete'),
				'attr'		=> array(
					'class'	=> 'alert',
					),
				)
			);
?>
	</td>
</tr>
<?php endif; ?>

<?php $count++; ?>
<?php endforeach; ?>

</table>