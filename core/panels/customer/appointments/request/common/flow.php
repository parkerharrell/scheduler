<?php
global $NTS_CURRENT_REQUEST, $NTS_CURRENT_REQUEST_INDEX, $NTS_CURRENT_REQUEST_WHAT;
$conf =& ntsConf::getInstance();

$additionalHeader = '';
$rowsCount = ( count($NTS_CURRENT_REQUEST) > 1 ) ? count($NTS_CURRENT_REQUEST) : 1;

/* if pack then additional header */
$packType = '';
if( $NTS_VIEW['PACK'] ){
	$myValue = array();
	$rawValue = $NTS_VIEW['PACK']->getProp('discount'); 
	if( preg_match('/discount/', $rawValue)){
		$myValue = explode( ':', $rawValue );
		$packType = 'discount';
		}
	elseif( preg_match('/price/', $rawValue)){
		$myValue = explode( ':', $rawValue );
		$packType = 'price';
		}
	else{
		$myValue = array( $rawValue, '' );
		}
	$additionalHeader = $NTS_VIEW['PACK']->getProp( 'title' ) . ': ';
	if( $myValue[0] == 'discount' ){
		$additionalHeader .= M('Discount') . ' <b>' . $myValue[1] . '%</b>';
		}
	elseif( $myValue[0] == 'price' ){
		$additionalHeader .= M('Total Price') . ' <b>';
		if( ($NTS_CURRENT_REQUEST[0]['seats'] > 1) ){
			$mySeats = $NTS_CURRENT_REQUEST[0]['seats'];
			$additionalHeader .= ntsCurrency::formatServicePrice($mySeats * $myValue[1]) . ' [' . $mySeats . ' x ' . ntsCurrency::formatServicePrice($myValue[1]) . ']';
			}
		else {
			$additionalHeader .= ntsCurrency::formatServicePrice($myValue[1]);
			}
		$additionalHeader .= '</b>';
		}
	elseif( $myValue[0] == 'onefree' ){
		$additionalHeader .= M('One Appointment Free');
		}
	}

$showTotalPrice = true;

if( $rowsCount > 1 ){
	for( $i = 0; $i < $rowsCount; $i++ ){
		if( ! ($NTS_CURRENT_REQUEST[$i]['service'] && $NTS_CURRENT_REQUEST[$i]['service']) ){
			$showTotalPrice = false;
			break;
			}
		}
	}
else {
	$showTotalPrice = false;
	}

if( $NTS_VIEW['PACK'] ){
	$rawValue = $NTS_VIEW['PACK']->getProp( 'discount' );
	$packValue = array();
	if( preg_match('/discount/', $rawValue))
		$packValue = explode( ':', $rawValue );
	elseif( preg_match('/price/', $rawValue))
		$packValue = explode( ':', $rawValue );
	else
		$packValue = array( $rawValue, '' );

	if( $packValue[0] == 'price' ){
		$showTotalPrice = true;
		}
	}

/* get prices */
if( ! $NTS_VIEW['RESCHEDULE'] )
	list( $discountedPrices, $fullPrices, $totalDiscountedPrice, $totalFullPrice ) = ntsLib::getPackPrice( $NTS_CURRENT_REQUEST, $NTS_VIEW['PACK'] );

$ntsdb =& dbWrapper::getInstance();

$t = $NTS_VIEW['t'];

$showResource = ( (! NTS_SINGLE_RESOURCE) ) ? true : false;
$showLocation = ( (! NTS_SINGLE_LOCATION) ) ? true : false;

$showSessionDuration = $conf->get('showSessionDuration');
$enableDiscount = false;

$reallyShow = true;

$flowTitles = array();
$conf =& ntsConf::getInstance();
$confFlow = $conf->get('appointmentFlow');

reset( $confFlow );
foreach( $confFlow as $f ){
	switch( $f[0] ){
		case 'seats':
			continue;
			break;

		case 'resource':
			if( ! $showResource )
				continue;
			$flowTitles[] = $f[0];
			break;

		case 'location':
			if( ! $showLocation )
				continue;
			$flowTitles[] = $f[0];
			break;

		default:
			$flowTitles[] = $f[0];
		}
	}

$flowTable = array();

