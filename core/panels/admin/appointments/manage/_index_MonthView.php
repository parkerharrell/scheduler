<?php
include_once( NTS_BASE_DIR . '/lib/datetime/ntsCalendar.php' );

$calYear = $NTS_VIEW['calYear']; 
$calMonth = $NTS_VIEW['calMonth'];
$calDay = $NTS_VIEW['calDay'];
$selectedDay = $NTS_VIEW['selectedDay'];

$ntsConf =& ntsConf::getInstance();
$weekStartsOn = $ntsConf->get('weekStartsOn');
$text_Monthnames = array( M('Jan'), M('Feb'), M('Mar'), M('Apr'), M('May'), M('Jun'), M('Jul'), M('Aug'), M('Sep'), M('Oct'), M('Nov'), M('Dec') );
$text_Weekdays = array( M('Sun'), M('Mon'), M('Tue'), M('Wed'), M('Thu'), M('Fri'), M('Sat') );
$calendar = new ntsCalendar();

$showMonths = 1;

$t->setDateTime( $calYear, $calMonth - $showMonths, 1, 0, 0, 0 );
$previousMo = $t->formatDate_Db();
$t->setDateTime( $calYear, $calMonth + $showMonths, 1, 0, 0, 0 );
$nextMo = $t->formatDate_Db();
?>

<table class="month">
<?php for( $k = 0; $k < $showMonths; $k++ ) : ?>
<?php
	$monthMatrix = $calendar->getMonthMatrix( $calYear, $calMonth );
?>
<tr>
<td class="arrow">
<?php	if( $k == 0 ) : ?>
<h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $previousMo) ); ?>">&lt;&lt;</a></h3>
<?php	else : ?>
&nbsp;
<?php	endif; ?>
</td>

<td colspan="7" style="width: auto;" class="month">
	<h2><?php echo $text_Monthnames[ $calMonth - 1 ]; ?> <?php echo $calYear; ?></h2>
</td>

<td class="arrow">
<?php 	if( $k == ($showMonths-1) ) : ?>
<h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $nextMo) ); ?>">&gt;&gt;</a></h3>
<?php 	else : ?>
&nbsp;
<?php 	endif; ?>
</td>
</tr>

<tr>
<td></td>
<?php for( $i = 0; $i <= 6; $i++ ) : ?>
<?php
		$dayIndex = $weekStartsOn + $i;
		$dayIndex = $dayIndex % 7;
?>
<td><h3><?php echo $text_Weekdays[$dayIndex]; ?></h3></td>
<?php endfor; ?>
<td></td>
</tr>

<?php $SI = 0; ?>
<?php foreach( $monthMatrix as $week => $days ) : ?>
<tr>
<td></td>
<?php 	foreach( $days as $day ) : ?>
<?php 		if( $day ) : ?>
<?php
			$t->setDateTime( $calYear, $calMonth, $day, 0, 0, 0 );
			$thisDate = $t->formatDate_Db();
?>
<td class="date-holder<?php if( $selectedDay == $thisDate ){echo ' today';} ?>">
<h3><a title="<?php echo $t->formatDate(); ?>" href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $thisDate, 'viewPeriod' => 'day') ); ?>"><?php echo $day; ?></a></h3>
</td>
<?php 		else : ?>
<td>&nbsp;</td>
<?php 		endif; ?>
<?php 	endforeach; ?>
<td></td>
</tr>

<tr>
<td></td>
<?php 	reset( $days ); ?>
<?php 	foreach( $days as $day ) : ?>
<?php 		if( $day ) : ?>
<?php
				$d = $day - 1;
				$t->setDateTime( $calYear, $calMonth, $day, 0, 0, 0 );
				$thisDate = $t->formatDate_Db();
?>
<td>
<ul>

<?php if( $viewSplit ) : ?>

