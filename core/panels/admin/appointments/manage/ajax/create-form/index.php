<?php
$t = $NTS_VIEW['t'];
$confirmParams = array();
$confirmParams['time'] = $NTS_VIEW['ts'];
$confirmParams['resource'] = $NTS_VIEW['resource']->getId();
$confirmParams['seats'] = 1;
$confirmParams['location'] = $NTS_VIEW['location']->getId();
if( $NTS_VIEW['customer'] )
	$confirmParams['customer'] = $NTS_VIEW['customer']->getId();
if( $NTS_VIEW['RESCHEDULE'] )
	$confirmParams['reschedule'] = $NTS_VIEW['RESCHEDULE']['obj']->getId();
$confirmParams['viewMode'] = '';
reset( $NTS_VIEW['allServices'] );

global $NTS_VIEW;
$showCats = $NTS_VIEW['showCats'];
$cat2service = $NTS_VIEW['cat2service'];
$entries = $NTS_VIEW['allServices'];

$servicesOptions = array();
if( ! $showCats ){
	foreach( $entries as $s ){
		$linkView = ntsView::objectTitle($s);
		$class = ( in_array($s->getId(), $NTS_VIEW['SELECTABLE_SERVICES']) ) ? 'available' : 'not-available';
		$servicesOptions[] = array( $s->getId(), $linkView, $class );
		}
	}
else {
	foreach( $showCats as $e ){
		$servicesOptions[] = array( 0, $e[1] );

		$myEntries = $cat2service[$e[0]];
		foreach( $myEntries as $s ){
			$linkView = ntsView::objectTitle($s);
			$class = ( in_array($s->getId(), $NTS_VIEW['SELECTABLE_SERVICES']) ) ? 'available' : 'not-available';
			$servicesOptions[] = array( $s->getId(), $linkView, $class );
			}
		}
	}
?>
<?php if( ! $NTS_VIEW['RESCHEDULE'] ) : ?>
	<h3><?php echo M('Create Appointment'); ?></h3>
<?php else : ?>
	<h3><?php echo M('Change Appointment'); ?></h3>
<?php endif; ?>

<b><?php echo ntsView::objectTitle( $NTS_VIEW['resource'] ); ?></b><br>
<b><?php echo $t->formatDate(); ?> <?php echo $t->formatTime(); ?></b><br>

<?php if( in_array($NTS_VIEW['resource']->getId(),$NTS_VIEW['RESOURCE_SCHEDULE_EDIT']) ) : ?>
	<a href="<?php echo ntsLink::makeLink('admin/schedules/timeoff/create', '', array('_res_id' => $NTS_VIEW['resource']->getId(), 'from' => $NTS_VIEW['ts'], 'viewMode' => '' ) ); ?>"><?php echo M('Timeoff') . ': ' . M('Create'); ?>?</a>
<?php endif; ?>

<ul class="nts-boxed-selector">
<?php foreach( $servicesOptions as $so ) : ?>
<li>
<?php
$linkView = $so[1];
?>
<?php if( $so[0] > 0 ) : ?>
<?php
	$confirmParams['service'] = $so[0];
	$linkClass = 'service-h';
	if( isset($so[2]) )
		$linkClass .= ' ' . $so[2];
?>
<a title="<?php echo $linkView; ?>" class="<?php echo $linkClass; ?>" href="<?php echo ntsLink::makeLink('-current-/../../../create/confirm', '', $confirmParams ); ?>"><?php echo $linkView; ?></a>
<?php else : ?>
	<b><?php echo $linkView; ?></b>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>