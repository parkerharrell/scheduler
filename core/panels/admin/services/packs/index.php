<p>
<a class="ok" href="<?php echo ntsLink::makeLink('-current-/create'); ?>"><?php echo M('Appointment Pack'); ?>: <?php echo M('Create'); ?></a>

<p>
<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Title'); ?></th>
	<th><?php echo M('Pricing'); ?></th>
	<th><?php echo M('Appointments'); ?></th>
	<th>&nbsp;</th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('_id' => $e->getId()) ); ?>"><?php echo $e->getProp('title'); ?></a>
	</td>

	<td>
	<?php
	$myValue = array();
	$rawValue = $e->getProp('discount'); 
	if( preg_match('/discount/', $rawValue)){
		$myValue = explode( ':', $rawValue );
		}
	elseif( preg_match('/price/', $rawValue)){
		$myValue = explode( ':', $rawValue );
		}
	else{
		$myValue = array( $rawValue, '' );
		}
	?>
		<?php if( $myValue[0] == 'discount' ) : ?>
			<?php echo M('Discount'); ?> <b><?php echo $myValue[1]; ?>%</b>
		<?php elseif( $myValue[0] == 'price' ) : ?>
			<?php echo M('Total Price'); ?> <b><?php echo ntsCurrency::formatServicePrice($myValue[1]); ?></b>
		<?php elseif( $myValue[0] == 'onefree' ) : ?>
			<?php echo M('One Appointment Free'); ?>
		<?php endif; ?>
	</td>

	<td>
		<?php
		$servicesString = $e->getProp( 'services' );
		$packs = ntsLib::splitPackServicesString( $servicesString );
		?>
		<?php echo count( $packs ); ?>
	</td>

	<td>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'up', array('pack' => $e->getId()) ); ?>"><?php echo M('Up'); ?></a>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'down', array('pack' => $e->getId()) ); ?>"><?php echo M('Down'); ?></a>
	</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="5" class="nts-row-actions">
		<div>
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-/delete', '', array('_id' => $e->getId()) ); ?>"><?php echo M('Delete'); ?></a>
		</div>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>