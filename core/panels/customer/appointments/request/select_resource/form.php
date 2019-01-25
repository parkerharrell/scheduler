<?php
global $NTS_VIEW;
$entries = $NTS_VIEW['entries'];

$objectOptions = array();
if( $NTS_VIEW['selectionMode'] == 'manualplus' )
	$objectOptions[] = array( 'auto', ' - ' . M("Don't have a particular preference") . ' - '  );
foreach( $entries as $l ){
	$objectOptions[] = array( $l->getId(), ntsView::objectTitle($l) );
	}
?>
<p>
<?php
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'		=> 'id',
		'options'	=> $objectOptions,
		'attr'		=> array(
			),
		)
	);
?>
<?php echo $this->makePostParams('-current-', 'select' ); ?>
<INPUT TYPE="submit" VALUE="<?php echo M('Continue'); ?>">