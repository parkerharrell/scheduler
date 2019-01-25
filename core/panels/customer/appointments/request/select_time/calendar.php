<?php
$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');
$text_Monthnames = array( M('Jan'), M('Feb'), M('Mar'), M('Apr'), M('May'), M('Jun'), M('Jul'), M('Aug'), M('Sep'), M('Oct'), M('Nov'), M('Dec') );
$text_Weekdays = array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') );
$calendar = new ntsCalendar();

$selectedDay = $NTS_VIEW['cal'];

//$tt = new ntsTime( $times[0], $NTS_CURRENT_USER->getTimezone() );

$showMonths = $ntsConf->get('monthsToShow');

$t->setDateTime( $calYear, $calMonth - $showMonths, 1, 0, 0, 0 );
$previousMo = $t->formatDate_Db();
$t->setDateTime( $calYear, $calMonth + $showMonths, 1, 0, 0, 0 );
$nextMo = $t->formatDate_Db();

$currentCalendar = array();
reset( $NTS_CURRENT_REQUEST );
foreach( $NTS_CURRENT_REQUEST as $cr ){
	$currentCalendar[] = $cr['cal'];
	}
?>

<div class="nts-calendar">
<table>

<?php for( $k = 0; $k < $showMonths; $k++ ) : ?>
	<?php
	$monthMatrix = $calendar->getMonthMatrix( $calYear, $calMonth );
	?>

	<tr class="months">
	<td>
		<?php if( $k == 0 ) : ?>
			<?php $currentCalendar[ $NTS_CURRENT_REQUEST_INDEX ] = $previousMo; ?>
			<a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => join('-', $currentCalendar)) ); ?>">&lt;</a>
		<?php else : ?>
			&nbsp;
		<?php endif; ?>
	</td>

	<td colspan="5" style="width: auto;">
		<?php echo $text_Monthnames[ $calMonth - 1 ]; ?> <?php echo $calYear; ?>
	</td>

	<td>
		<?php if( $k == ($showMonths-1) ) : ?>
			<?php $currentCalendar[ $NTS_CURRENT_REQUEST_INDEX ] = $nextMo; ?>
			<a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => join('-', $currentCalendar)) ); ?>">&gt;</a>
		<?php else : ?>
			&nbsp;
		<?php endif; ?>
	</td>
	</tr>

	<tr class="days">
	<?php for( $i = 0; $i <= 6; $i++ ) : ?>
		<?php
		$dayIndex = $weekStartsOn + $i;
		$dayIndex = $dayIndex % 7;
		?>
		<td>
		<div><?php echo $text_Weekdays[$dayIndex]; ?></div>
		</td>
	<?php endfor; ?>
	</tr>

	<?php foreach( $monthMatrix as $week => $days ) : ?>
	<tr>
		<?php foreach( $days as $day ) : ?>
		<?php if( $day ) : ?>
			<?php
			$thisDate = ntsTime::formatDateParam( $calYear, $calMonth, $day );
 			$ok = ( isset($dates[$thisDate]) && $dates[$thisDate] ) ? true : false;
			$class = '';
			$class .= ( $ok ) ? ' available' : 'not_available';
			$class .= ( $selectedDay == $thisDate ) ? ' selected' : '';			
			?>
			<td>
			<div class="<?php echo $class; ?>">
			<?php if( $ok ) : ?>
				<?php $currentCalendar[ $NTS_CURRENT_REQUEST_INDEX ] = ntsTime::formatDateParam($calYear, $calMonth, $day); ?>
				<a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => join('-', $currentCalendar)) ); ?>"><?php echo $day; ?></a>
			<?php else : ?>
				<?php echo $day; ?>
			<?php endif; ?>
			</div>
			</td>
		<?php else : ?>
			<td>
			&nbsp;
			</td>
		<?php endif; ?>
		<?php endforeach; ?>
	</tr>
	<?php endforeach; ?>
<?php
	$calMonth++;
	if( $calMonth > 12 ){
		$calMonth = 1;
		$calYear++;
		}
?>
<?php endfor; ?>

</table>
</div>
