<?php
$slotsPerHour = 60 / NTS_TIME_UNIT;

// get previous and next links
$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] );
$t->modify( '-1 day' );
$previousLinkCal = $t->formatDate_Db();
$t->setTimestamp( $NTS_VIEW['SLOTS'][count($NTS_VIEW['SLOTS']) - 1][1] + 1 );
$nextLinkCal = $t->formatDate_Db();

$selectedDay = $NTS_VIEW['selectedDay'];
$slotsPerDay = ( 24 * 60 ) / NTS_TIME_UNIT;

$FINAL_HIDE_SLOTS = array();
$hiddenHolder = array();
for( $h = 0; $h < 24; $h++ ){
	$hideSlot = true;
	for( $i = 0; $i < $slotsPerHour; $i++ ){
		$s = $h*$slotsPerHour + $i;
		for( $j = 0; $j < $resourceCount; $j++ ){
			$thisResId = $RESOURCE_IDS[ $j ];
			if(
				( ($NTS_VIEW['APPS'][$s][$thisResId][0] + $NTS_VIEW['APPS'][$s][$thisResId][1]) )
				||
				( $NTS_VIEW['WORKING_TIMES'][$s][$thisResId] )
				){
				$hideSlot = false;
				break;
				}
			}
		if( ! $hideSlot )
			break;
		}

	if( $hideSlot ){
		if( $hiddenHolder )
			$hiddenHolder[ 1 ] = $h;
		else
			$hiddenHolder = array( $h, $h );
		}
	else {
		if( $hiddenHolder ){
			$FINAL_HIDE_SLOTS[] = $hiddenHolder;
			$hiddenHolder = array();
			}
		}
	}
if( $hiddenHolder ){
	$FINAL_HIDE_SLOTS[] = $hiddenHolder;
	$hiddenHolder = array();
	}
?>
<table>
<tr>
<th class="arrow"><h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $previousLinkCal) ); ?>">&lt;&lt;</a></h3></th>
<th colspan="<?php echo $slotsPerHour; ?>" style="width: auto;">
<?php $t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] ); ?>
<h3><?php echo $t->formatWeekday(); ?><br><?php echo $t->formatDate(); ?></h3>
</th>
<th class="arrow"><h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $nextLinkCal) ); ?>">&gt;&gt;</a></h3></th>
</tr>

<tr>
<td></td>
<?php
$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] );
$thisDate = $t->formatDate_Db();
$slotClass = 'holder';
?>
<td class="<?php echo $slotClass; ?>" colspan="<?php echo $slotsPerHour; ?>">

<?php
$slotsStart = 0;
$slotsEnd = $slotsPerDay - 1;

$slotClass = '';
$dayWorking = false;
$dayMayBeWorking = false;
$daySelectable = false;
$dayAppsApproved = 0;
$dayAppsPending = 0; 
$appsSeen = array();
$timeoffDetected = false;
$dayTimeoff = false;

for( $ii = 0; $ii < $slotsPerDay; $ii++ ){
	if( ! $dayWorking ){
		reset( $NTS_VIEW['WORKING_TIMES'][$ii] );
		foreach( $NTS_VIEW['WORKING_TIMES'][$ii] as $ri => $value ){
			if( $value ){
				$dayMayBeWorking = true;
				if( $NTS_VIEW['TIMEOFFS'][$ii][$ri] ){
					$timeoffDetected = true;
					}
				else {
					$dayWorking = true;
					break;
					}
				}
			}
		}
	if( ! $daySelectable ){
		reset( $NTS_VIEW['SELECTABLE_TIMES'][$ii] );
		foreach( $NTS_VIEW['SELECTABLE_TIMES'][$ii] as $ri => $value ){
			if( $value ){
				$daySelectable = true;
				break;
				}
			}
		}
	}
if( $dayMayBeWorking && $timeoffDetected ){
	$dayTimeoff = true;
	}

