<table class="nts-listing">

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<b><?php echo ntsView::objectTitle($e); ?></b>
	<br><?php echo $e->getProp('description'); ?>
	</td>
	<td>
	<?php if( count($NTS_VIEW['entries']) > 1 ) : ?>
<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit/edit',
				'action'	=> 'up',
				'params'	=> array('_id' => $e->getId()),
				'title'		=> M('Up'),
				'attr'		=> array(
					'class'	=> 'ok',
					),
				)
			);
?>

<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit/edit',
				'action'	=> 'down',
				'params'	=> array('_id' => $e->getId()),
				'title'		=> M('Down'),
				'attr'		=> array(
					'class'	=> 'ok',
					),
				)
			);
?>
	<?php endif; ?>
	</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="2" class="nts-row-actions">
<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit',
				'params'	=> array('_id' => $e->getId()),
				'title'		=> M('Edit'),
				)
			);
?>
		<?php if( count($NTS_VIEW['entries']) > 1 ) : ?>
<?php
		echo ntsLink::printLink(
			array(
				'panel'		=> '-current-/../edit/delete',
				'params'	=> array('_id' => $e->getId()),
				'title'		=> M('Delete'),
				'attr'		=> array(
					'class'	=> 'alert',
					),
				)
			);
?>
		<?php endif; ?>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>