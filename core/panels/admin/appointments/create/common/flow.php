<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

if( isset($NTS_VIEW['CURRENT_REQUEST']) ){
	$service = $NTS_VIEW['CURRENT_REQUEST']['service'];
	$location = $NTS_VIEW['CURRENT_REQUEST']['location'];
	$resource = $NTS_VIEW['CURRENT_REQUEST']['resource'];
	$customer = $NTS_VIEW['CURRENT_REQUEST']['customer'];
	$time = $NTS_VIEW['CURRENT_REQUEST']['time'];
	$seats = $NTS_VIEW['CURRENT_REQUEST']['seats'];

	$showResource = ( $resource && (! NTS_SINGLE_RESOURCE) ) ? true : false;
	$showLocation = ( $location && (! NTS_SINGLE_LOCATION) ) ? true : false;
	$showPrice = ( $service && ntsCurrency::formatServicePrice($service->getProp('price')) ) ? true : false;
	}

if( $NTS_VIEW['RESCHEDULE'] ){
	$showResource = ( ! NTS_SINGLE_RESOURCE ) ? true : false;
	$showLocation = ( ! NTS_SINGLE_LOCATION ) ? true : false;
	$showPrice = ( ntsCurrency::formatServicePrice($NTS_VIEW['RESCHEDULE']['obj']->getProp('price')) ) ? true : false;
	}

$showSessionDuration = $conf->get('showSessionDuration');
$t = $NTS_VIEW['t'];
$totalPrice = 0;

if( $NTS_VIEW['RESCHEDULE'] || $customer || $time || $service || $showResource || $showLocation ){
	$reallyShow = true;
	}
else {
	return;
	}
?>
<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
<h2><?php echo M('Change Appointment'); ?></h2>
<?php else : ?>
<h2><?php echo M('Create Appointment'); ?></h2>
<?php endif; ?>

<div id="nts-appointment-flow">
<table>
<tr>
<?php $shownColumns = 0; ?>
<?php if( $NTS_VIEW['RESCHEDULE'] && isset($NTS_VIEW['CURRENT_REQUEST']) ) : ?>
	<th></th>
<?php endif; ?>

<?php if( $NTS_VIEW['RESCHEDULE'] || $time ) : ?>
	<th><?php echo M('Date and Time'); ?></th>
	<?php $shownColumns += 2; ?>
<?php endif; ?>

<?php if( $NTS_VIEW['RESCHEDULE'] || $service ) : ?>
	<th><?php echo M('Service'); ?></th>
<?php endif; ?>
<?php if( $showPrice ) : ?>
	<th><?php echo M('Price'); ?></th>
	<?php $shownColumns++; ?>
<?php endif; ?>

<?php if( ($NTS_VIEW['RESCHEDULE'] || $location ) && (! NTS_SINGLE_LOCATION) ) : ?>
	<th><?php echo M('Location'); ?></th>
	<?php $shownColumns++; ?>
<?php endif; ?>

<?php if( ($NTS_VIEW['RESCHEDULE'] || $resource ) && (! NTS_SINGLE_RESOURCE) ) : ?>
	<th><?php echo M('Bookable Resource'); ?></th>
	<?php $shownColumns++; ?>
<?php endif; ?>

<?php if( $NTS_VIEW['RESCHEDULE'] || $customer ) : ?>
	<th><?php echo M('Customer'); ?></th>
	<?php $shownColumns++; ?>
<?php endif; ?>

</tr>

<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
	<tr>
	<?php if( isset($NTS_VIEW['CURRENT_REQUEST']) ) : ?>
		<th><?php echo M('Old Appointment'); ?></th>
	<?php endif; ?>

	<?php
	$t = new ntsTime( $NTS_VIEW['RESCHEDULE']['time'] );

	$thisLoc = $NTS_VIEW['RESCHEDULE']['location'];
	$thisRes = $NTS_VIEW['RESCHEDULE']['resource'];
	$thisCust = $NTS_VIEW['RESCHEDULE']['customer'];
	$thisSeats = $NTS_VIEW['RESCHEDULE']['obj']->getProp('seats');
	$timeView = $t->formatTime( $NTS_VIEW['RESCHEDULE']['obj']->getProp('duration') );
	?>
	<td>
	<?php echo $t->formatWeekday(); ?>, <?php echo $t->formatDate(); ?><br>
	<?php echo $timeView; ?>
	</td>

	<td>
		<?php
		$cellView = ntsView::objectTitle($NTS_VIEW['RESCHEDULE']['service']);
		?>
		<?php echo $cellView; ?>
		<?php if( $thisSeats > 1 ) : ?>
			<br><?php echo M('Seats'); ?>: <?php echo $thisSeats; ?>
		<?php endif; ?>
	</td>

 	<?php if( $showPrice ) : ?>
		<td>
		<?php echo ntsCurrency::formatServicePrice($NTS_VIEW['RESCHEDULE']['obj']->getProp('price')); ?>
		</td>
	<?php endif; ?>

	<?php if( (! NTS_SINGLE_LOCATION) ) : ?>
	<td>
		<?php echo ntsView::objectTitle($thisLoc); ?>
	</td>
	<?php endif; ?>

	<?php if( (! NTS_SINGLE_RESOURCE) ) : ?>
	<td>
		<?php echo $thisRes->getProp('title'); ?>
	</td>
	<?php endif; ?>

	<td>
		<?php echo $thisCust->getProp('first_name'); ?> <?php echo $thisCust->getProp('last_name'); ?>
	</td>

	</tr>
