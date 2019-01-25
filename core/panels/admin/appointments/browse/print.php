<?php
$actionSpan = $NTS_VIEW['fix'] ? 4 : 5;
$NTS_VIEW['actionReturn'] = $NTS_VIEW['fix'] ? $NTS_VIEW['fix'] : 'all';
$ff =& ntsFormFactory::getInstance();

$startUpPanel = 'admin/appointments';
?>

<?php
include_once( NTS_BASE_DIR . '/lib/view/ntsPager.php' );
$app =& ntsApplication::getInstance();
$displayUsername = true;
$displayEmail = true;
?>

<table>
<tr>
<td style="padding: 0 1em; vertical-align: top; width: 25em;">
<?php if( $NTS_VIEW['showFilter'] ) : ?>
	<?php if( isset($NTS_VIEW['fromTimestamp']) && isset($NTS_VIEW['toTimestamp']) ) : ?>
		<?php
		$t = new ntsTime( $NTS_VIEW['fromTimestamp'] );
		$fromFormatted = $t->formatDate();
		$t = new ntsTime( $NTS_VIEW['toTimestamp'] );
		$toFormatted = $t->formatDate();
		?>
		<?php echo M('Dates'); ?>: <b><?php echo $fromFormatted; ?></b> - <b><?php echo $toFormatted; ?></b>
	<?php endif; ?>

	<?php if( isset($NTS_VIEW['searchParams']['createdFrom']) && isset($NTS_VIEW['searchParams']['createdTo']) ) : ?>
		<?php
		$t = new ntsTime( $NTS_VIEW['searchParams']['createdFrom'] );
		$createdFromFormatted = $t->formatDate();
		$t = new ntsTime( $NTS_VIEW['searchParams']['createdTo'] );
		$createdToFormatted = $t->formatDate();
		?>
		<?php echo M('Created'); ?>: <b><?php echo $createdFromFormatted; ?></b> - <b><?php echo $createdToFormatted; ?></b>
	<?php endif; ?>

	<?php if( isset($NTS_VIEW['searchParams']['service']) ) : ?>
		<br><?php echo M('Service'); ?>: <b><?php echo $NTS_VIEW['searchParams']['service']->getProp('title'); ?></b>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['location']) ) : ?>
		<br><?php echo M('Location'); ?>: <b><?php echo ntsView::objectTitle($NTS_VIEW['searchParams']['location']); ?></b>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['resource']) ) : ?>
		<br><?php echo M('Bookable Resource'); ?>: <b><?php echo $NTS_VIEW['searchParams']['resource']->getProp('title'); ?></b>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['customer']) ) : ?>
		<br><?php echo M('Customer'); ?>: <b><?php echo $NTS_VIEW['searchParams']['customer']->getProp('first_name'); ?> <?php echo $NTS_VIEW['searchParams']['customer']->getProp('last_name'); ?></b>
	<?php endif; ?>

<?php else: ?>
	<?php if( $NTS_VIEW['show'] == 'old' ) : ?>
		<h2 style="padding-top: 0; margin-top: 0;"><?php echo M('Old Appointments'); ?></h2>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'upcoming') ); ?>"><?php echo M('Upcoming Appointments'); ?></a>
	<?php else : ?>
		<h2 style="padding-top: 0; margin-top: 0;"><?php echo M('Upcoming Appointments'); ?></h2>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'old') ); ?>"><?php echo M('Old Appointments'); ?></a>
	<?php endif; ?>
	
	<?php if ( $NTS_VIEW['fix'] ) : ?>
		&nbsp;<a href="<?php echo ntsLink::makeLink( $PANEL_PREFIX . '/search', '', array($NTS_VIEW['fix'] => $NTS_VIEW['fixId']) ); ?>"><?php echo M('Advanced Search'); ?></a>
	<?php endif; ?>
<?php endif; ?>
</td>

<?php if( count($NTS_VIEW['entries']) ) : ?>
	<td style="font-size: 70%; padding: 0 1em; vertical-align: bottom;">
&nbsp;
	</td>
<?php endif; ?>
</tr>
</table>

<p>
<?php if( ! count($NTS_VIEW['entries']) ) : ?>
<?php
	echo M('None');
	return;
?>
<?php endif; ?>

