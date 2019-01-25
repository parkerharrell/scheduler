<div id="nts-time-selector">
<?php $t->setTimestamp( $dayTimes[0] ); ?>
<h3><?php echo $t->formatWeekday(); ?>, <?php echo $t->formatDate(); ?></h3>
<ul>
<?php foreach( $dayTimes as $ts ) : ?>
<?php 	$t->setTimestamp( $ts ); ?>
	<li>
	<a href="<?php echo ntsLink::makeLink('-current-', 'select', array('id' => $ts) ); ?>"><?php echo $t->formatTime(); ?></a>
<?php endforeach; ?>
</ul>
</div>
