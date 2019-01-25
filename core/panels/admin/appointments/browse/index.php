<?php require( dirname(__FILE__) . '/_header.php' ); ?>

<?php
$count = 0;
$t = new ntsTime();
reset( $NTS_VIEW['entries'] );

$finalEntries = ntsLib::processGroupAppointments( $NTS_VIEW['entries'] );
?>

<ul class="nts-listing" id="nts-appointment-listing">

<?php if( $NTS_VIEW['showPerPage'] == 'all' ) : ?>
<li>
<?php
// count total duration and price
$totalDuration = 0;
$totalPrice = 0;
foreach( $NTS_VIEW['entries'] as $a ){
	if( (! $a->getProp('cancelled')) && ($a->getProp('approved')) ){
		$totalDuration += $a->getProp( 'duration' );
		$thisPrice = $a->getProp( 'price' );
		if( strlen($thisPrice) )
			$totalPrice += $thisPrice;
		}
	}
?>
<?php if( count($NTS_VIEW['entries']) > 0 ) : ?>
	<?php echo M('Total Duration'); ?>:
	<b>
	<?php if( $totalDuration ) : ?>
		<?php echo ntsTime::formatPeriod($totalDuration); ?>
	<?php else : ?>
		-
	<?php endif; ?>
	</b>
	&nbsp;
	<?php echo M('Total Amount'); ?>:
	<b>
	<?php if( $totalPrice ) : ?>
		<?php echo ntsCurrency::formatServicePrice($totalPrice); ?>
	<?php else : ?>
		-
	<?php endif; ?>
	</b>
<?php endif; ?>
<li>
<?php endif; ?>

<?php foreach( $finalEntries as $fe ) : ?>
<?php
	if( ! is_array($fe) )
		$fe = array( $fe );

	$a = $NTS_VIEW['entries'][$fe[0]];
	$resourceId = $a->getProp('resource_id');
	/* check if can view or manage this appointment */
	$iCanViewThis = false;
	$iCanEditThis = false;
	global $NTS_CURRENT_USER;
	$resourceApps = $NTS_CURRENT_USER->getProp( '_resource_apps' );
	if( (! isset($resourceApps[$resourceId]) ) || ( $resourceApps[$resourceId] == 'none' ) ){
		}
	elseif( $resourceApps[$resourceId] == 'view' ){
		$iCanViewThis = true;
		}
	else {
		$iCanViewThis = true;
		$iCanEditThis = true;
		}

	if( ! $iCanViewThis ){
		continue;
		}

	$class = '';
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

<li class="<?php echo ($count % 2) ? 'even' : 'odd'; ?>">

<ul>
<!-- TIME -->
<?php
$t = new ntsTime( $a->getProp('starts_at') );
$dateView = $t->formatWeekdayShort() . ', ' . $t->formatDate();
$timeView = $t->formatTime( $a->getProp('duration') );
?>
<li class="appointment-time"><h3><?php echo $timeView; ?> [<?php echo $dateView; ?>]</h3></li>

<!-- SERVICE -->
<?php
	$service = ntsObjectFactory::get( 'service' );
	$service->setId( $a->getProp('service_id') );
//	$cellView = ntsView::objectTitle($service);
	$cellView = $service->getProp( 'title' );
?>
<li><?php echo M('Service'); ?>: <b><?php echo $cellView; ?></b></li>

<!-- LOCATION -->
<?php if( ($NTS_VIEW['fix'] != 'location') && (! NTS_SINGLE_LOCATION) ) : ?>
<li>
<?php
	$location = ntsObjectFactory::get( 'location' );
	$location->setId( $a->getProp('location_id') );
	$cellView = ( ! $location->notFound() ) ? ntsView::objectTitle($location) : ' - ' . M('Deleted') . ' - ';
?>
<?php echo M('Location'); ?>: <b><?php echo $cellView; ?></b>
</li>
<?php endif; ?>

<!-- RESOURCE -->
<?php if( ( ! (($NTS_VIEW['fix'] == 'resource') && (count($NTS_VIEW['fixId']) <= 1)) ) && (! NTS_SINGLE_RESOURCE) ) : ?>
<li>
<?php
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $a->getProp('resource_id') );
	$cellView = ( ! $resource->notFound() ) ? ntsView::objectTitle($resource) : ' - ' . M('Deleted') . ' - ';
?>
<?php echo M('Bookable Resource'); ?>: <b><?php echo $cellView; ?></b>
</li>
<?php endif; ?>

<!-- CUSTOMER -->
<?php if( ! ($NTS_VIEW['fix'] == 'customer') ) : ?>
<li>

<table>
<?php foreach( $fe as $fid ) : ?>
<?php
$a2 = $NTS_VIEW['entries'][$fid];
$customer = new ntsUser();
$customer->setId( $a2->getProp('customer_id') );
$approved = $a2->getProp( 'approved' );

