<?php
$earliestTs = isset($NTS_VIEW['availability']['services'][$s->getId()]) ? $NTS_VIEW['availability']['services'][$s->getId()] : 0;
?>
<li>
<?php if( $earliestTs ) : ?>
	<h3><a href="<?php echo ntsLink::makeLink('-current-', 'select', array('id' => $s->getId()) ); ?>"><?php echo ntsView::objectTitle($s); ?></a></h3>
<?php else : ?>
	<h3><?php echo ntsView::objectTitle($s); ?></h3>
<?php endif; ?>
<p>
<?php if( strlen($s->getProp('price')) ) : ?> 
	<?php echo M('Price'); ?>: <b><?php echo ntsCurrency::formatServicePrice($s->getProp('price')); ?></b><br>
<?php endif; ?>

<?php if( $earliestTs ) : ?>
	<?php $t->setTimestamp( $earliestTs ); ?>
	<?php echo M('Nearest Availability'); ?>: <b><?php echo $t->formatDate(); ?> <?php echo $t->formatTime(); ?></b>
<?php else : ?>
	<?php echo M('Not Available Now'); ?>
<?php endif; ?>

<?php if( $s->getProp('description') ) : ?> 
	<p><?php echo $s->getProp('description'); ?>
<?php endif; ?>
</li>