reset( $NTS_VIEW['APPS_BY_DATE'][$thisDate] );
foreach( $NTS_VIEW['APPS_BY_DATE'][$thisDate] as $resId => $thisApps ){
	$dayAppsApproved += $thisApps[0];
	$dayAppsPending += $thisApps[1];
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
		$slotClass .= ' fullbook';
		}
	}
elseif ( $dayTimeoff ){
	$slotClass .= ' timeoff';
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
<ul><li>
<div class="apps-h apps-h-alone<?php echo $slotClass; ?>">
<?php if( ($dayAppsApproved + $dayAppsPending) > 0 ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/list-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$slotsStart][0], 'to' => $NTS_VIEW['SLOTS'][$slotsEnd][1], 'location' => $NTS_VIEW['location']->getId()) ); ?>"><?php echo ($dayAppsApproved + $dayAppsPending); ?></a>
<?php elseif( $dayTimeoff ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/timeoff-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$slotsStart][0], 'to' => $NTS_VIEW['SLOTS'][$slotsEnd][1]) ); ?>">&nbsp;</a>
<?php else : ?>
&nbsp;
<?php endif; ?>
</div>
</li></ul>
</td>
<td></td>
</tr>

<!-- FULL DAY VIEW STARTS -->

<?php for( $h = 0; $h < 24; $h++ ) : ?>
<!-- hide links -->

<tr id="time-row-<?php echo $h; ?>">
<td></td>

<?php for( $i = 0; $i < $slotsPerHour; $i++ ) : ?>

<?php
$SI = $h * $slotsPerHour + $i;
$t->setTimestamp( $NTS_VIEW['SLOTS'][$SI][0] );
?>
<td class="hours">
<?php if( $viewSplit ) : ?>

	<?php if( $resourceCount > 1 ) : ?>
		<?php echo $t->formatTime(); ?>
	<?php else : ?>
	<?php 
		$linkClass = '';
		if( $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId] )
			$linkClass .= ' selectable';
		else
			$linkClass .= ' not-selectable';

		$createParams = array(
			'location'	=> $NTS_VIEW['location']->getId(),
			'resource'	=> $thisResId,
			'time'		=> $NTS_VIEW['SLOTS'][$SI][0],
			);
		if( $NTS_VIEW['RESCHEDULE'] ){
			$createParams[ 'reschedule' ] = $NTS_VIEW['RESCHEDULE']['obj']->getId();
			}
		if( $NTS_VIEW['customer'] ){
			$createParams[ 'customer' ] = $NTS_VIEW['customer']->getId();
			}
	?>
	<div class="<?php echo $linkClass; ?>">
	<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/create-form', '', $createParams ); ?>"><?php echo $t->formatTime(); ?></a>
	</div>
	<?php endif; ?>

<?php else : ?>

	<?php 
		$linkClass = 'not-selectable';
		for( $j = 0; $j < $resourceCount; $j++ ){
			$thisResId = $RESOURCE_IDS[ $j ];
			if( $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId] ){
				$linkClass = 'selectable';
				break;
				}
			}

		if( $resourceCount > 1 ){
			$createParams = array(
				'location'	=> $NTS_VIEW['location']->getId(),
				'time'		=> $NTS_VIEW['SLOTS'][$SI][0],
				);
			$panel = '-current-/ajax/select-resource';
			}
		else {
			$createParams = array(
				'location'	=> $NTS_VIEW['location']->getId(),
				'resource'	=> $thisResId,
				'time'		=> $NTS_VIEW['SLOTS'][$SI][0],
				);
			$panel = '-current-/ajax/create-form';
			}

		if( $NTS_VIEW['RESCHEDULE'] ){
			$createParams[ 'reschedule' ] = $NTS_VIEW['RESCHEDULE']['obj']->getId();
			}
		if( $NTS_VIEW['customer'] ){
			$createParams[ 'customer' ] = $NTS_VIEW['customer']->getId();
			}
	?>
	<div class="<?php echo $linkClass; ?>">
	<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink($panel, '', $createParams ); ?>"><?php echo $t->formatTime(); ?></a>
	</div>