<table>
<tr>
	<th><?php echo M('Legend'); ?>:</th>
	<td style="width: 1em; padding: 0;" class="ntsApproved">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Approved'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsPending">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Pending'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsNoshow">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('No Show'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsCancelled">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Cancelled'); ?></td>

	<td style="text-align: right;">
[<?php echo $NTS_VIEW['showFrom']; ?> - <?php echo $NTS_VIEW['showTo']; ?> of <?php echo $NTS_VIEW['totalCount']; ?>]
	</td>
</tr>
</table>

<table class="nts-listing" id="nts-appointment-listing">
<tr class="listing-header">
	<th style="width: 1em; padding: 0;">&nbsp;</th>
	<th><?php echo M('When'); ?></th>
	<th><?php echo M('Service'); ?></th>
<?php if( $NTS_VIEW['fix'] != 'location' ) : ?>
	<th><?php echo M('Location'); ?></th>
<?php endif; ?>
<?php if( $NTS_VIEW['fix'] != 'resource' ) : ?>
	<th><?php echo M('Bookable Resource'); ?></th>
<?php endif; ?>
<?php if( $NTS_VIEW['fix'] != 'customer' ) : ?>
	<th><?php echo M('Customer'); ?></th>
<?php endif; ?>
</tr>

<?php 
$count = 0;
$t = new ntsTime();
?>
<?php foreach( $NTS_VIEW['entries'] as $a ) : ?>
<tr class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">
	<?php
	if( $a->getProp('cancelled') ){
		$class = 'ntsCancelled';
		}
	else {
		if( $a->getProp('no_show') ){
			$class = 'ntsNoshow';
			}
		else {
			if( $a->getProp('approved') )
				$class = 'ntsApproved';
			else
				$class = 'ntsPending';
			}
		}
	?>
	<td style="padding: 0;" class="<?php echo $class; ?>">&nbsp;</td>
	<td>
		<?php
		$t = new ntsTime( $a->getProp('starts_at') );

		$cellView = $t->formatWeekdayShort() . ', ' . $t->formatDate();
		$startTime = $t->formatTime();
		$t->modify( '+' . $a->getProp('duration') . ' seconds' );
		$endTime = $t->formatTime();
		$cellView .= '<br><b>' . $startTime . ' - ' . $endTime . '</b>';
		?>
		<a href="<?php echo ntsLink::makeLink($startUpPanel . '/edit', '', array('_id' => $a->getProp('id')), true ); ?>">
		<?php echo $cellView; ?>
		</a>

		<?php
		$t = new ntsTime( $a->getProp('created_at') );
		$createdView = $t->formatWeekdayShort() . ', ' . $t->formatDate() . ' ' . $t->formatTime();
		?>
		<br>
		<span style="font-size: 80%; font-style: italic;"><?php echo M('Created'); ?>: <?php echo $createdView; ?></span>
	</td>
	<td>
		<?php
		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $a->getProp('service_id') );

		$seats = $a->getProp('seats');
		echo nl2br( ntsView::appServiceView($a) );
		?>
	</td>

<?php if( $NTS_VIEW['fix'] != 'location' ) : ?>
	<td>
		<?php
		$location = new ntsObject('location');
		$location->setId( $a->getProp('location_id') );
		?>
		<a href="<?php echo ntsLink::makeLink('admin/locations/edit', '', array('_id' => $location->getId()) ); ?>">
		<?php echo ntsView::objectTitle($location); ?>
		</a>
	</td>
<?php endif; ?>

<?php if( $NTS_VIEW['fix'] != 'resource' ) : ?>
	<td>
		<?php
		$resource = ntsObjectFactory::get( 'resource' );
		$resource->setId( $a->getProp('resource_id') );
		?>
		<a href="<?php echo ntsLink::makeLink('admin/resources/edit', '', array('_id' => $resource->getId()) ); ?>">
		<?php echo ntsView::objectTitle( $resource ); ?>
		</a>
	</td>
<?php endif; ?>

<?php if( $NTS_VIEW['fix'] != 'customer' ) : ?>
	<td>
		<?php
		$customer = new ntsUser();
		$customer->setId( $a->getProp('customer_id') );
		?>
		<a href="<?php echo ntsLink::makeLink('admin/customers/edit', '', array('_id' => $customer->getId()) ); ?>">
		<?php echo ntsView::objectTitle( $customer ); ?>
		</a>
	</td>
<?php endif; ?>
</tr>
<?php $count++; ?>
<?php endforeach; ?>
</table>