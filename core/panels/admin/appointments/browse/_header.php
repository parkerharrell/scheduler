<?php
global $PANEL_PREFIX, $NTS_CURRENT_USER;
$PANEL_PREFIX = 'admin/appointments';

$actionSpan = $NTS_VIEW['fix'] ? 5 : 6;
$NTS_VIEW['actionReturn'] = $NTS_VIEW['fix'] ? $NTS_VIEW['fix'] : 'all';
$ff =& ntsFormFactory::getInstance();
?>
<?php
include_once( NTS_BASE_DIR . '/lib/view/ntsPager.php' );
$app =& ntsApplication::getInstance();

$pager = new ntsPager( $NTS_VIEW['totalCount'], $NTS_VIEW['showPerPage'], 10 );
$pager->setPage( $NTS_VIEW['currentPage'] );

$pages = $pager->getPages();
reset( $pages );

$statusTitles = array(
	'approved'	=> M('Approved'),
	'pending'	=> M('Pending'),
	'noshow'	=> M('No Show'),
	'cancelled'	=> M('Cancelled'),
	);
?>

<?php 
$pagerParams = ( isset($NTS_VIEW['searchParams']) ) ?  $NTS_VIEW['searchParams'] : array();
?>

<?php if( ! (isset($NTS_VIEW['searchParams']['from']) || isset($NTS_VIEW['searchParams']['from']) ) ) : ?>

<?php	if( $NTS_VIEW['show'] == 'old' ) : ?>
<h2><?php echo M('Old Appointments'); ?></h2>
<?php	else : ?>
<h2><?php echo M('Upcoming Appointments'); ?></h2>
<?php	endif; ?>

<?php endif; ?>

<table>
<tr>
<td style="padding: 0 1em; vertical-align: top;">
<?php if( ! (isset($NTS_VIEW['searchParams']['from']) || isset($NTS_VIEW['searchParams']['from']) ) ) : ?>
	<?php if( $NTS_VIEW['show'] == 'old' ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'upcoming') ); ?>"><?php echo M('Upcoming Appointments'); ?></a>
	<?php else : ?>
		<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'old') ); ?>"><?php echo M('Old Appointments'); ?></a>
	<?php endif; ?>
<?php endif; ?>

<?php if( $NTS_VIEW['showFilter'] ) : ?>
	<p>
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
	<?php if( (isset($NTS_VIEW['fromTimestamp']) && isset($NTS_VIEW['toTimestamp'])) || (isset($NTS_VIEW['searchParams']['createdFrom']) && isset($NTS_VIEW['searchParams']['createdTo'])) ) : ?>
		<br>
	<?php endif; ?>
	
	<?php if( isset($NTS_VIEW['searchParams']['status']) ) : ?>
		<?php echo M('Status'); ?>: <b><?php echo $statusTitles[$NTS_VIEW['searchParams']['status']]; ?></b><br>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['service']) ) : ?>
		<?php echo M('Service'); ?>: <b><?php echo $NTS_VIEW['searchParams']['service']->getProp('title'); ?></b><br>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['location']) ) : ?>
		<?php echo M('Location'); ?>: <b><?php echo ntsView::objectTitle($NTS_VIEW['searchParams']['location']); ?></b><br>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['resource']) ) : ?>
		<?php echo M('Bookable Resource'); ?>: <b><?php echo $NTS_VIEW['searchParams']['resource']->getProp('title'); ?></b><br>
	<?php endif; ?>
	<?php if( isset($NTS_VIEW['searchParams']['customer']) ) : ?>
		<?php echo M('Customer'); ?>: <b><?php echo $NTS_VIEW['searchParams']['customer']->getProp('first_name'); ?> <?php echo $NTS_VIEW['searchParams']['customer']->getProp('last_name'); ?></b><br>
	<?php endif; ?>

<?php else: ?>
	<?php if ( $NTS_VIEW['fix'] ) : ?>
		&nbsp;<a href="<?php echo ntsLink::makeLink( $PANEL_PREFIX . '/search', '', array($NTS_VIEW['fix'] => $NTS_VIEW['fixId']) ); ?>"><?php echo M('Advanced Search'); ?></a>
	<?php endif; ?>
<?php endif; ?>

</td>

<?php if( count($NTS_VIEW['entries']) ) : ?>
	<td style="font-size: 70%; padding: 0 1em; vertical-align: bottom;">
		<?php 
			$overParams = $pagerParams;
			if( isset($overParams['service']) )
				$overParams['service'] = $overParams['service']->getId();
			if( isset($overParams['location']) )
				$overParams['location'] = $overParams['location']->getId();
		?>
		<?php echo M('Other Views & Export'); ?>:
		<?php $overParams['display'] = 'print'; ?>	
		<a target="_blank" href="<?php echo ntsLink::makeLink('-current-', '', $overParams ); ?>"><?php echo M('Print'); ?></a> 
		<?php $overParams['display'] = 'ical'; ?>	
		<a href="<?php echo ntsLink::makeLink('-current-', 'export', $overParams ); ?>">iCal</a>
		<?php $overParams['display'] = 'excel'; ?>	
		<a href="<?php echo ntsLink::makeLink('-current-', 'export', $overParams ); ?>">Excel</a>
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
	<td style="text-align: left;">
	<?php
	$form =& $ff->makeForm( dirname(__FILE__) . '/bulkActionsForm' );
	$form->display( array(), true );
	?>
	</td>

	<th><?php echo M('Legend'); ?>:</th>
	<td style="width: 1em; padding: 0;" class="ntsApproved">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Approved'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsPending">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Pending'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsNoShow">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('No Show'); ?></td>
	<td style="width: 1em; padding: 0;" class="ntsCancelled">&nbsp;</td>
	<td style="text-align: left; padding-left: 0.5em; padding-right: 2em;"><?php echo M('Cancelled'); ?></td>

	<td style="text-align: right;">
[<?php echo $NTS_VIEW['showFrom']; ?> - <?php echo $NTS_VIEW['showTo']; ?> of <?php echo $NTS_VIEW['totalCount']; ?>]
&nbsp;&nbsp;Pages: 
<?php foreach( $pages as $pi ): ?>
	<?php if( $NTS_VIEW['currentPage'] != $pi['number'] ) : ?>
		<?php $pagerParams['p'] = $pi['number']; ?>
		<a href="<?php echo ntsLink::makeLink('-current-', $NTS_VIEW['action'], $pagerParams ); ?>"><?php echo $pi['title']; ?></a>
	<?php else : ?>
		<b><?php echo $pi['title']; ?></b>
	<?php endif; ?>
<?php endforeach; ?>
	</td>
</tr>
</table>