<?php endif; ?>

<?php if( isset($NTS_VIEW['CURRENT_REQUEST']) ) : ?>
<?php $rowsCount = ( count($time) > 1 ) ? count($time) : 1; ?>
<?php for( $i = 0; $i < $rowsCount; $i++ ) : ?>
	<tr>
	<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
	<th><?php echo M('New Appointment'); ?></th>
	<?php endif; ?>

	<?php if( $time ) : ?>
		<?php 
		$t = new ntsTime( $time[$i] );
		if( $service->getProp('until_closed') ){
			$tm = new haTimeManager();
			$tm->allowEarlierThanNow = true;
			$tm->setService( $service );
			$tm->setLocation( $thisLoc );
			$tm->setResource( $thisRes );

			$testTimes = $tm->getSelectableTimes_Internal( 
				$time[$i],
				$time[$i],
				$seats[$i]
				);

			$duration = 0;
			if( isset($testTimes[ $time[$i] ]) ){
				reset( $testTimes[ $time[$i] ] );
				foreach( $testTimes[ $time[$i] ] as $tt ){
					if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
						$duration = $tt[ $tm->SLT_INDX['duration'] ];
					}
				}
			}
		else {
			$duration = $service->getProp('duration');
			}
		$timeView = $t->formatTime( $duration );
		$dateView = $t->formatWeekday() . ', ' . $t->formatDate();
		?>
		<td>
		<?php echo $dateView; ?><br>
		<?php echo $timeView; ?>
		</td>
	<?php elseif($NTS_VIEW['RESCHEDULE']) : ?>
		<td></td>
	<?php endif; ?>

	<?php if( $NTS_VIEW['RESCHEDULE'] || $service ) : ?>
	<td>
		<?php echo ntsView::objectTitle($service); ?>
		<?php if( $seats[$i] > 1 ) : ?>
			<br><?php echo M('Seats'); ?>: <?php echo $seats[$i]; ?>
		<?php endif; ?>
	</td>
	<?php endif; ?>

	<?php if( $showPrice ) : ?>
		<?php
		$thisPrice = ntsLib::getServicePrice(
			$service,
			$seats[$i]
			);
		?>
		<?php $totalPrice += $thisPrice; ?>
		<td>
		<?php echo ntsCurrency::formatServicePrice($thisPrice); ?>
		</td>
	<?php endif; ?>

	<?php if( ($location || $NTS_VIEW['RESCHEDULE'] )&& (! NTS_SINGLE_LOCATION) ) : ?>
	<td>
		<?php if( $location ) : ?>
			<?php $thisLoc = isset($location[$i]) ? $location[$i] : $location[0]; ?>
			<?php echo ntsView::objectTitle($thisLoc); ?>
		<?php endif; ?>
	</td>
	<?php endif; ?>

	<?php if( ($resource || $NTS_VIEW['RESCHEDULE']) && (! NTS_SINGLE_RESOURCE) ) : ?>
	<td>
		<?php if( $resource ) : ?>
			<?php $thisRes = isset($resource[$i]) ? $resource[$i] : $resource[0]; ?>
			<?php echo $thisRes->getProp('title'); ?>
		<?php endif; ?>
	</td>
	<?php endif; ?>

	<?php if( $customer || $NTS_VIEW['RESCHEDULE'] ) : ?>
	<td>
		<?php if( $customer ) : ?>
			<?php $thisCust = isset($customer[$i]) ? $customer[$i] : $customer[0]; ?>
			<?php echo $thisCust->getProp('first_name'); ?> <?php echo $thisCust->getProp('last_name'); ?>
		<?php endif; ?>
	</td>
	<?php endif; ?>

	</tr>
<?php endfor; ?>
<?php endif; ?>

<?php if( (! $NTS_VIEW['RESCHEDULE']) && $showPrice && (count($time) > 1) ) : ?>
<tr>
<th colspan="2"></td>
<th style="text-align: right;"><?php echo M('Total'); ?></td>
<th><?php echo ntsCurrency::formatServicePrice($totalPrice); ?></td>
<?php if( (! NTS_SINGLE_LOCATION) ) : ?>
	<th></th>
<?php endif; ?>
<?php if( (! NTS_SINGLE_RESOURCE) ) : ?>
	<th></th>
<?php endif; ?>

</tr>
<?php endif; ?>

</table>

</div>