<!-- START RESOURCES ITERATION -->
<?php for( $j = 0; $j < $resourceCount; $j++ ) : ?>
<?php
	$dayWorking = false;
	$dayMayBeWorking = false;
	$daySelectable = false;
	$dayAppsApproved = 0;
	$dayAppsPending = 0; 
	$appsSeen = array();
	$timeoffDetected = false;
	$dayTimeoff = false;

	$thisResId = $RESOURCE_IDS[ $j ];
	$thisAppsCount = $NTS_VIEW['APPS'][$SI][$thisResId][0] + $NTS_VIEW['APPS'][$SI][$thisResId][1];
	$slotClass = '';
	if( ! $dayWorking ){
		if( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] ){
			$dayWorking = true;
			if( $NTS_VIEW['TIMEOFFS'][$SI][$thisResId] ){
				$dayTimeoff = true;
				}
			}
		}
	if( ! $daySelectable ){
		if( $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId] ){
			$daySelectable = true;
			}
		}

	if( $dayWorking ){
		if( $daySelectable ){
			if( ($dayAppsApproved + $dayAppsPending) > 0 ){
				$slotClass .= ' partbook';
				}
			else {
				$slotClass .= ' working';
				}
			}
		else {
			if( $dayTimeoff )
				$slotClass .= ' timeoff';
			else
				$slotClass .= ' fullbook';
			}
		}
	else {
		if( ($dayAppsApproved + $dayAppsPending) > 0 ){
			$slotClass .= ' fullbook';
			}
		}

	if( $dayAppsPending > 0 ){
		$slotClass .= ' pending';
		}
?>

<li>
<?php if( $resourceCount > 1 ) : ?>
<div class="res-h"><span><?php echo $RESOURCE_TITLES[$j]; ?></span></div>
<div class="apps-h<?php echo $slotClass; ?>">
<?php else : ?>
<div class="apps-h apps-h-alone<?php echo $slotClass; ?>">
<?php endif; ?>

<?php if( $thisAppsCount > 0 ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/list-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'resource' => $thisResId, 'location' => $NTS_VIEW['location']->getId()) ); ?>"><?php echo $thisAppsCount; ?></a>
<?php elseif( $dayTimeoff ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/timeoff-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'resource' => $thisResId) ); ?>">&nbsp;</a>
<?php else : ?>
&nbsp;
<?php endif; ?>

<?php if( $resourceCount > 1 ) : ?>
</div></div>
<?php else : ?>
</div>
<?php endif ; ?>
</li>

<?php endfor; ?>
<!-- END RESOURCES ITERATION -->

<?php else : ?>

<!-- START RESOURCES ITERATION -->
<?php
$slotClasses = array();
$thisAppsCount = 0;

$slotClasses['timeoff'] = 0;
for( $j = 0; $j < $resourceCount; $j++ ){
	$thisResId = $RESOURCE_IDS[ $j ];
	$myAppsCount = $NTS_VIEW['APPS'][$SI][$thisResId][0] + $NTS_VIEW['APPS'][$SI][$thisResId][1];
	$myCapacity = $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId];
	$thisAppsCount += $myAppsCount;

	if( $NTS_VIEW['TIMEOFFS'][$SI][$thisResId] && (! $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId]) ){
		$slotClasses['timeoff']++;
		}

	if( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] ){
		if( $myAppsCount > 0 ){
			if( $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId] ){
				$slotClasses['partbook'] = 1;
				}
			else {
				$slotClasses['fullbook'] = 1;
				}
			}
		else {
			$slotClasses['working'] = 1;
			}
		}
	else {
		if( $myAppsCount > 0 ){
			$slotClasses['fullbook'] = 1;
			}
		}

	if( $NTS_VIEW['APPS'][$SI][$thisResId][1] > 0 ){
		$slotClasses['pending'] = 1;
		}
	}

$slotClass = '';
if( isset($slotClasses['pending']) )
	$slotClass .= ' pending';

if( isset($slotClasses['working']) ){
	if( $slotClasses['timeoff'] >= $resourceCount ){
		$slotClass .= ' timeoff';
		}
	else {
		if( isset($slotClasses['fullbook']) || isset($slotClasses['partbook']) )
			$slotClass .= ' partbook';
		else
			$slotClass .= ' working';
		}
	}
elseif( isset($slotClasses['partbook']) ){
	$slotClass .= ' partbook';
	}
elseif( isset($slotClasses['fullbook']) ){
	$slotClass .= ' fullbook';
	}
?>

<li>
<div class="apps-h apps-h-alone<?php echo $slotClass; ?>">
<?php if( $thisAppsCount > 0 ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/list-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'location' => $NTS_VIEW['location']->getId()) ); ?>"><?php echo $thisAppsCount; ?></a>
<?php else : ?>
&nbsp;
<?php endif; ?>
</div>
</li>

<?php endif; ?>

</ul>

</td>
<?php 		$SI++; ?>
<?php 		else : ?>
<td>&nbsp;</td>
<?php 		endif; ?>
<?php 	endforeach; ?>
<td></td>
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