for( $i = 0; $i < $rowsCount; $i++ ){
	$flowTable[ $i ] = array();
	reset( $flowTitles );
	foreach( $flowTitles as $ft ){
		$cellView = '';
		$cellClass = '';
		if( ($ft == $NTS_CURRENT_REQUEST_WHAT) && ($i == $NTS_CURRENT_REQUEST_INDEX) ){
			$cellView = M('Selecting Now');
			$cellClass = 'selecting-now';
			}
		elseif( $NTS_CURRENT_REQUEST_WHAT ) {
			switch( $ft ){
				case 'time':
					if( $NTS_CURRENT_REQUEST[$i]['time'] ){
						$t = new ntsTime( $NTS_CURRENT_REQUEST[$i]['time'], $NTS_CURRENT_USER->getTimezone() );
						$cellView = $t->formatWeekday() . ', ' . $t->formatDate();
						$startTime = $t->formatTime();

						$duration = 0;
						$endTime = '';
						if( $NTS_VIEW['RESCHEDULE'] ){
							$duration = $NTS_VIEW['RESCHEDULE']->getProp('duration');
							}
						else {
							if( $NTS_CURRENT_REQUEST[$i]['service']->getProp('until_closed') ){
								if( $NTS_CURRENT_REQUEST[$i]['location'] && $NTS_CURRENT_REQUEST[$i]['resource'] ){
									$tm = new haTimeManager();
									$tm->setService( $NTS_CURRENT_REQUEST[$i]['service'] );
									$tm->setLocation( $NTS_CURRENT_REQUEST[$i]['location'] );
									$tm->setResource( $NTS_CURRENT_REQUEST[$i]['resource'] );

									$testTimes = $tm->getSelectableTimes( 
										$NTS_CURRENT_REQUEST[$i]['time'],
										$NTS_CURRENT_REQUEST[$i]['time'],
										$NTS_CURRENT_REQUEST[$i]['seats']
										);

									reset( $testTimes[ $NTS_CURRENT_REQUEST[$i]['time'] ] );
									foreach( $testTimes[ $NTS_CURRENT_REQUEST[$i]['time'] ] as $tt ){
										if( $tt[ $tm->SLT_INDX['duration'] ] > $duration )
											$duration = $tt[ $tm->SLT_INDX['duration'] ];
										}
									}
								}
							else {
								$duration = $NTS_CURRENT_REQUEST[$i]['service']->getProp('duration');
								}
							}

						$cellView .= '<br><b>' . $startTime;
						if( $duration ){
							$t->modify( '+' . $duration . ' seconds' );
							$endTime = $t->formatTime();
							$cellView .= ' - ' . $endTime . '</b>';
							}
						$cellView .= '</b>';
						}
					elseif( $NTS_CURRENT_REQUEST[$i]['cal'] ){
						$t = new ntsTime( 0, $NTS_CURRENT_USER->getTimezone() );
						$t->setDateDb( $NTS_CURRENT_REQUEST[$i]['cal'] );
						$cellView = $t->formatWeekday() . ', ' . $t->formatDate();
						}
					break;

				case 'service':
					if( $NTS_VIEW['RESCHEDULE'] ){
						$cellView = nl2br(ntsView::serviceView( $NTS_CURRENT_REQUEST[$i]['service'], $NTS_VIEW['RESCHEDULE']->getProp('seats'), $NTS_VIEW['RESCHEDULE']->getProp('duration'), $NTS_VIEW['RESCHEDULE']->getProp('until_closed'), false ) );
						}
					elseif( $NTS_CURRENT_REQUEST[$i]['service'] ){
						$cellView = nl2br(ntsView::serviceView( $NTS_CURRENT_REQUEST[$i]['service'], $NTS_CURRENT_REQUEST[$i]['seats'], $NTS_CURRENT_REQUEST[$i]['service']->getProp('duration'), $NTS_CURRENT_REQUEST[$i]['service']->getProp('until_closed') ) );
						$showBuiltInPrice = $NTS_VIEW['PACK'] ? false : true;
						if( ! $showBuiltInPrice ){
							$priceView = '';
						// don't show price if pack total is given
							if( $packValue[0] != 'price' ){
								if( $discountedPrices[$i] != $fullPrices[$i] ){
									$priceView = '<span style="text-decoration: line-through;">';
									}
								$priceView .= ntsView::servicePriceView( $NTS_CURRENT_REQUEST[$i]['service'], $NTS_CURRENT_REQUEST[$i]['seats'] ); 
								if( $discountedPrices[$i] != $fullPrices[$i] ){
									$priceView .= '</span> <b>' . ntsCurrency::formatServicePrice($discountedPrices[$i]) . '</b>';
									}
								if( strlen($priceView) ){
									$cellView .= "<br>" . M('Price') . ': ' . $priceView;
									}
								}
							}
						}
					break;

				case 'location':
					if( $NTS_CURRENT_REQUEST[$i]['location'] ){
						$cellView = ntsView::objectTitle( $NTS_CURRENT_REQUEST[$i]['location'] );
						}
					break;
				case 'resource':
					if( $NTS_CURRENT_REQUEST[$i]['resource'] ){
						$cellView = $NTS_CURRENT_REQUEST[$i]['resource']->getProp('title');
						}
					break;
				}
			}
		else {
			$cellView = 'nbsp;';
			}

		$flowTable[$i][] = array( $cellView, $cellClass );
		}
	}