<?php endif; ?>
</td>
<?php endfor; ?>

<td></td>
</tr>

<?php reset( $FINAL_HIDE_SLOTS );
?>
<?php foreach( $FINAL_HIDE_SLOTS as $fhs ) : ?>
	<?php if( $fhs[0] == $h ) : ?>
<?php
$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] + $fhs[0] * 60 * 60 );
$startTime = $t->formatTime();
$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] + ($fhs[1] + 1) * 60 * 60 );
$endTime = $t->formatTime();
?>
<tr id="expander-row-<?php echo $h; ?>">
<td></td>
<td colspan="<?php echo $slotsPerHour; ?>" class="hideRow">
<a id="nts-expand-<?php echo $fhs[0] . '-' . $fhs[1]; ?>" href="#"><?php echo M('Show'); ?>: <?php echo $startTime; ?> - <?php echo $endTime; ?></a>
</td>
<td></td>
</tr>
	<?php endif;?>
<?php endforeach; ?>

<tr id="slot-row-<?php echo $h; ?>">
<td></td>

<?php for( $i = 0; $i < $slotsPerHour; $i++ ) : ?>

<?php $SI = $h * $slotsPerHour + $i; ?>
<td class="holder">
<ul>

<?php if( $viewSplit ) : ?>

<?php for( $j = 0; $j < $resourceCount; $j++ ) : ?>
<?php
$thisResId = $RESOURCE_IDS[ $j ];
$slotClass = '';
$thisAppsCount = $NTS_VIEW['APPS'][$SI][$thisResId][0] + $NTS_VIEW['APPS'][$SI][$thisResId][1];
$thisCapacity = $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId];
$thisTimeoff = false;

if( $thisCapacity ){
	if( $thisAppsCount > 0 ){
		reset( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] );
		foreach( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] as $servId => $servCapacity ){
			$servApps = isset( $NTS_VIEW['APPS_BY_SERVICE'][$SI][$thisResId][$servId] ) ? $NTS_VIEW['APPS_BY_SERVICE'][$SI][$thisResId][$servId] : 0;
			if( $servApps >= $servCapacity ){
				$slotClass .= ' fullbook';
				break;
				}
			}
		if( ! $slotClass )
			$slotClass .= ' partbook';
		}
	else {
		if( $NTS_VIEW['TIMEOFFS'][$SI][$thisResId] ){
			$slotClass .= ' timeoff';
			$thisTimeoff = true;
			}
		else
			$slotClass .= ' working';
		}
	}
else {
	if( $thisAppsCount > 0 ){
		$slotClass .= ' fullbook';
		}
	}

if( $NTS_VIEW['APPS'][$SI][$thisResId][1] > 0 ){
	$slotClass .= ' pending';
	}

?>
<li>
<?php if( $resourceCount > 1 ) : ?>
<?php
	$linkClass = '';
	if( $NTS_VIEW['SELECTABLE_TIMES'][$SI][$thisResId] )
		$linkClass .= ' selectable';
	else
		$linkClass .= ' not-selectable';
	$createParams = array(
		'location'	=> $NTS_VIEW['location']->getId(),
		'resource'	=> $thisResId,
		'time'		=> $NTS_VIEW['SLOTS'][$SI][0],
		);
	if( $NTS_VIEW['RESCHEDULE'] ){
		$createParams[ 'reschedule' ] = $NTS_VIEW['RESCHEDULE']['obj']->getId();
		}
	if( $NTS_VIEW['customer'] ){
		$createParams[ 'customer' ] = $NTS_VIEW['customer']->getId();
		}
