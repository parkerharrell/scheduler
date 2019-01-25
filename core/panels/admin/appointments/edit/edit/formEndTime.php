<?php
$appId = $this->getValue('id');
$startTs = $this->getValue('starts_at');
$maxEndTs = $startTs + $this->getValue('max_duration');

$ts = $startTs + 60 * NTS_TIME_UNIT;
$tsOptions = array();

$t = new ntsTime( $startTs );
$startTime = $t->formatTime();
$t->modify( '+' . $this->getValue('duration') . ' seconds' );
$endTime = $t->formatTime();

while( $ts <= $maxEndTs ){
	$t = new ntsTime( $ts );
	$tsOptions[] = array( $ts, $t->formatTime() );
	$ts += 60 * NTS_TIME_UNIT;
	}
?>
<?php echo $startTime; ?> - 

<span id="nts-end-time-<?php echo $appId; ?>">
	<?php echo $endTime; ?>
	<?php if( ! $this->readonly ) : ?>
		<a href="#" id="nts-control-end-time-<?php echo $appId; ?>"><?php echo M('Other end time?'); ?></a>
	<?php endif; ?>
</span>

<?php if( ! $this->readonly ) : ?>
<span style="display: none;" id="nts-end-time-form-<?php echo $appId; ?>">
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'end_time',
		'options'	=> $tsOptions
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'endtime' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Update'); ?>">
<a href="#" id="nts-hide-form-<?php echo $appId; ?>"><?php echo M('Cancel'); ?></a>
</span>
<?php endif; ?>

<script type="text/javascript">
// ajax calls to change end time
var ntsPageLinksPrefix = 'nts-control-end-time-';
jQuery("a[id^=" + ntsPageLinksPrefix + "]").live("click", function() {
	var appId = this.id.substring( ntsPageLinksPrefix.length );

	var timeElId = '#nts-end-time-' + appId;
	var formElId = '#nts-end-time-form-' + appId;
	jQuery(timeElId).hide();
	jQuery(formElId).show();

	return false;
	});

var ntsHideFormPrefix = 'nts-hide-form-';
jQuery("a[id^=" + ntsHideFormPrefix + "]").live("click", function() {
	var appId = this.id.substring( ntsHideFormPrefix.length );

	var timeElId = '#nts-end-time-' + appId;
	var formElId = '#nts-end-time-form-' + appId;
	jQuery(formElId).hide();
	jQuery(timeElId).show();

	return false;
	});	
</script>