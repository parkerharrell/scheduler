<?php
$displayColumns = 2;
?>

<table class="nts-listing">
<tr class="listing-header">
<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<th><?php echo M('Username'); ?></th>
<?php else: ?>
	<th><?php echo M('Email'); ?></th>
<?php endif; ?>
	<th><?php echo M('Full Name'); ?></th>
</tr>

<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">

<td>
<?php if( ! NTS_EMAIL_AS_USERNAME ) : ?>
	<b><?php echo $e->getProp('username'); ?></b>
	<br>
	<?php echo $e->getProp('email'); ?>
<?php else: ?>
	<b><?php echo $e->getProp('email'); ?></b>
<?php endif; ?>
</td>

<td>
	<?php echo ntsView::objectTitle($e); ?>
</td>
</tr>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td colspan="<?php echo $displayColumns; ?>" class="nts-row-actions">
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
			<?php if( $e->getId() != NTS_CURRENT_USERID ) : ?>
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
		<?php endif; ?>
	</td>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>