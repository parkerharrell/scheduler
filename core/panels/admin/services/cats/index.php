<p>
<a class="ok" href="<?php echo ntsLink::makeLink('-current-/create'); ?>"><?php echo M('Category'); ?>: <?php echo M('Create'); ?></a>

<p>
<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Title'); ?></th>
	<th><?php echo M('Services'); ?></th>
	<th>&nbsp;</th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('_id' => $e['id']) ); ?>"><?php echo $e['title']; ?></a>
	</td>
	<td>
		<a href="<?php echo ntsLink::makeLink('-current-/edit/services', '', array('_id' => $e['id']) ); ?>"><?php echo $e['count_services']; ?></a>
	</td>
	<td>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'up', array('cat' => $e['id']) ); ?>"><?php echo M('Up'); ?></a>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-', 'down', array('cat' => $e['id']) ); ?>"><?php echo M('Down'); ?></a>
	</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="5" class="nts-row-actions">
		<div>
		<a href="<?php echo ntsLink::makeLink('-current-/delete', '', array('_id' => $e['id']) ); ?>"><?php echo M('Delete'); ?></a>
		</div>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>