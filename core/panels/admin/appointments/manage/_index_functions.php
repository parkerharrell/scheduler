<?php
function ntsShowResourcesInCell( $resources, $d, $i = -1, $thisDate = 0){
	global $NTS_VIEW;
	$resourceCount = count( $resources );
?>
<table>
<?php for( $j = 0; $j < $resourceCount; $j++ ) : ?>
<?php
$thisResId = $resources[$j]->getId();
if( $i == -1 )
	$APPS_ARRAY = $NTS_VIEW['APPS'][$d][$thisResId];
else
	$APPS_ARRAY = $NTS_VIEW['SLOTS'][$d][$i][$j]['apps'];

$thisAppsCount = count( $APPS_ARRAY );
$approvedCount = 0;
$pendingCount = 0;
reset( $APPS_ARRAY );
foreach( $APPS_ARRAY as $appArray ){
	if( $appArray['approved'] )
		$approvedCount++;
	else
		$pendingCount++;
	}

$slotClass = 'haslot';
if( $thisAppsCount ){
	if( $NTS_VIEW['selectableTimes'][$d][$thisResId] )
		$slotClass .= ' partbook';
	else
		$slotClass .= ' fullbook';
	if( $pendingCount )
		$slotClass .= ' pending';
	}
else {
	if( $NTS_VIEW['selectableTimes'][$d][$thisResId] )
		$slotClass .= ' working';
	}
?>

<tr>
<?php if( 1 || $resourceCount > 1 ) : ?>
<?php if( $i == -1 ) : // month view ?>
<td class="resource-holder">
	<a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $thisDate, 'viewPeriod' => 'day', 'resource' => $resources[$j]->getId()) ); ?>"><?php echo ntsView::objectTitle( $resources[$j] ); ?></a>
</td>
<?php else : // week view ?>
<?php
$resSlotClass = 'hours';
if( $NTS_VIEW['SLOTS'][$d][$i][$j]['selectableSessions'] )
	$resSlotClass .= ' selectable';
?>
<td class="resource-holder <?php echo $resSlotClass; ?>">
	<div id="nts-crapp-<?php echo $d . '-' . $i . '-' . $j; ?>"><?php echo ntsView::objectTitle( $resources[$j] ); ?></div>
</td>
<?php endif; ?>
<?php endif; ?>
<td class="apps-holder <?php echo $slotClass; ?>">
<?php if( $thisAppsCount ) : ?>
<div class="nts-tooltip">
<?php echo $thisAppsCount; ?>
<span>
<?php ntsShowAppsInSlot( $APPS_ARRAY ) ; ?>
</span>
</div>
<?php else : ?>
&nbsp;
<?php endif; ?>
</td>
</tr>
<?php endfor; ?>
</table>
<?php
	}
?>
<?php
function ntsShowAppsInSlot( $thisApps ){
	global $NTS_VIEW, $NTS_CURRENT_USER;
	$t = $NTS_VIEW['t'];
?>
<ul>
<?php foreach( $thisApps as $thisApp ) : ?>
<?php
	$app = ntsObjectFactory::get( 'appointment' );
	$app->setId( $thisApp['id'] );

	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $app->getProp('service_id') );

	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $app->getProp('location_id') );

	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $app->getProp('resource_id') );
	$customer = new ntsUser();
	$customer->setId( $app->getProp('customer_id') );
	
	$seats = $app->getProp('seats');

	$t->setTimestamp( $app->getProp('starts_at') );
	$cellView = $t->formatWeekday() . ', ' . $t->formatDate();
	$startTime = $t->formatTime();
	$t->modify( '+' . $app->getProp('duration') . ' seconds' );
	$endTime = $t->formatTime();

	$approved = $app->getProp( 'approved' );
	$linkClass = $approved ? 'approved' : 'pending';
?>
<li>
<a class="<?php echo $linkClass; ?>" id="nts-appointment-<?php echo $app->getId(); ?>" href="#"><?php echo $startTime; ?> - <?php echo $endTime; ?></a>
<b><?php echo $service->getProp('title'); ?></b>
<?php if( $seats > 1 ) : ?>
(<?php echo M('Seats'); ?>: <?php echo $seats; ?>)
<?php endif; ?>
<br>
<?php echo M('Customer'); ?>: <b><?php echo $customer->getProp('first_name'); ?> <?php echo $customer->getProp('last_name'); ?></b><br>
<?php if( (! NTS_SINGLE_RESOURCE) && ($NTS_CURRENT_USER->hasRole('admin') ) ) : ?>
<?php 	echo M('Bookable Resource'); ?>: <b><?php echo $resource->getProp('title'); ?></b><br>
<?php endif; ?>
<?php if( ! NTS_SINGLE_LOCATION ) : ?>
<?php 	echo M('Location'); ?>: <b><?php echo $location->getProp('title'); ?></b><br>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php
	}
?>