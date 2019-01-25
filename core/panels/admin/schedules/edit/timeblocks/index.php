<?php
global $NTS_READ_ONLY;

$ff =& ntsFormFactory::getInstance();
$duplicateFormFile = dirname( __FILE__ ) . '/duplicateForm';

$t = new ntsTime;

$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');

$scheduleInfo = $NTS_VIEW['scheduleInfo'];
$timeblocksInfo = $NTS_VIEW['timeblocksInfo'];
$limitDays = $NTS_VIEW['limitDays'];

$text_Weekdays = array( M('Sunday'), M('Monday'), M('Tuesday'), M('Wednesday'), M('Thursday'), M('Friday'), M('Saturday') );

$totalCount = 0;
?>

<table class="nts-listing">
<tr class="listing-header">
	<th><?php echo M('Weekday'); ?></th>
	<th><?php echo M('Working Time'); ?></th>
	<th>
<?php if( ! $NTS_READ_ONLY ) : ?>
	<?php echo M('Duplicate To'); ?>
<?php endif; ?>
	</th>
</tr>
<?php for( $i = 0; $i <= 6; $i++ ) : ?>
	<?php
	$dayIndex = $weekStartsOn + $i;
	$dayIndex = $dayIndex % 7;
	if( $limitDays && (! isset($limitDays[$dayIndex]) ) )
		continue;
	?>
<tr class="sub-header <?php echo ($totalCount % 2) ? 'even' : 'odd'; ?>">
	<?php
	$myTimeblocks = array();
	$count = 0;
	foreach( $timeblocksInfo as $tb ){
		if( $tb['applied_on'] != $dayIndex )
			continue;  
		$myTimeblocks[] = $tb;
		}
	?>
	<td>
		<b><?php echo $text_Weekdays[$dayIndex]; ?></b>
		<?php if ( isset($limitDays[$dayIndex]) ) : ?>
			<br><b><?php echo $limitDays[$dayIndex]; ?></b>
		<?php endif; ?>
		<br>
<?php if( ! $NTS_READ_ONLY ) : ?>
		<a class="ok" href="<?php echo ntsLink::makeLink('-current-/create', '', array('applied_on' => $dayIndex) ); ?>"><?php echo M('Time Slot'); ?>: <?php echo M('Add'); ?></a>
<?php endif; ?>

	</td>

	<?php if( ! $myTimeblocks ) : ?>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<?php $totalCount++; ?>
		</tr>
	<?php endif; ?>

	<?php foreach( $myTimeblocks as $tb ) : ?>
		<?php
		if( $tb['applied_on'] != $dayIndex )
			continue;
		$t = new ntsTime;
		$t->setDateTime( 2009, 12, 15, 0, 0, 0 );
		$t->modify( '+' . $tb['starts_at'] . ' seconds' );
		$startFormatted = $t->formatTime();
		$t->modify( '+' . ($tb['ends_at'] - $tb['starts_at']) . ' seconds' );
		$endFormatted = $t->formatTime();

		$tbObject = new ntsObject( 'timeblock' );
		$tbObject->setId( $tb['id'] );
		?>
	<?php if( $count ) : ?>
		<tr class="<?php echo ($totalCount % 2) ? 'even' : 'odd'; ?>">
		<td>&nbsp;</td>
	<?php endif; ?>
		<td style="white-space: normal; width: 24em;">
<?php if( ! $NTS_READ_ONLY ) : ?>
			<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('id' => $tb['id']) ); ?>"><b><?php echo $startFormatted; ?> - <?php echo $endFormatted; ?></b></a>
			<a style="margin: 0 10px; text-decoration: none; font-weight: bold;" class="alert" href="<?php echo ntsLink::makeLink('-current-/delete', '', array('timeblock_id' => $tb['id']) ); ?>">[X]</a>
<?php else : ?>
			<b><?php echo $startFormatted; ?> - <?php echo $endFormatted; ?></b>
<?php endif; ?>

			<br>
			<?php if( $tb['selectable_every'] ) : ?>
				<?php echo M('Every'); ?> <?php echo $tb['selectable_every'] / 60; ?> <?php echo M('Minutes'); ?>
			<?php else : ?>
				<?php if( count($tb['selectable_fixed']) ) : ?>
					<?php
					$t = new ntsTime();
					$showTimes = array();
					foreach( $tb['selectable_fixed'] as $ts ){
						$t->setDateTime( 2011, 1, 14, 0, 0, 0 );
						$t->modify( '+' . $ts . ' seconds' );
						$showTimes[] = $t->formatTime();
						}
					?>
					<?php echo join( $showTimes, ', ' ); ?>
				<?php else : ?>
					<span class="alert"><?php echo M('No Selectable Times'); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</td>

		<td>
		<?php if( $NTS_READ_ONLY || $count ) : ?>
			&nbsp;
		<?php else : ?>
			<?php
			$params = array(
				'day_from'	=> $dayIndex,
				);
			$duplicateForm =& $ff->makeForm( $duplicateFormFile, $params, $dayIndex );
			?>
			<?php $duplicateForm->display(); ?>
		<?php endif; ?>
		</td>
		
		</tr>
		<?php 
		$count++;
		$totalCount++;
		?>
	<?php endforeach; ?>

</tr>
<?php endfor; ?>
</table>