?>
<div class="res-h<?php echo $linkClass; ?>"><a title="<?php echo $RESOURCE_TITLES[$j]; ?>" class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/create-form', '', $createParams); ?>"><?php echo $RESOURCE_TITLES[$j]; ?></a></div>
<div class="apps-h<?php echo $slotClass; ?>">
<?php else : ?>
<div class="apps-h apps-h-alone<?php echo $slotClass; ?>">
<?php endif ; ?>
<?php 	if( $thisAppsCount > 0 ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/list-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'resource' => $thisResId, 'location' => $NTS_VIEW['location']->getId()) ); ?>"><?php if( $thisCapacity > 1 ){echo $thisAppsCount;} else {echo $thisAppsCount;} ?></a>
<?php	elseif( $thisTimeoff ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/timeoff-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'resource' => $thisResId) ); ?>">&nbsp;</a>
<?php 	else : ?>
&nbsp;
<?php 	endif; ?>
<?php if( $resourceCount > 1 ) : ?>
	</div></div>
<?php else : ?>
	</div>
<?php endif ; ?>

</li>
<?php endfor; ?>

<?php else : ?>

<?php
$slotClasses = array();
$thisAppsCount = 0;
$thisTimeoff = false;

$slotClasses['timeoff'] = 0;

for( $j = 0; $j < $resourceCount; $j++ ){
	$thisResId = $RESOURCE_IDS[ $j ];
	$myAppsCount = $NTS_VIEW['APPS'][$SI][$thisResId][0] + $NTS_VIEW['APPS'][$SI][$thisResId][1];
	$myCapacity = $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId];
	$thisAppsCount += $myAppsCount;

	if( $NTS_VIEW['TIMEOFFS'][$SI][$thisResId] ){
		$slotClasses['timeoff']++;
		}

	if( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] ){
		if( $myAppsCount > 0 ){
			reset( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] );
			foreach( $NTS_VIEW['WORKING_TIMES'][$SI][$thisResId] as $servId => $servCapacity ){
				$servApps = isset( $NTS_VIEW['APPS_BY_SERVICE'][$SI][$thisResId][$servId] ) ? $NTS_VIEW['APPS_BY_SERVICE'][$SI][$thisResId][$servId] : 0;
				if( $servApps >= $servCapacity ){
					$slotClasses['fullbook'] = 1;
					break;
					}
				}
			if( ! isset($slotClasses['fullbook']) )
				$slotClasses['partbook'] = 1;
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

if( $slotClasses['timeoff']	>= $resourceCount ){
	$slotClass .= ' timeoff';
	$thisTimeoff = true;
	}
else {
	if( isset($slotClasses['working']) ){
		if( isset($slotClasses['fullbook']) || isset($slotClasses['partbook']) )
			$slotClass .= ' partbook';
		else
			$slotClass .= ' working';
		}
	elseif( isset($slotClasses['partbook']) ){
		$slotClass .= ' partbook';
		}
	elseif( isset($slotClasses['fullbook']) ){
		$slotClass .= ' fullbook';
		}
	}
?>

<li>

<div class="apps-h apps-h-alone<?php echo $slotClass; ?>">

<?php 	if( $thisAppsCount > 0 ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/list-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1], 'location' => $NTS_VIEW['location']->getId()) ); ?>"><?php echo $thisAppsCount; ?></a>
<?php	elseif( $thisTimeoff ) : ?>
<a class="nts-ajax-link" href="<?php echo ntsLink::makeLink('-current-/ajax/timeoff-from-to', '', array('from' => $NTS_VIEW['SLOTS'][$SI][0], 'to' => $NTS_VIEW['SLOTS'][$SI][1]) ); ?>">&nbsp;</a>
<?php 	else : ?>
&nbsp;
<?php 	endif; ?>

</div>
</li>

<?php endif; ?>

</ul>

</td>

<?php endfor; ?>

<td></td>
</tr>

<?php reset( $FINAL_HIDE_SLOTS ); ?>
<?php foreach( $FINAL_HIDE_SLOTS as $fhs ) : ?>
	<?php if( $fhs[1] == $h ) : ?>
		<?php
		$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] + $fhs[0] * 60 * 60 );
		$startTime = $t->formatTime();
		$t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] + ($fhs[1] + 1) * 60 * 60 );
		$endTime = $t->formatTime();
		?>
