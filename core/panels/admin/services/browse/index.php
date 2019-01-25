<?php
$ntsdb =& dbWrapper::getInstance();
?>
<table class="nts-listing">
<?php $count = 0; ?>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<td>
		<b><?php echo $e->getProp('title'); ?></b>
<?php
	$schedulesCount = 0;
	$serviceId = $e->getId();
	$sql =<<<EOT
SELECT 
	COUNT( DISTINCT(id) ) AS count 
FROM 
	{PRFX}objectmeta
WHERE
	obj_class = "schedule" AND
	meta_name = "_service" AND
	meta_value = $serviceId
EOT;
	$result = $ntsdb->runQuery( $sql );
	if( $result ){
		$i = $result->fetch();
		$schedulesCount = $i['count'];
		}

	$pgs = $e->getPaymentGateways();
	$price = $e->getProp( 'price' );
?>
<?php if( $schedulesCount <= 0 ) : ?>
	<br><span class="alert"><?php echo M('No schedules configured!'); ?></span>
<?php endif; ?>
<?php if( $price && (count($pgs) <= 0) ) : ?>
	<br><span class="alert"><?php echo M('No payment gateways configured!'); ?></span>
<?php endif; ?>

	</td>
	<td>
<?php 	if( count($NTS_VIEW['entries']) > 1 ) : ?>
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
<?php 	endif; ?>
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