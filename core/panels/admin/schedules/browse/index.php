<?php
global $NTS_VIEW;
$showResource = $NTS_VIEW['FIXED_RESOURCE'] ? false : true;
$colspan = $showResource ? 4 : 3;
$t = new ntsTime;
?>

<?php if( ! count($NTS_VIEW['entries']) ) : ?>
	<p><?php echo M('None'); ?>
<?php else : ?>
<p>
<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Title'); ?></th>
<?php if( $showResource ) : ?>
	<th><?php echo M('Bookable Resource'); ?></th>
<?php endif; ?>
	<th><?php echo M('Valid From Date'); ?></th>
	<th><?php echo M('Valid To Date'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $h ) : ?>
<?php $e = $h->getByArray(); ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<b><?php echo $e['title']; ?></b>
	</td>
<?php if( $showResource ) : ?>
	<td>
<?php
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $e['resource_id'] );
?>
	<?php echo ntsView::objectTitle($resource); ?>
	</td>
<?php endif; ?>
	<td>
		<?php 
		$t = new ntsTime();
		$t->setDateDb( $e['valid_from'] );
		echo $t->formatDate();
		?>
	</td>
	<td>
		<?php
		$t = new ntsTime();
		$t->setDateDb( $e['valid_to'] );
		echo $t->formatDate();
		?>
	</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="<?php echo $colspan; ?>" class="nts-row-actions">

<?php	if( in_array($e['resource_id'], $NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/../edit', '', array('_id' => $e['id']) ); ?>"><?php echo M('Edit'); ?></a>
	<?php if( ! NTS_APP_LITE ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/../edit/delete', '', array('_id' => $e['id']) ); ?>"><?php echo M('Delete'); ?></a>
	<?php endif; ?>
<?php	elseif( in_array($e['resource_id'], $NTS_VIEW['RESOURCE_SCHEDULE_VIEW']) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/../edit', '', array('_id' => $e['id']) ); ?>"><?php echo M('View'); ?></a>
<?php	endif; ?>
<?php if( (! NTS_APP_LITE) && $NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) : ?>
	<a href="<?php echo ntsLink::makeLink('-current-/../edit/copy', '', array('_id' => $e['id']) ); ?>"><?php echo M('Copy'); ?></a>
<?php endif; ?>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>
<?php endif; ?>
