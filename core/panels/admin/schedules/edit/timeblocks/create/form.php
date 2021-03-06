<?php
$ntsdb =& dbWrapper::getInstance();
$appliedOn = $this->getValue( 'applied_on' );
$scheduleId = $this->getValue( 'schedule_id' );
$startsAt = 0;
$endsAt = 0;
?>
<?php
echo $this->makeValidator (
	'checkIfBlockOverlaps',
	array(
		'directCode'	=> dirname(__FILE__) . '/validate/checkIfBlockOverlaps.php',
		'error'		=> 'There already exists a time block that overlaps with this one',
		'params'	=> array(
			'schedule_id'	=> $scheduleId,
			'applied_on'	=> $appliedOn,
			),
		)
	);
?>
<table>
<tr>
	<th><?php echo M('Applied On'); ?></th>
	<td>
	<?php
	$text_ShortDays = array( M('Sunday'), M('Monday'), M('Tuesday'), M('Wednesday'), M('Thursday'), M('Friday'), M('Saturday') );
	$text_AppliedOn = $text_ShortDays[$appliedOn];
	?>
	<b><?php echo $text_AppliedOn; ?></b>
	</td>
</tr>

<tr>
	<th><?php echo M('From'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'	=> 'starts_at',
			),
	/* validators */
		array(
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('To'); ?></th>
	<td>
	<?php
	echo $this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'		=> 'ends_at',
			),
	/* validators */
		array(
			array(
				'code'		=> 'greaterThan.php', 
				'error'		=> "This time should be after the from time",
				'params'	=> array(
					'compareWithField' => 'starts_at',
					),
				),
			)
		);
	?>
	</td>
</tr>

<tr>
	<th><?php echo M('Selectable Times'); ?></th>
	<td>
<?php
$selectableStyleOptions = array(
	array( 'every', M('Periodic') ),
	array( 'fixed', M('Fixed') ),
	);
?>
	<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'selectable_style',
			'options'	=> $selectableStyleOptions,
			)
		);
	?>
	</td>
</tr>
<tr>
	<th>&nbsp;</th>
	<td>

<div id="nts-SelectableEvery">
	<b><?php echo M('Every'); ?></b>
	<?php
	$options = array( 3, 5, 6, 9, 10, 12, 15, 18, 20, 21, 24, 25, 27, 30, 40, 45, 50, 60, 75, 90, 2*60, 2.5*60, 3*60, 4*60, 5*60, 6*60, 8*60, 9*60, 12*60, 18*60, 24*60 );
	$selectabeOptions = array();
	foreach( $options as $o ){
		if( $o % NTS_TIME_UNIT )
			continue;
		$selectabeOptions[] = array( 60 * $o, $o );
		}

	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'selectable_every',
			'options'	=> $selectabeOptions,
			)
		);
	?>
	<?php echo M('Minutes'); ?>
</div>

<div id="nts-SelectableFixed">
<?php
echo $this->makeInput (
/* type */
	'date/fixedSelectableTime',
/* attributes */
	array(
		'id'		=> 'selectable_fixed',
		'params'	=> array(
			'min'	=> $startsAt,
			'max'	=> $endsAt,
			),
		),
	/* validators */
		array(
/*
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Please choose at least one option'),
				)
*/
			)
	);
?>
</div>
	
	</td>
</tr>

<tr>
	<th>&nbsp;</th>
	<td>
	<?php echo $this->makePostParams('-current-', 'create', array('applied_on' => $appliedOn) ); ?>
	<INPUT TYPE="submit" VALUE="<?php echo M('Create'); ?>">
	</td>
</tr>
</table>

<script language="JavaScript">
function ntsProcessSelectStyle(){
	var currentStyle = jQuery("#selectable_style").val();
	switch( currentStyle ){
		case 'every':
			jQuery("#nts-SelectableEvery").show();
			jQuery("#nts-SelectableFixed").hide();
			break;
		case 'fixed':
			jQuery("#nts-SelectableEvery").hide();
			jQuery("#nts-SelectableFixed").show();
			var newMin = 60 * 60 * jQuery("#starts_at_qty_hour").val() + 60 * jQuery("#starts_at_qty_min").val();
			dynaListselectable_fixed.setMin( newMin )
			var newMax = 60 * 60 * jQuery("#ends_at_qty_hour").val() + 60 * jQuery("#ends_at_qty_min").val();
			dynaListselectable_fixed.setMax( newMax )
			dynaListselectable_fixed.walkOptions();
			break;
		}
	}

ntsProcessSelectStyle();
jQuery("#selectable_style").change( ntsProcessSelectStyle );

jQuery("#starts_at_qty_hour").change( function(){
	if( jQuery("#selectable_style").val() == 'fixed' ){
		var newMin = 60 * 60 * jQuery("#starts_at_qty_hour").val() + 60 * jQuery("#starts_at_qty_min").val();
		dynaListselectable_fixed.setMin( newMin )
		dynaListselectable_fixed.walkOptions();
		dynaListselectable_fixed.toggleNewForm( true );
		}
	});
jQuery("#starts_at_qty_min").change( function(){
	if( jQuery("#selectable_style").val() == 'fixed' ){
		var newMin = 60 * 60 * jQuery("#starts_at_qty_hour").val() + 60 * jQuery("#starts_at_qty_min").val();
		dynaListselectable_fixed.setMin( newMin )
		dynaListselectable_fixed.walkOptions();
		dynaListselectable_fixed.toggleNewForm( true );
		}
	});
jQuery("#ends_at_qty_hour").change( function(){
	if( jQuery("#selectable_style").val() == 'fixed' ){
		var newMax = 60 * 60 * jQuery("#ends_at_qty_hour").val() + 60 * jQuery("#ends_at_qty_min").val();
		dynaListselectable_fixed.setMax( newMax )
		dynaListselectable_fixed.walkOptions();
		dynaListselectable_fixed.toggleNewForm( true );
		}
	});
jQuery("#ends_at_qty_min").change( function(){
	if( jQuery("#selectable_style").val() == 'fixed' ){
		var newMax = 60 * 60 * jQuery("#ends_at_qty_hour").val() + 60 * jQuery("#ends_at_qty_min").val();
		dynaListselectable_fixed.setMax( newMax )
		dynaListselectable_fixed.walkOptions();
		dynaListselectable_fixed.toggleNewForm( true );
		}
	});
</script>