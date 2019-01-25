<?php
$t = $NTS_VIEW['t'];
$confirmParams = array();
$confirmParams['time'] = $NTS_VIEW['ts'];
$confirmParams['seats'] = 1;
$confirmParams['location'] = $NTS_VIEW['location']->getId();
if( $NTS_VIEW['customer'] )
	$confirmParams['customer'] = $NTS_VIEW['customer']->getId();
if( $NTS_VIEW['RESCHEDULE'] )
	$confirmParams['reschedule'] = $NTS_VIEW['RESCHEDULE']['obj']->getId();
$confirmParams['viewMode'] = '';
?>

<?php if( ! $NTS_VIEW['RESCHEDULE'] ) : ?>
	<h3><?php echo M('Create Appointment'); ?></h3>
<?php else : ?>
	<h3><?php echo M('Change Appointment'); ?></h3>
<?php endif; ?>
<b><?php echo $t->formatDate(); ?> <?php echo $t->formatTime(); ?></b><br>

<?php echo M('Bookable Resources'); ?>

<ul class="nts-boxed-selector">
<?php foreach( $NTS_VIEW['managedResources'] as $res ) : ?>
<li>
<?php
	$linkView = ntsView::objectTitle( $res );
	$confirmParams['resource'] = $res->getId();
	$linkClass = 'service-h';
	$linkClass .= ( in_array($res->getId(), $NTS_VIEW['SELECTABLE_RESOURCES']) ) ? ' available' : ' not-available';
?>
<a class="nts-ajax-link <?php echo $linkClass; ?> nts-ajax-keep-position" href="<?php echo ntsLink::makeLink('-current-/../create-form', '', $confirmParams ); ?>"><?php echo $linkView; ?></a>
</li>
<?php endforeach; ?>
</ul>