reset( $flowTitles );
reset( $flowTable );
$flowCount = 0;
$colWidth = (int) ( 95 / count($flowTitles) );

$needCounter = (count($flowTable) > 1) ? 1 : 0;
?>

<div id="nts-appointment-list">
<table>

<?php if( $additionalHeader ) : ?>
	<tr>
	<th colspan="<?php echo (count($flowTitles) + $needCounter); ?>"><?php echo $additionalHeader; ?></th>
	</tr>
<?php endif; ?>

<tr>
<?php if( $needCounter ) : ?>
	<th width="5%">&nbsp;</th>
<?php endif; ?>
<?php foreach( $flowTitles as $ft ) : ?>
	<th width="<?php echo $colWidth; ?>%">
<?php	switch( $ft ){
		case 'time':
			echo M('Date and Time');
			break;
		case 'service':
			echo M('Service');
			break;
		case 'location':
			echo M('Location');
			break;
		case 'resource':
			echo M('Bookable Resource');
			break;
		}
?>
	</th>
<?php endforeach; ?>
</tr>

<?php foreach( $flowTable as $ft ) : ?>
<tr<?php if( $flowCount % 2 ){echo ' class="odd"';} ?>>
<?php if( $needCounter ) : ?>
	<td><?php echo ($flowCount+1); ?></td>
<?php endif; ?>
<?php 	foreach( $ft as $cellArray ) : ?>
	<td<?php if( $cellArray[1] ){echo ' class="' . $cellArray[1] . '"';} ?>><?php if( strlen($cellArray[0]) ){echo $cellArray[0];} else {echo '&nbsp;';} ; ?></td>
<?php 	endforeach; ?>
</tr>

<?php $flowCount++; ?>
<?php endforeach; ?>

<?php
if( $showTotalPrice ){
	if( $NTS_VIEW['PACK'] ){
		$enableDiscount = ( $totalDiscountedPrice < $totalFullPrice ) ? true : false;
		}
	else {
		if( ! strlen($totalFullPrice) )
			$showTotalPrice = false;
		}
	}
?>

<?php if( $showTotalPrice ) : ?>
	<tr>
	<th style="text-align: right;"><?php echo M('Total'); ?></td>
	<th colspan="<?php echo (count($flowTitles) ); ?>">
		<?php if( $enableDiscount ) :  ?>
			<span style="text-decoration: line-through;"><?php echo ntsCurrency::formatServicePrice($totalFullPrice); ?></span>
			<?php if( $NTS_VIEW['PACK'] && ($packType == 'price') && ($NTS_CURRENT_REQUEST[0]['seats'] > 1) ) : ?>
				<?php $mySeats = $NTS_CURRENT_REQUEST[0]['seats']; ?>
				<?php echo ntsCurrency::formatServicePrice($totalDiscountedPrice); ?> [<?php echo $mySeats; ?> x <?php echo ntsCurrency::formatServicePrice($totalDiscountedPrice/$mySeats); ?>]
			<?php else : ?>
				<?php echo ntsCurrency::formatServicePrice($totalDiscountedPrice); ?>
			<?php endif; ?>
		<?php else : ?>
			<?php echo ntsCurrency::formatServicePrice($totalDiscountedPrice); ?>
		<?php endif; ?>
	</th>
	</tr>
<?php endif; ?>

</table>

</div>
<?php //require( dirname(__FILE__) . '/flow2.php' ); ?>