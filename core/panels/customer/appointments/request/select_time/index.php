<?php
global $NTS_CURRENT_REQUEST, $NTS_CURRENT_REQUEST_INDEX, $NTS_CURRENT_USER;

$ntsConf =& ntsConf::getInstance();
$showDays = $ntsConf->get('daysToShowCustomer');
$link = new ntsLink();

include_once( NTS_BASE_DIR . '/lib/datetime/ntsCalendar.php' );
$times = $NTS_VIEW['times'];
$dates = $NTS_VIEW['dates'];
$t = $NTS_VIEW['t'];
$t = new ntsTime;
$t->setTimezone( $NTS_CURRENT_USER->getTimezone() );

$cal = $NTS_VIEW['cal'];
list( $calYear, $calMonth, $calDay ) = ntsTime::splitDate( $cal );

/* PREPARE LINK FOR FASTER EXECUTION */
$link->prepare('-current-', 'select' );

$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
?>

<!-- APPOINTMENT REQUEST FLOW -->
<?php require( dirname(__FILE__) . '/../common/flow.php' ); ?>
<table>
<tr>
<td style="padding: 0 0.5em; vertical-align: top;">
<?php require( dirname(__FILE__) . '/calendar.php' ); ?>
</td>

<td style="padding: 0 0 0 2em; vertical-align: top;">
<?php
$shownDays = 0;
$l = 0;
?>
<p>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<?php
	$formTimezoneParams = array(
		'tz'	=> $NTS_CURRENT_USER->getTimezone(),
		);
	$formTimezone =& $ff->makeForm( dirname(__FILE__) . '/formTimezone', $formTimezoneParams );
	$formTimezone->display();
	?>
<?php elseif( NTS_ENABLE_TIMEZONES < 0 ) : ?>
<?php else : ?>
	<?php echo M('Times shown in [b]{TIME_ZONE}[/b] time zone', array('TIME_ZONE' => ntsTime::timezoneTitle($NTS_CURRENT_USER->getTimezone()) )); ?>
<?php endif; ?>

<?php
$showTimes = array();
reset( $times );
foreach( $times as $ts ){
	$t->setTimestamp( $ts );
	$thisDate = $t->formatDate_Db();
	if( ! isset($showTimes[$thisDate]) )
		$showTimes[$thisDate] = array();
	$showTimes[$thisDate][] = $ts;	
	}
?>
<?php foreach( $showTimes as $dayTimes ) : ?>
	<?php require( dirname(__FILE__) . '/time.php' ); ?>
<?php endforeach; ?>
</td>
</tr>
</table>