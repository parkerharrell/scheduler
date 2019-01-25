<?php
$t = new ntsTime;
?>
<b><?php echo M('Timeoff'); ?></b>

<ul>
<?php foreach( $NTS_VIEW['entries'] as $e ) : ?>
<?php
	$t->setTimestamp( $e['starts_at'] );
	$startTime = $t->formatDate() . ' ' . $t->formatTime();
	$t->setTimestamp( $e['ends_at'] );
	$endTime = $t->formatDate() . ' ' . $t->formatTime();
?>

<li>
<h2><?php echo $startTime; ?> - <?php echo $endTime; ?></h2>
<?php if( $e['description'] ) : ?>
	<?php echo $e['description']; ?>
<?php endif; ?>

</li>
<?php endforeach; ?>
</ul>