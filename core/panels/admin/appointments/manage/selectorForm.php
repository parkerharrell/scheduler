<?php
global $NTS_VIEW;
?>
<?php if( count($NTS_VIEW['allLocations']) > 1 ) : ?>
<?php
$options = array();
//$options[] = array( 0, ' - ' . M('All') . ' - ' );
reset( $NTS_VIEW['allLocations'] );
foreach( $NTS_VIEW['allLocations'] as $o ){
	$options[] = array( $o->getId(), $o->getProp('title') );
	}
?>
<b><?php echo M('Location'); ?></b> 
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'location',
		'options'	=> $options,
		)
	);
?>
&nbsp;&nbsp;
<?php endif; ?>

<?php if( count($NTS_VIEW['allResources']) > 1 ) : ?>
<?php
$options = array();
$options[] = array( 0, ' - ' . M('All') . ' - ' );
reset( $NTS_VIEW['allResources'] );
foreach( $NTS_VIEW['allResources'] as $o ){
	$options[] = array( $o->getId(), $o->getProp('title') );
	}
?>
<b><?php echo M('Bookable Resource'); ?></b> 
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'resource',
		'options'	=> $options,
		)
	);
?>
&nbsp;&nbsp;

<?php if( count($NTS_VIEW['resources']) > 1 ) : ?>
<?php
$options = array(
	array( 'together', M('Together') ),
	array( 'split', M('Split') ),
	);
?>
<b><?php echo M('View'); ?></b> 
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'viewSplit',
		'options'	=> $options,
		)
	);
?>
&nbsp;&nbsp;
<?php endif; ?>
<?php endif; ?>
<?php
$options = array(
	array( 'month', M('Month') ),
	array( 'week', M('Week') ),
	array( 'day', M('Day') ),
	);
?>
<b><?php echo M('Period'); ?></b> 
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'viewPeriod',
		'options'	=> $options,
		)
	);
?>

<script language="javascript">
jQuery("#service").change(function() {
	var newParams = new Array();
	newParams[ 'service' ] = this.value;
	ntsUpdateCurrentLocation( newParams );
	});
jQuery("#location").change(function() {
	var newParams = new Array();
	newParams[ 'location' ] = this.value;
	ntsUpdateCurrentLocation( newParams );
	});
jQuery("#resource").change(function() {
	var newParams = new Array();
	newParams[ 'resource' ] = this.value;
	ntsUpdateCurrentLocation( newParams );
	});
jQuery("#viewPeriod").change(function() {
	var newParams = new Array();
	newParams[ 'viewPeriod' ] = this.value;
	ntsUpdateCurrentLocation( newParams );
	});
jQuery("#viewSplit").change(function() {
	var newParams = new Array();
	newParams[ 'viewSplit' ] = this.value;
	ntsUpdateCurrentLocation( newParams );
	});
</script>