<tr id="collapser-row-<?php echo $h; ?>">
<td></td>
<td colspan="<?php echo $slotsPerHour; ?>" class="hideRow">
<a id="nts-collapse-<?php echo $fhs[0] . '-' . $fhs[1]; ?>" href="#"><?php echo M('Hide'); ?>: <?php echo $startTime; ?> - <?php echo $endTime; ?></a>
</td>
<td></td>
</tr>
	<?php endif;?>
<?php endforeach; ?>

<?php endfor; ?>

<tr>
<th class="arrow"><h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $previousLinkCal) ); ?>">&lt;&lt;</a></h3></td>
<th colspan="<?php echo $slotsPerHour; ?>" style="width: auto;">
<?php $t->setTimestamp( $NTS_VIEW['SLOTS'][0][0] ); ?>
<h3><?php echo $t->formatWeekday(); ?><br><?php echo $t->formatDate(); ?></h3>
</th>
<th class="arrow"><h3><a href="<?php echo ntsLink::makeLink('-current-', '', array('cal' => $nextLinkCal) ); ?>">&gt;&gt;</a></h3></td>
</tr>

<!-- FULL DAY VIEW ENDS -->
</table>

<script language="javascript">
var ntsExpanderPrefix = 'nts-expand-';
var ntsCollapserPrefix = 'nts-collapse-';

jQuery("#nts-create-appointment").mouseover(function(){
	jQuery(this).show();
	});
jQuery("#nts-create-appointment").mouseout(function(){
	jQuery(this).hide();
	});

<?php foreach( $FINAL_HIDE_SLOTS as $fs ) : ?>
<?php 	for( $i = $fs[0]; $i <= $fs[1]; $i++ ) : ?>
jQuery("#time-row-<?php echo $i;?>").hide();
jQuery("#slot-row-<?php echo $i;?>").hide();
<?php	endfor; ?>

<?php	if( $fs[1] == ($slotsPerDay - 1) ): ?>
jQuery("#time-row-<?php echo ($fs[1] + 1);?>").hide();
<?php	endif; ?>

<?php endforeach; ?>

jQuery("tr[id^=collapser-row-]").hide();

/* expander */
jQuery("a[id^=" + ntsExpanderPrefix + "]").click(function() {
	var startStopString = this.id.substring( ntsExpanderPrefix.length );
	var startStop = startStopString.split( '-', 2 );

	jQuery("#expander-row-" + startStop[0]).hide();
	startStop[0] = parseInt(startStop[0]);
	startStop[1] = parseInt(startStop[1]);
	for( i = startStop[0]; i <= startStop[1]; i++ ){
		jQuery("#time-row-" + i).fadeIn(500);
		jQuery("#slot-row-" + i).fadeIn(500);
		}

	jQuery("#collapser-row-" + startStop[0]).fadeIn(500);
	jQuery("#collapser-row-" + startStop[1]).fadeIn(500);
	return false;
	});

/* collapser */
jQuery("a[id^=" + ntsCollapserPrefix + "]").click(function() {
	var startStopString = this.id.substring( ntsCollapserPrefix.length );
	var startStop = startStopString.split( '-', 2 );

	jQuery("#collapser-row-" + startStop[0]).hide();
	jQuery("#collapser-row-" + startStop[1]).hide();

	startStop[0] = parseInt(startStop[0]);
	startStop[1] = parseInt(startStop[1]);
	for( i = startStop[0]; i <= startStop[1]; i++ ){
		if( (i != startStop[0]) || (i == 0) ){
			jQuery("#time-row-" + i).fadeOut(500);
			}
		jQuery("#slot-row-" + i).fadeOut(500);
		}
	jQuery("#expander-row-" + startStop[0]).fadeIn(500);
	return false;
	});	
</script>