<div id="nts-ajax-holder" style="display: none;"></div>
<?php
$maxAppInOnePopup = 1000;
$resourceCount = count( $NTS_VIEW['resources'] );
$resources = $NTS_VIEW['resources'];
$t = $NTS_VIEW['t'];

$viewSplit = ( $NTS_VIEW['viewSplit'] == 'split' ) ? true : false;

$AJAX_PANEL_PREFIX = 'admin/ajax';

/* prefill resource titles */
$RESOURCE_TITLES = array();
$RESOURCE_IDS = array();
for( $j = 0; $j < $resourceCount; $j++ ){
	$RESOURCE_IDS[ $j ] = $resources[$j]->getId();
	$RESOURCE_TITLES[ $j ] = ntsView::objectTitle( $resources[$j] );
	}
?>
<script language="JavaScript">
function ntsShowAppointment( id ){
	var src = "<?php echo ntsLink::makeLink('-current-/../edit', '', array('viewMode' => 'inline')); ?>" + "&_id=" + id;
	$.modal('<iframe src="' + src + '" height="500" width="830" style="border: 0;">',
		{
		closeHTML:"",
		containerCss:{
			height:500,
			padding:0,
			width:830
			},
		overlayClose:true
		}
	);
	}
</script>
<style>
#simplemodal-overlay {background-color:#000;}
#simplemodal-container {background-color:#ffffff; border:8px solid #444; padding:12px;}
</style>
<!-- APPOINTMENT REQUEST FLOW -->
<?php if( $NTS_VIEW['RESCHEDULE'] ) : ?>
	<?php require( dirname(__FILE__) . '/../create/common/flow.php' ); ?>
<?php endif; ?>

<div id="haDayHours2">

<table class="config">
<tr>
<td>
<div style="width: auto;"><b><?php echo M('Legend'); ?></b></div>
<div class="selectable"><a href="javascript:void(0)"><?php echo M('Selectable Times'); ?></a></div>
<div class="working"><?php echo M('Working Time'); ?></div>
<div class="timeoff"><?php echo M('Timeoff'); ?></div>
<div class="partbook"><?php echo M('Partially Booked'); ?></div>
<div class="fullbook"><?php echo M('Fully Booked'); ?></div>
</td>
</tr>

<tr>
<td><?php $NTS_VIEW['selectorForm']->display(); ?></td>
</tr>
</table>

<?php  require( dirname(__FILE__) . '/_index_functions.php' ); ?>
<?php
switch( $NTS_VIEW['viewPeriod'] ){
	case 'month':
		require( dirname(__FILE__) . '/_index_MonthView.php' );
		break;
	case 'day':
		require( dirname(__FILE__) . '/_index_DayView.php' );
		break;
	case 'week':
		require( dirname(__FILE__) . '/_index_WeekView.php' );
		break;
	}
?>
</div>

<script language="javascript">
var ntsAppointmentPrefix = 'nts-appointment-';

/* show appointment popup */
jQuery("a[id^=" + ntsAppointmentPrefix + "]").live("click", function() {
	var appId = this.id.substring( ntsAppointmentPrefix.length );
	ntsShowAppointment( appId );
	return false;
	});

/* ajax links */
jQuery("a[class^=nts-ajax-link]").live("click", function(e) {
	var url = jQuery(this).attr("href");
	url += '&viewMode=ajax';
	jQuery('#nts-ajax-holder').show();

	var classString = jQuery(this).attr("class");

	if( jQuery(this).attr("class").indexOf('nts-ajax-keep-position') == -1 ){
	// adjust position
		var pos = jQuery(this).offset();
		jQuery('#nts-ajax-holder').css( {"left": pos.left + 0 + "px", "top": pos.top + 0 + "px"} );
		}
	jQuery('#nts-ajax-holder').html( "<?php echo M('Loading'); ?>" );

	jQuery('#nts-ajax-holder').load( url );
	return false;
	});
	
jQuery("html").click(function(){
	jQuery("#nts-ajax-holder").hide();
	});

</script>