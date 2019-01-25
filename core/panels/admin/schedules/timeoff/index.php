<?php
global $NTS_VIEW;
$showResource = $NTS_VIEW['FIXED_RESOURCE'] ? false : true;
$colspan = $showResource ? 4 : 3;
$now = time();
?>
<?php if( $NTS_VIEW['RESOURCE_SCHEDULE_EDIT'] ) : ?>
	<a class="ok" href="<?php echo ntsLink::makeLink( '-current-/create' ); ?>"><?php echo M('Timeoff'); ?>: <?php echo M('Create'); ?></a>
<?php endif; ?>

<table class="nts-listing">
<?php if( $NTS_VIEW['newTimeoffs'] ) : ?>
<tr>
	<td colspan="<?php echo $colspan; ?>">
	<h3><?php echo M('Next Timeoffs'); ?></h3>
	</td>
</tr>
<tr class="listing-header">
<?php if( $showResource ) : ?>
	<th><?php echo M('Bookable Resource'); ?></th>
<?php endif; ?>
	<th><?php echo M('From'); ?></th>
	<th><?php echo M('To'); ?></th>
	<th><?php echo M('Description'); ?></th>
</tr>
<?php endif; ?>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['newTimeoffs'] as $to ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<?php
	$t = new ntsTime( $to['starts_at'] );
	$fromTitle = $t->formatWeekdayShort() . ', ' . $t->formatDate() . '<br>' . $t->formatTime();

	$t = new ntsTime( $to['ends_at'] );
	$toTitle = $t->formatWeekdayShort() . ', ' . $t->formatDate() . '<br>' . $t->formatTime();

	$nowOn = ( ($now >= $to['starts_at']) && ($now <= $to['ends_at']) ) ? true : false;
	?>
<?php if( $showResource ) : ?>
	<td>
		<?php
		$thisRes = ntsObjectFactory::get( 'resource' );
		$thisRes->setId( $to['resource_id'] );
		?>
		<?php echo $thisRes->getProp('title'); ?>
	</td>
<?php endif; ?>
	<td>
		<?php if( $nowOn ) : ?>
			<b class="ok">
		<?php else : ?>
			<b>
		<?php endif; ?>
		<?php echo $fromTitle; ?>
		</b>
	</td>
	<td>
		<?php if( $nowOn ) : ?>
			<b class="ok">
		<?php else : ?>
			<b>
		<?php endif; ?>
		<?php echo $toTitle; ?>
		</b>
	</td>
	<td><?php echo $to['description']; ?></td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="<?php echo $colspan; ?>" class="nts-row-actions">
<?php	if( in_array($to['resource_id'], $NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) ) : ?>
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-', 'delete', array('id' => $to['id']) ); ?>"><?php echo M('Delete'); ?></a>
<?php	endif; ?>

	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>

<?php if( $NTS_VIEW['oldTimeoffs'] ) : ?>
<tr>
	<td colspan="<?php echo $colspan; ?>">
	<h3><?php echo M('Old Timeoffs'); ?></h3>
	</td>
</tr>
<tr class="listing-header">
<?php if( $showResource ) : ?>
	<th><?php echo M('Bookable Resource'); ?></th>
<?php endif; ?>
	<th><?php echo M('From'); ?></th>
	<th><?php echo M('To'); ?></th>
	<th><?php echo M('Description'); ?></th>
</tr>
<?php endif; ?>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['oldTimeoffs'] as $to ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<?php
	$t = new ntsTime( $to['starts_at'] );
	$fromTitle = $t->formatWeekdayShort() . ', ' . $t->formatDate() . '<br>' . $t->formatTime();

	$t = new ntsTime( $to['ends_at'] );
	$toTitle = $t->formatWeekdayShort() . ', ' . $t->formatDate() . '<br>' . $t->formatTime();

	$nowOn = ( ($now >= $to['starts_at']) && ($now <= $to['ends_at']) ) ? true : false;
	?>
<?php if( $showResource ) : ?>
	<td>
		<?php
		$thisRes = ntsObjectFactory::get( 'resource' );
		$thisRes->setId( $to['resource_id'] );
		?>
		<?php echo $thisRes->getProp('title'); ?>
	</td>
<?php endif; ?>
	<td>
		<?php if( $nowOn ) : ?>
			<b class="ok">
		<?php else : ?>
			<b>
		<?php endif; ?>
		<?php echo $fromTitle; ?>
		</b>
	</td>
	<td>
		<?php if( $nowOn ) : ?>
			<b class="ok">
		<?php else : ?>
			<b>
		<?php endif; ?>
		<?php echo $toTitle; ?>
		</b>
	</td>
	<td><?php echo $to['description']; ?></td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="<?php echo $colspan; ?>" class="nts-row-actions">
<?php	if( in_array($to['resource_id'], $NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) ) : ?>
		<a class="alert" href="<?php echo ntsLink::makeLink('-current-', 'delete', array('id' => $to['id']) ); ?>"><?php echo M('Delete'); ?></a>
<?php	endif; ?>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>
