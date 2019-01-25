<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$confFlow = $conf->get('appointmentFlow');
reset( $confFlow );
$currentFlow = array();
$currentFlowSetting = array();
foreach( $confFlow as $f ){
	if( $f[0] == 'seats' )
		continue;
	$currentFlow[] = $f;
	$currentFlowSetting[] = $f[0];
	}
reset( $currentFlow );

$possibleFlows = array(
	'service'	=> M('Service'),
	'time'		=> M('Date and Time'),
	'location'	=> M('Location'),
	'resource'	=> M('Bookable Resource'),
	);
	
$assignOptions = array(
	'service'	=> array(),
	'time'		=> array(),
	'location'	=> array( 
		array( 'manual',		M('Let Customer Select') ),
		array( 'manualplus',	M('Let Customer Select With Auto Assign Option') ),
		array( 'auto',			M('Automatically Select Any Available') ),
		),
	'resource'	=> array( 
		array( 'manual',		M('Let Customer Select') ),
		array( 'manualplus',	M('Let Customer Select With Auto Assign Option') ),
		array( 'auto',			M('Automatically Select Any Available') ),
		),
	);
$count = 0;
?>
<table>
<?php foreach( $currentFlow as $f ) : ?>
	<?php $count++; ?>
	<tr id="nts-flow-option-<?php echo $count; ?>" style="height: 2em;">
	<th><?php echo $possibleFlows[$f[0]]; ?></th>
	<td>
	<?php
	if( $assignOptions[$f[0]] ){
		echo $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'assign-' . $f[0],
				'options'	=> $assignOptions[$f[0]],
				)
			);
		}
	?>
	</td>
	<td>
		<a class="ok" href="#" id="nts-move-up-<?php echo $f[0]; ?>"><?php echo M('Up'); ?></a>
		<a class="ok" href="#" id="nts-move-down-<?php echo $f[0]; ?>"><?php echo M('Down'); ?></a>
	</td>
	</tr>
<?php endforeach; ?>

<tr>
<td></td>

<td colspan="2">
<?php
echo $this->makeInput (
/* type */
	'hidden',
/* attributes */
	array(
		'id'	=> 'current-flow-setting',
		'value'	=> join( $currentFlowSetting, '|' )
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Save'); ?>">
</td>
</tr>
</table>

<script language="javascript">
ntsUpPrefix = "nts-move-up-";
ntsDownPrefix = "nts-move-down-";
ntsRowPrefix = "nts-flow-option-";

var ntsCurrentRows = new Array();
var ntsCurrentHandles = new Array();
<?php 
reset( $currentFlow );
$count = 0;
foreach( $currentFlow as $f ) :
$count++;
?>
ntsCurrentRows["<?php echo $f[0]; ?>"] = <?php echo $count; ?>;
ntsCurrentHandles[<?php echo $count; ?>] = "<?php echo $f[0]; ?>";
<?php endforeach; ?>

/* move up */
jQuery("a[id^=" + ntsUpPrefix + "]").live("click", function() {
	var srcRowHandle = this.id.substring(ntsUpPrefix.length);
	var srcRowId = parseInt( ntsCurrentRows[srcRowHandle] );
	if( srcRowId > 1 ){
		var trgRowId = parseInt( srcRowId - 1 );
		var trgRowHandle = ntsCurrentHandles[ trgRowId ]
		var srcRowHtmlId = ntsRowPrefix + srcRowId;
		var trgRowHtmlId = ntsRowPrefix + trgRowId; 

		var tmp = jQuery('#' + srcRowHtmlId).html();
		jQuery('#' + srcRowHtmlId).html( jQuery('#' + trgRowHtmlId).html() );
		jQuery('#' + trgRowHtmlId).html( tmp );

		ntsCurrentRows[ srcRowHandle ] = trgRowId;
		ntsCurrentRows[ trgRowHandle ] = srcRowId;
		ntsCurrentHandles[ trgRowId ] = srcRowHandle;
		ntsCurrentHandles[ srcRowId ] = trgRowHandle;

		document.forms["<?php echo $this->getName(); ?>"]["current-flow-setting"].value = ntsCurrentHandles.join('|');
		}
	return true;
	});

jQuery("a[id^=" + ntsDownPrefix + "]").live("click", function() {
	var srcRowHandle = this.id.substring(ntsDownPrefix.length);
	var srcRowId = parseInt( ntsCurrentRows[srcRowHandle] );
	if( srcRowId < 4 ){
		var trgRowId = parseInt( srcRowId + 1 );
		var trgRowHandle = ntsCurrentHandles[ trgRowId ]
		var srcRowHtmlId = ntsRowPrefix + srcRowId;
		var trgRowHtmlId = ntsRowPrefix + trgRowId; 
		
		var tmp = jQuery('#' + srcRowHtmlId).html();
		jQuery('#' + srcRowHtmlId).html( jQuery('#' + trgRowHtmlId).html() );
		jQuery('#' + trgRowHtmlId).html( tmp );

		ntsCurrentRows[ srcRowHandle ] = trgRowId;
		ntsCurrentRows[ trgRowHandle ] = srcRowId;
		ntsCurrentHandles[ trgRowId ] = srcRowHandle;
		ntsCurrentHandles[ srcRowId ] = trgRowHandle;

		document.forms["<?php echo $this->getName(); ?>"]["current-flow-setting"].value = ntsCurrentHandles.join('|');
		}
	return true;
	});
</script>
