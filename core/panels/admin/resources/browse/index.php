<?php
$ntsdb =& dbWrapper::getInstance();
?>
<table class="nts-listing">
<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<?php
	$restrictions = $e->getProp('_restriction');

	$schedulesCount = 0;
	$resId = $e->getId();
	$sql =<<<EOT
SELECT 
	COUNT(id) AS count 
FROM 
	{PRFX}schedules
WHERE
	resource_id = $resId
EOT;
	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$i = $result->fetch();
		$schedulesCount = $i['count'];
		}

list( $appsAdmins, $scheduleAdmins ) = $e->getAdmins( true );
?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<b><?php echo ntsView::objectTitle($e); ?></b>
<?php if( $e->getProp('description') ) : ?>
	<br><?php echo $e->getProp('description'); ?>
<?php endif; ?>

<?php if( $schedulesCount <= 0 ) : ?>
	<br><span class="alert"><?php echo M('No schedules configured!'); ?></span>
<?php endif; ?>

<?php if( count($appsAdmins) <= 0 ) : ?>
	<br><span class="alert"><?php echo M('No administrative users to manage appointments!'); ?></span>
<?php endif; ?>

<?php if( count($scheduleAdmins) <= 0 ) : ?>
	<br><span class="alert"><?php echo M('No administrative users to manage schedules!'); ?></span>
<?php endif; ?>

<?php if( in_array('suspended', $restrictions) ) : ?>
	<br><span class="alert"><?php echo M('Suspended'); ?></span>
<?php endif; ?>
	</td>
	<td>

<?php	if( in_array('suspended', $restrictions) ) : ?>
<?php
			echo ntsLink::printLink(
				array(
					'panel'		=> '-current-/../edit/edit',
					'action'	=> 'activate',
					'params'	=> array('_id' => $e->getId()),
					'title'		=> M('Activate'),
					'attr'		=> array(
						'class'	=> 'ok',
						),
					)
				);
?>
<?php	elseif( count($NTS_VIEW['entries']) > 1 ) : ?>
<?php
			echo ntsLink::printLink(
				array(
					'panel'		=> '-current-/../edit/edit',
					'action'	=> 'suspend',
					'params'	=> array('_id' => $e->getId()),
					'title'		=> M('Suspend'),
					'attr'		=> array(
						'class'	=> 'alert',
						),
					)
				);
?>
<?php	endif; ?>

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