$class2 = '';
if( $a2->getProp('cancelled') ){
	$class2 = 'ntsCancelled';
	}
else {
	if( $a2->getProp('no_show') ){
		$class2 = 'ntsNoshow';
		}
	else {
		if( $a2->getProp('approved') )
			$class2 = 'ntsApproved';
		else
			$class2 = 'ntsPending';
		}
	}
?>
<tr>
<td class="<?php echo $class2; ?>">
<input type="checkbox" id="id[]" name="id[]" VALUE="<?php echo $a2->getId(); ?>">
</td>
<td><b><?php echo $customer->getProp('first_name'); ?> <?php echo $customer->getProp('last_name'); ?></b></td>
<td class="nts-row-actions">
<?php
	$thisActions = array();
	if( $iCanEditThis ){
		if( ! $a2->getProp('cancelled') ){
			$thisActions[] = array( M('Edit'), ntsLink::makeLink($PANEL_PREFIX . '/edit', '', array('_id' => $a2->getProp('id')), true ) );
			if( ! $a2->getProp('approved') ){
				if( ! $a2->getProp('no_show') ){
					$thisActions[] = array( M('Approve'), ntsLink::makeLink($PANEL_PREFIX . '/edit/approve', '', array('_id' => $a2->getProp('id')), true ) );
					}
				}
         ### Customized by RAH 5/17/11 - Add Schedule Pending action link
			if( $a2->getProp('approved') ){
				$thisActions[] = array( M('Schedule Pending'), ntsLink::makeLink($PANEL_PREFIX . '/edit/pending', '', array('_id' => $a2->getProp('id')), true ) );
				}

			if( ! $a2->getProp('no_show') ){
				$thisActions[] = array( M('Reschedule'), ntsLink::makeLink($PANEL_PREFIX . '/manage', '', array('reschedule' => $a2->getProp('id')) ) );
				}
			$thisActions[] = array( M('Reject'), ntsLink::makeLink($PANEL_PREFIX . '/edit/reject', '', array('_id' => $a2->getProp('id')), true ), 'alert' );

			if( ! $a2->getProp('no_show') ){
				$thisActions[] = array( M('No Show'), ntsLink::makeLink($PANEL_PREFIX . '/edit/noshow', '', array('_id' => $a2->getProp('id')), true ) );
				}

			if( $a2->getProp('no_show') ){
				$thisActions[] = array( M('Release No Show'), ntsLink::makeLink($PANEL_PREFIX . '/edit/releasenoshow', '', array('_id' => $a2->getProp('id')), true ) );
				}
			}
		}
?>
<?php if( $thisActions ) : ?>
<?php foreach( $thisActions as $ta ) : ?>
<a href="<?php echo $ta[1]; ?>" class="<?php if(isset($ta[2])){echo $ta[2];}; ?>"><?php echo $ta[0]; ?></a>
<?php endforeach; ?>

<?php endif; ?>

	</td>
	</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</li>

<!-- ACTIONS -->
<?php
$thisActions = array();
if( ! is_array($fe) ){
	if( $iCanEditThis ){
		if( ! $a->getProp('cancelled') ){
			$thisActions[] = array( M('Edit'), ntsLink::makeLink($PANEL_PREFIX . '/edit', '', array('_id' => $a->getProp('id')), true ) );
			if( ! $a->getProp('approved') ){
				if( ! $a->getProp('no_show') ){
					$thisActions[] = array( M('Approve'), ntsLink::makeLink($PANEL_PREFIX . '/edit/approve', '', array('_id' => $a->getProp('id')), true ) );
					}
				}
			if( ! $a->getProp('no_show') ){
				$thisActions[] = array( M('Reschedule'), ntsLink::makeLink($PANEL_PREFIX . '/manage', '', array('reschedule' => $a->getProp('id')) ) );
				}
			$thisActions[] = array( M('Reject'), ntsLink::makeLink($PANEL_PREFIX . '/edit/reject', '', array('_id' => $a->getProp('id')), true ), 'alert' );

			if( ! $a->getProp('no_show') ){
				$thisActions[] = array( M('No Show'), ntsLink::makeLink($PANEL_PREFIX . '/edit/noshow', '', array('_id' => $a->getProp('id')), true ) );
				}

			if( $a->getProp('no_show') ){
				$thisActions[] = array( M('Release No Show'), ntsLink::makeLink($PANEL_PREFIX . '/edit/releasenoshow', '', array('_id' => $a->getProp('id')), true ) );
				}
			}
		}
	}
?>

<?php if( $thisActions ) : ?>
<li class="nts-row-actions">
<?php foreach( $thisActions as $ta ) : ?>
<a href="<?php echo $ta[1]; ?>" class="<?php if(isset($ta[2])){echo $ta[2];}; ?>"><?php echo $ta[0]; ?></a>
<?php endforeach; ?>
</li>
<?php endif; ?>

</ul>

</li>
<?php $count++; ?>
<?php endforeach; ?>

</ul>