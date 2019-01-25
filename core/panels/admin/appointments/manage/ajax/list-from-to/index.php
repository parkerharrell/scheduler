<?php
$t = new ntsTime;
$classesShown = array();

$finalEntries = ntsLib::processGroupAppointments( $NTS_VIEW['entries'] );
?>
<ul>
<?php foreach( $finalEntries as $fe ) : ?>
<?php
	if( is_array($fe) )
		$app = $NTS_VIEW['entries'][$fe[0]];
	else
		$app = $NTS_VIEW['entries'][$fe];

	$serviceId = $app->getProp('service_id');
	$startsAt = $app->getProp('starts_at');
	$locationId = $app->getProp('location_id');
	$resourceId = $app->getProp('resource_id');

	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $serviceId );

	$classType = $service->getProp( 'class_type' );
	if( $classType ){
		$classIndex = $serviceId . '-' . $locationId . '-' . $resourceId . '-' . $startsAt;
		}

	if( (! $classType) || (! isset($classesShown[$classIndex]) ) ){
		$location = ntsObjectFactory::get( 'location' );
		$location->setId( $locationId );

		$resource = ntsObjectFactory::get( 'resource' );
		$resource->setId( $resourceId );

		$seats = $app->getProp('seats');
		}

	if( $classType ){
		$showAs = isset($classesShown[$classIndex]) ? 'customer' : 'start';
		}
	else {
		$showAs = 'full';
		}

	$customer = new ntsUser();
	$customer->setId( $app->getProp('customer_id') );

	$t->setTimestamp( $startsAt );
	$startTime = $t->formatTime();
	$t->modify( '+' . $app->getProp('duration') . ' seconds' );
	$endTime = $t->formatTime();

	$approved = $app->getProp( 'approved' );
	$linkClass = 'app-h';
	if( ! is_array($fe) )
		$linkClass .= $approved ? ' approved' : ' pending';
?>

<li class="<?php echo $linkClass; ?>">

<?php if( ! is_array($fe) ) : ?>
<a id="nts-appointment-<?php echo $app->getId(); ?>" href="#">
<?php endif; ?>

<h2><?php echo $startTime; ?> - <?php echo $endTime; ?></h2>
<b><?php echo $service->getProp('title'); ?></b>
<?php if( $seats > 1 ) : ?>
(<?php echo M('Seats'); ?>: <?php echo $seats; ?>)
<?php endif; ?>
<br>
<?php if( (! NTS_SINGLE_RESOURCE) ) : ?>
<?php 	echo M('Bookable Resource'); ?>: <b><?php echo $resource->getProp('title'); ?></b><br>
<?php endif; ?>
<?php if( ! NTS_SINGLE_LOCATION ) : ?>
<?php 	echo M('Location'); ?>: <b><?php echo $location->getProp('title'); ?></b><br>
<?php endif; ?>

<?php if( ! is_array($fe) ) : ?>
	<?php echo M('Customer'); ?>: <b><?php echo $customer->getProp('first_name'); ?> <?php echo $customer->getProp('last_name'); ?></b><br>
<?php else : ?>
	<?php echo M('Customers'); ?>:<br>
	<ol>
	<?php foreach( $fe as $fid ) : ?>
	<?php
	$app2 = $NTS_VIEW['entries'][$fid];
	$customer = new ntsUser();
	$customer->setId( $app2->getProp('customer_id') );
	$approved = $app2->getProp( 'approved' );
	$linkClass2 = 'app-h';
	$linkClass2 .= $approved ? ' approved' : ' pending';
	?>
	<li class="<?php echo $linkClass2; ?>">
	<a id="nts-appointment-<?php echo $app2->getId(); ?>" href="#"><b><?php echo $customer->getProp('first_name'); ?> <?php echo $customer->getProp('last_name'); ?></b></a>
	</li>
	<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php if( ! is_array($fe) ) : ?>
</a>
<?php endif; ?>

</li>
<?php endforeach; ?>
</ul>